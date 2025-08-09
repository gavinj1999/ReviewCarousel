<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Upload extends Action
{
    protected $resultPageFactory;
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $this->logger->debug('Loading Upload CSV page');
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Vendor_ReviewCarousel::carousel_upload');
            $resultPage->getConfig()->getTitle()->prepend(__('Upload Review CSV'));
            $this->logger->debug('Upload page layout loaded, blocks: ' . implode(', ', array_keys($resultPage->getLayout()->getAllBlocks())));
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->critical('Error loading upload page: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while loading the upload page: %1', $e->getMessage()));
            return $this->resultRedirectFactory->create()->setPath('reviewcarousel/carousel');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel_upload');
    }
}
