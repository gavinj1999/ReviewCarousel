<?php
namespace Vendor\ReviewCarousel\Block\Adminhtml\Carousel;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class Edit extends Template
{
    protected $_coreRegistry;
    protected $_logger;

    public function __construct(
        Context $context,
        Registry $registry,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_logger = $logger;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        try {
            $this->setTemplate('Vendor_ReviewCarousel::carousel/edit.phtml');
            $this->_logger->debug('Edit block constructed successfully');
        } catch (\Exception $e) {
            $this->_logger->critical('Error in Edit block _construct: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    public function getCarousel()
    {
        try {
            $carousel = $this->_coreRegistry->registry('review_carousel');
            $this->_logger->debug('Retrieved carousel from registry: ' . ($carousel ? json_encode($carousel->getData()) : 'null'));
            return $carousel ?: new \Magento\Framework\DataObject();
        } catch (\Exception $e) {
            $this->_logger->critical('Error retrieving carousel from registry: ' . $e->getMessage());
            return new \Magento\Framework\DataObject();
        }
    }
}
