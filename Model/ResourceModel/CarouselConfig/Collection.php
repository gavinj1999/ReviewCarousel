<?php
namespace Vendor\ReviewCarousel\Model\ResourceModel\CarouselConfig;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'config_id';

    protected function _construct()
    {
        $this->_init(\Vendor\ReviewCarousel\Model\CarouselConfig::class, \Vendor\ReviewCarousel\Model\ResourceModel\CarouselConfig::class);
    }
}
