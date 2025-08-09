<?php
namespace Vendor\ReviewCarousel\Block\Adminhtml\Carousel;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Psr\Log\LoggerInterface;

class Upload extends Template
{
    protected $_logger;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->_logger = $logger;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        try {
            $this->setTemplate('Vendor_ReviewCarousel::carousel/upload.phtml');
            $this->_logger->debug('Upload block constructed successfully');
        } catch (\Exception $e) {
            $this->_logger->critical('Error in Upload block _construct: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    public function getCurrentFilePath()
    {
        try {
            $config = $this->_objectManager->create(\Vendor\ReviewCarousel\Model\CarouselConfig::class);
            $config->load(1);
            $filePath = $config->getFilePath();
            $this->_logger->debug('Retrieved current file path: ' . ($filePath ?: 'none'));
            return $filePath;
        } catch (\Exception $e) {
            $this->_logger->critical('Error retrieving current file path: ' . $e->getMessage());
            return null;
        }
    }
}
