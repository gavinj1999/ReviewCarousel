<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Vendor\ReviewCarousel\Model\Carousel;
use Psr\Log\LoggerInterface;

class Edit extends Action
{
    protected $resultPageFactory;
    protected $coreRegistry;
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->logger->debug('Loading Edit page for route: ' . $this->getRequest()->getFullActionName());
            $id = $this->getRequest()->getParam('id');
            $carousel = $this->_objectManager->create(\Vendor\ReviewCarousel\Model\Carousel::class);
            $this->logger->debug('Carousel model instantiated for ID: ' . ($id ?: 'new'));
            if ($id) {
                $this->logger->debug('Loading carousel with ID: ' . $id);
                $carousel->load($id);
                if (!$carousel->getId()) {
                    $this->logger->warning('Carousel not found for ID: ' . $id);
                    $this->messageManager->addErrorMessage(__('This carousel no longer exists.'));
                    return $this->resultRedirectFactory->create()->setPath('reviewcarousel/carousel');
                }
            }
            $this->coreRegistry->register('review_carousel', $carousel);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Vendor_ReviewCarousel::carousel');
            $resultPage->getConfig()->getTitle()->prepend($carousel->getId() ? __('Edit Carousel %1', $carousel->getName() ?? '') : __('New Carousel'));
            $this->logger->debug('Edit page loaded successfully for carousel ID: ' . ($carousel->getId() ?: 'new'));
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->critical('Error in Edit controller: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while loading the carousel form: %1', $e->getMessage()));
            return $this->resultRedirectFactory->create()->setPath('reviewcarousel/carousel');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel');
    }
}
