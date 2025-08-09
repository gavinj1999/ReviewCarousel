<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Psr\Log\LoggerInterface;

class UploadSave extends Action implements HttpPostActionInterface
{
    protected $logger;
    protected $formKeyValidator;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        LoggerInterface $logger,
        Validator $formKeyValidator
    ) {
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->logger->debug('Attempting to save uploaded CSV file');
            if (!$this->getRequest()->isPost()) {
                $this->logger->warning('Invalid request method: not a POST request');
                $this->messageManager->addErrorMessage(__('Invalid request method.'));
                return $resultRedirect->setPath('reviewcarousel/carousel/upload');
            }
            $data = $this->getRequest()->getPostValue();
            $this->logger->debug('Form data received: ' . json_encode($data));
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                $this->logger->warning('Invalid form key during upload save');
                $this->messageManager->addErrorMessage(__('Invalid form key. Please refresh the page and try again.'));
                return $resultRedirect->setPath('reviewcarousel/carousel/upload');
            }
            if (!isset($_FILES['csv_file']['name']) || empty($_FILES['csv_file']['name'])) {
                $this->logger->debug('No CSV file uploaded');
                $this->messageManager->addErrorMessage(__('No file was uploaded.'));
                return $resultRedirect->setPath('reviewcarousel/carousel/upload');
            }
            $filePath = $_FILES['csv_file']['tmp_name'];
            if (!file_exists($filePath)) {
                $this->logger->warning('Uploaded CSV file not found at: ' . $filePath);
                $this->messageManager->addErrorMessage(__('Uploaded file could not be processed.'));
                return $resultRedirect->setPath('reviewcarousel/carousel/upload');
            }
            $connection = $this->_objectManager->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
            $tableName = $connection->getTableName('vendor_review_carousel_reviews');
            $connection->truncateTable($tableName);
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file);
            $headers = array_map('trim', $headers);
            $expectedHeaders = ['place_id', 'place_name', 'review_id', 'review_link', 'name', 'reviewer_id', 'reviewer_profile', 'rating', 'review_text', 'published_at', 'published_at_date', 'response_from_owner_text', 'response_from_owner_ago', 'response_from_owner_date', 'total_number_of_reviews_by_reviewer', 'total_number_of_photos_by_reviewer', 'is_local_guide', 'review_translated_text', 'response_from_owner_translated_text', 'experience_details', 'review_photos'];
            $requiredHeaders = ['place_id', 'place_name', 'review_id', 'name', 'rating', 'review_text', 'published_at'];
            if (!array_intersect($requiredHeaders, $headers) === $requiredHeaders) {
                $this->logger->warning('Invalid CSV headers: ' . implode(',', $headers));
                $this->messageManager->addErrorMessage(__('CSV file must contain headers: ' . implode(',', $requiredHeaders)));
                fclose($file);
                return $resultRedirect->setPath('reviewcarousel/carousel/upload');
            }
            $rowsInserted = 0;
            while ($row = fgetcsv($file)) {
                $data = array_combine($headers, $row);
                $insertData = [
                    'place_id' => isset($data['place_id']) ? $data['place_id'] : null,
                    'place_name' => isset($data['place_name']) ? $data['place_name'] : null,
                    'review_id' => isset($data['review_id']) ? $data['review_id'] : null,
                    'review_link' => isset($data['review_link']) ? $data['review_link'] : null,
                    'name' => isset($data['name']) ? $data['name'] : null,
                    'reviewer_id' => isset($data['reviewer_id']) ? $data['reviewer_id'] : null,
                    'reviewer_profile' => isset($data['reviewer_profile']) ? $data['reviewer_profile'] : null,
                    'rating' => isset($data['rating']) ? (float)$data['rating'] : null,
                    'review_text' => isset($data['review_text']) ? $data['review_text'] : null,
                    'published_at' => isset($data['published_at']) ? $data['published_at'] : null,
                    'published_at_date' => isset($data['published_at_date']) ? $data['published_at_date'] : null,
                    'response_from_owner_text' => isset($data['response_from_owner_text']) ? $data['response_from_owner_text'] : null,
                    'response_from_owner_ago' => isset($data['response_from_owner_ago']) ? $data['response_from_owner_ago'] : null,
                    'response_from_owner_date' => isset($data['response_from_owner_date']) ? $data['response_from_owner_date'] : null,
                    'total_number_of_reviews_by_reviewer' => isset($data['total_number_of_reviews_by_reviewer']) ? (int)$data['total_number_of_reviews_by_reviewer'] : null,
                    'total_number_of_photos_by_reviewer' => isset($data['total_number_of_photos_by_reviewer']) ? (int)$data['total_number_of_photos_by_reviewer'] : null,
                    'is_local_guide' => isset($data['is_local_guide']) ? $data['is_local_guide'] : null,
                    'review_translated_text' => isset($data['review_translated_text']) ? $data['review_translated_text'] : null,
                    'response_from_owner_translated_text' => isset($data['response_from_owner_translated_text']) ? $data['response_from_owner_translated_text'] : null,
                    'experience_details' => isset($data['experience_details']) ? $data['experience_details'] : null,
                    'review_photos' => isset($data['review_photos']) ? $data['review_photos'] : null
                ];
                $connection->insert($tableName, $insertData);
                $rowsInserted++;
            }
            fclose($file);
            $this->logger->debug('Inserted ' . $rowsInserted . ' reviews into vendor_review_carousel_reviews');
            $this->messageManager->addSuccessMessage(__('The CSV file has been uploaded and %1 reviews imported successfully.', $rowsInserted));
            return $resultRedirect->setPath('reviewcarousel/carousel/upload');
        } catch (\Exception $e) {
            $this->logger->critical('Error saving uploaded CSV: ' . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $this->messageManager->addErrorMessage(__('An error occurred while saving the CSV file: %1', $e->getMessage()));
            return $resultRedirect->setPath('reviewcarousel/carousel/upload');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel_upload');
    }
}