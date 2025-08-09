<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class ManageCsv extends Action
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
            $this->logger->debug('Loading Manage CSV page');
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Vendor_ReviewCarousel::carousel_managecsv');
            $resultPage->getConfig()->getTitle()->prepend(__('Manage Review CSV'));
            $this->logger->debug('Manage CSV page layout loaded, blocks: ' . implode(', ', array_keys($resultPage->getLayout()->getAllBlocks())));
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->critical('Error loading manage CSV page: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while loading the manage CSV page: %1', $e->getMessage()));
            return $this->resultRedirectFactory->create()->setPath('reviewcarousel/carousel');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel_managecsv');
    }
}
