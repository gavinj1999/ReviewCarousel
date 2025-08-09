<?php
namespace Vendor\ReviewCarousel\Model\ResourceModel\Carousel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(\Vendor\ReviewCarousel\Model\Carousel::class, \Vendor\ReviewCarousel\Model\ResourceModel\Carousel::class);
    }
}
