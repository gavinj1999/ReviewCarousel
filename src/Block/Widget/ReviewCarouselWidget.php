<?php
namespace Vendor\ReviewCarousel\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Vendor\ReviewCarousel\Model\Carousel;
use Vendor\ReviewCarousel\Model\ResourceModel\Carousel\CollectionFactory as CarouselCollectionFactory;
use Psr\Log\LoggerInterface;

class ReviewCarouselWidget extends Template implements BlockInterface
{
    protected $logger;
    protected $carouselCollectionFactory;
    protected $carousel;

    public function __construct(
        Template\Context $context,
        LoggerInterface $logger,
        CarouselCollectionFactory $carouselCollectionFactory,
        Carousel $carousel,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->carouselCollectionFactory = $carouselCollectionFactory;
        $this->carousel = $carousel;
        parent::__construct($context, $data);
        try {
            $this->setTemplate('Vendor_ReviewCarousel::widget/carousel.phtml');
            $this->logger->debug('Initialized ReviewCarouselWidget with template: Vendor_ReviewCarousel::widget/carousel.phtml');
        } catch (\Exception $e) {
            $this->logger->critical('Error in ReviewCarouselWidget __construct: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCarouselData()
    {
        try {
            $carouselId = $this->getData('carousel_id');
            $this->logger->debug('Attempting to load carousel data for ID: ' . ($carouselId ?: 'none'));
            if (!$carouselId) {
                $this->logger->warning('No carousel_id provided, attempting to load first available carousel');
                $collection = $this->carouselCollectionFactory->create();
                $carousel = $collection->getFirstItem();
                if (!$carousel->getId()) {
                    $this->logger->warning('No carousels found in vendor_review_carousel table');
                    return null;
                }
                $carouselId = $carousel->getId();
                $this->logger->debug('Using fallback carousel ID: ' . $carouselId);
            }

            $this->carousel->load($carouselId);
            if (!$this->carousel->getId()) {
                $this->logger->warning('Carousel not found for ID: ' . $carouselId);
                return null;
            }

            $this->logger->debug('Carousel loaded successfully: ' . json_encode($this->carousel->getData()));
            return $this->carousel;
        } catch (\Exception $e) {
            $this->logger->critical('Error loading carousel data: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    public function getReviews()
    {
        try {
            $carousel = $this->getCarouselData();
            if (!$carousel) {
                $this->logger->warning('No carousel data available, cannot load reviews');
                return [];
            }

            $connection = $this->getResourceConnection()->getConnection();
            $tableName = $connection->getTableName('vendor_review_carousel_reviews');
            $this->logger->debug('Querying table: ' . $tableName);
            $select = $connection->select()->from($tableName);

            // Apply exclude_no_text filter
            if ($carousel->getExcludeNoText()) {
                $select->where('review_text IS NOT NULL AND review_text != ?', '');
                $this->logger->debug('Applying exclude_no_text filter');
            } else {
                $this->logger->debug('No exclude_no_text filter applied');
            }

            // Apply default_ratings filter
            $defaultRatings = $carousel->getDefaultRatings();
            if ($defaultRatings && is_numeric($defaultRatings)) {
                $select->where('rating >= ?', (float)$defaultRatings);
                $this->logger->debug('Applying default_ratings filter: rating >= ' . $defaultRatings);
            }

            // Apply sorting
            $sortField = 'rating';
            $sortDir = 'DESC';
            if ($carousel->getDefaultSort() === 'date_asc') {
                $sortField = 'published_at';
                $sortDir = 'ASC';
            } elseif ($carousel->getDefaultSort() === 'rating_asc') {
                $sortField = 'rating';
                $sortDir = 'ASC';
            }
            $select->order($sortField . ' ' . $sortDir);
            $this->logger->debug('Executing query: ' . $select->__toString());
            $reviews = $connection->fetchAll($select);
            $this->logger->debug('Loaded ' . count($reviews) . ' reviews from database: ' . json_encode(array_slice($reviews, 0, 5)));
            return $reviews;
        } catch (\Exception $e) {
            $this->logger->critical('Error loading reviews: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function getStarHtml($rating)
    {
        try {
            $rating = (float)$rating;
            $this->logger->debug('Generating star HTML for rating: ' . $rating);
            $fullStars = floor($rating);
            $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
            $emptyStars = 5 - $fullStars - $halfStar;
            $html = '';
            for ($i = 0; $i < $fullStars; $i++) {
                $html .= '<span class="review-star full"></span>';
            }
            if ($halfStar) {
                $html .= '<span class="review-star half"></span>';
            }
            for ($i = 0; $i < $emptyStars; $i++) {
                $html .= '<span class="review-star empty"></span>';
            }
            return $html;
        } catch (\Exception $e) {
            $this->logger->critical('Error generating star HTML: ' . $e->getMessage());
            return '';
        }
    }

    protected function getResourceConnection()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\ResourceConnection::class);
    }
}
