<?php
namespace Vendor\ReviewCarousel\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CarouselConfig extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('vendor_review_carousel_config', 'config_id');
    }
}
