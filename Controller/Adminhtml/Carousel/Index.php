<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    protected $resultPageFactory;
    protected $logger;

    public function __construct(
        Context $context,
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
            $this->logger->debug('Loading Review Carousels index page');
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Vendor_ReviewCarousel::carousel');
            $resultPage->getConfig()->getTitle()->prepend(__('Review Carousels'));
            $this->logger->debug('Index page layout loaded, blocks: ' . implode(', ', array_keys($resultPage->getLayout()->getAllBlocks())));
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->critical('Error loading index page: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while loading the page: %1', $e->getMessage()));
            return $this->resultRedirectFactory->create()->setPath('*/');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel');
    }
}
