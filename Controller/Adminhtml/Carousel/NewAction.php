<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Psr\Log\LoggerInterface;

class NewAction extends Action
{
    protected $resultForwardFactory;
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ForwardFactory $resultForwardFactory,
        LoggerInterface $logger
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->logger->debug('Forwarding to Edit controller from NewAction for route: ' . $this->getRequest()->getFullActionName());
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('edit');
            $this->logger->debug('Forwarded to Edit controller');
            return $resultForward;
        } catch (\Exception $e) {
            $this->logger->critical('Error in NewAction controller: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while loading the new carousel form: %1', $e->getMessage()));
            return $this->resultRedirectFactory->create()->setPath('reviewcarousel/carousel');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel');
    }
}
