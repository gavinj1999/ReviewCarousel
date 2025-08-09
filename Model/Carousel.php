<?php
namespace Vendor\ReviewCarousel\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Carousel extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'vendor_review_carousel';

    protected function _construct()
    {
        $this->_init(\Vendor\ReviewCarousel\Model\ResourceModel\Carousel::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }
}
