<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Vendor\ReviewCarousel\Model\Carousel;
use Psr\Log\LoggerInterface;

class Delete extends Action
{
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->logger->debug('Deleting carousel with ID: ' . $id);
                $carousel = $this->_objectManager->create(\Vendor\ReviewCarousel\Model\Carousel::class);
                $carousel->load($id);
                if ($carousel->getId()) {
                    $carousel->delete();
                    $this->logger->debug('Carousel deleted successfully: ID ' . $id);
                    $this->messageManager->addSuccessMessage(__('The carousel has been deleted.'));
                    return $resultRedirect->setPath('reviewcarousel/carousel');
                }
            } catch (\Exception $e) {
                $this->logger->critical('Error deleting carousel: ' . $e->getMessage());
                $this->messageManager->addErrorMessage(__('An error occurred while deleting the carousel: %1', $e->getMessage()));
                return $resultRedirect->setPath('reviewcarousel/carousel/edit', ['id' => $id]);
            }
        }
        $this->logger->warning('No carousel ID provided for deletion');
        $this->messageManager->addErrorMessage(__('We can\'t find a carousel to delete.'));
        return $resultRedirect->setPath('reviewcarousel/carousel');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel');
    }
}
