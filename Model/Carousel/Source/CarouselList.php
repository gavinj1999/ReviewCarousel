<?php
namespace Vendor\ReviewCarousel\Model\Carousel\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Vendor\ReviewCarousel\Model\ResourceModel\Carousel\CollectionFactory;

class CarouselList implements OptionSourceInterface
{
    protected $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->collectionFactory->create();
        foreach ($collection as $carousel) {
            $options[] = [
                'value' => $carousel->getId(),
                'label' => $carousel->getName()
            ];
        }
        return $options;
    }
}
