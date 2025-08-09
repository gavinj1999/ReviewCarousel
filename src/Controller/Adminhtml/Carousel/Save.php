<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Vendor\ReviewCarousel\Model\Carousel;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

class Save extends Action implements HttpPostActionInterface
{
    protected $coreRegistry;
    protected $logger;
    protected $formKeyValidator;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        LoggerInterface $logger,
        Validator $formKeyValidator
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->logger->debug('Attempting to save carousel data');
            if (!$this->getRequest()->isPost()) {
                $this->logger->warning('Invalid request method: not a POST request');
                $this->messageManager->addErrorMessage(__('Invalid request method.'));
                return $resultRedirect->setPath('reviewcarousel/carousel');
            }
            $data = $this->getRequest()->getPostValue();
            $this->logger->debug('Form data received: ' . json_encode($data));
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                $this->logger->warning('Invalid form key during save');
                $this->messageManager->addErrorMessage(__('Invalid form key. Please refresh the page and try again.'));
                $this->_getSession()->setFormData($data);
                return $resultRedirect->setPath('reviewcarousel/carousel/edit', ['id' => $data['id'] ?? null]);
            }
            if (empty($data['name'])) {
                $this->logger->warning('Name field is empty');
                $this->messageManager->addErrorMessage(__('The Name field is required.'));
                $this->_getSession()->setFormData($data);
                return $resultRedirect->setPath('reviewcarousel/carousel/edit', ['id' => $data['id'] ?? null]);
            }
            $id = !empty($data['id']) ? $data['id'] : null;
            $carousel = $this->_objectManager->create(\Vendor\ReviewCarousel\Model\Carousel::class);
            $this->logger->debug('Carousel model instantiated for ID: ' . ($id ?: 'new'));
            if ($id) {
                $carousel->load($id);
                if (!$carousel->getId()) {
                    $this->logger->warning('Carousel not found for ID: ' . $id);
                    $this->messageManager->addErrorMessage(__('This carousel no longer exists.'));
                    return $resultRedirect->setPath('reviewcarousel/carousel');
                }
            }
            $carousel->setName($data['name']);
            $carousel->setDefaultRatings(isset($data['default_ratings']) && $data['default_ratings'] !== '' ? $data['default_ratings'] : null);
            $carousel->setExcludeNoText(isset($data['exclude_no_text']) ? (int)$data['exclude_no_text'] : 1);
            $carousel->setDefaultSort(isset($data['default_sort']) && $data['default_sort'] !== '' ? $data['default_sort'] : null);
            $carousel->setBgColor(isset($data['bg_color']) && $data['bg_color'] !== '' ? $data['bg_color'] : null);
            $carousel->setTextColor(isset($data['text_color']) && $data['text_color'] !== '' ? $data['text_color'] : null);
            $carousel->setStarColor(isset($data['star_color']) && $data['star_color'] !== '' ? $data['star_color'] : null);
            $carousel->setFeaturedBgColor(isset($data['featured_bg_color']) && $data['featured_bg_color'] !== '' ? $data['featured_bg_color'] : null);
            $carousel->setStarSize(isset($data['star_size']) && $data['star_size'] !== '' ? (int)$data['star_size'] : null);
            $carousel->setFontSize(isset($data['font_size']) && $data['font_size'] !== '' ? (int)$data['font_size'] : null);
            $carousel->setFontFamily(isset($data['font_family']) && $data['font_family'] !== '' ? $data['font_family'] : null);
            $carousel->setFeaturedReviewIndex(isset($data['featured_review_index']) && $data['featured_review_index'] !== '' ? (int)$data['featured_review_index'] : null);
            $this->logger->debug('Saving carousel with data: ' . json_encode($carousel->getData()));
            $carousel->save();
            $this->logger->debug('Carousel saved successfully: ID ' . $carousel->getId());
            $this->messageManager->addSuccessMessage(__('The carousel has been saved.'));
            $this->_getSession()->setFormData(false);
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('reviewcarousel/carousel/edit', ['id' => $carousel->getId()]);
            }
            return $resultRedirect->setPath('reviewcarousel/carousel');
        } catch (\Exception $e) {
            $this->logger->critical('Error saving carousel: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $this->messageManager->addErrorMessage(__('An error occurred while saving the carousel: %1', $e->getMessage()));
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('reviewcarousel/carousel/edit', ['id' => $id]);
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel');
    }
}
