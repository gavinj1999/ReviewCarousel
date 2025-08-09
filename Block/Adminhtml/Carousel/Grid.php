<?php
namespace Vendor\ReviewCarousel\Block\Adminhtml\Carousel;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Vendor\ReviewCarousel\Model\ResourceModel\Carousel\CollectionFactory;
use Psr\Log\LoggerInterface;

class Grid extends Extended
{
    protected $collectionFactory;
    protected $logger;

    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        parent::__construct($context, $backendHelper, $data);
        $this->logger->debug('Grid block initialized with CollectionFactory: ' . get_class($collectionFactory));
    }

    protected function _construct()
    {
        parent::_construct();
        try {
            $this->setId('carouselGrid');
            $this->setDefaultSort('id');
            $this->setDefaultDir('ASC');
            $this->logger->debug('Grid block constructed successfully');
        } catch (\Exception $e) {
            $this->logger->critical('Error in Grid block _construct: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    protected function _prepareCollection()
    {
        try {
            $collection = $this->collectionFactory->create();
            $this->logger->debug('Grid collection created, count: ' . count($collection));
            $this->setCollection($collection);
            $this->logger->debug('Grid collection prepared: ' . count($collection) . ' items');
            return parent::_prepareCollection();
        } catch (\Exception $e) {
            $this->logger->critical('Error preparing grid collection: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    protected function _prepareColumns()
    {
        try {
            $this->addColumn('id', ['header' => __('ID'), 'index' => 'id']);
            $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
            $this->addColumn('default_ratings', ['header' => __('Default Ratings'), 'index' => 'default_ratings']);
            $this->addColumn('exclude_no_text', ['header' => __('Exclude No Text'), 'index' => 'exclude_no_text', 'type' => 'options', 'options' => [0 => __('No'), 1 => __('Yes')]]);
            $this->addColumn('default_sort', ['header' => __('Default Sort'), 'index' => 'default_sort']);
            $this->addColumn('bg_color', ['header' => __('Background Color'), 'index' => 'bg_color']);
            $this->addColumn('text_color', ['header' => __('Text Color'), 'index' => 'text_color']);
            $this->addColumn('star_color', ['header' => __('Star Color'), 'index' => 'star_color']);
            $this->addColumn('featured_bg_color', ['header' => __('Featured Background Color'), 'index' => 'featured_bg_color']);
            $this->addColumn('star_size', ['header' => __('Star Size'), 'index' => 'star_size']);
            $this->addColumn('font_size', ['header' => __('Font Size'), 'index' => 'font_size']);
            $this->addColumn('font_family', ['header' => __('Font Family'), 'index' => 'font_family']);
            $this->addColumn('featured_review_index', ['header' => __('Featured Review Index'), 'index' => 'featured_review_index']);
            $this->addColumn('action', [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    ['caption' => __('Edit'), 'url' => ['base' => '*/*/edit'], 'field' => 'id'],
                    ['caption' => __('Delete'), 'url' => ['base' => '*/*/delete'], 'field' => 'id', 'confirm' => __('Are you sure you want to delete this carousel?')]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]);
            $this->logger->debug('Grid columns prepared successfully');
            return parent::_prepareColumns();
        } catch (\Exception $e) {
            $this->logger->critical('Error preparing grid columns: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    public function getRowUrl($row)
    {
        try {
            $url = $this->getUrl('*/*/edit', ['id' => $row->getId()]);
            $this->logger->debug('Row URL generated for ID ' . $row->getId() . ': ' . $url);
            return $url;
        } catch (\Exception $e) {
            $this->logger->critical('Error generating row URL: ' . $e->getMessage());
            return '';
        }
    }
}
