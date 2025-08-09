<?php
namespace Vendor\ReviewCarousel\Controller\Adminhtml\Carousel;

use Magento\Backend\App\Action;
use Vendor\ReviewCarousel\Model\CarouselConfig;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class DeleteCsv extends Action
{
    protected $filesystem;
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $configId = $this->getRequest()->getParam('config_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($configId) {
            try {
                $this->logger->debug('Deleting CSV file with config ID: ' . $configId);
                $config = $this->_objectManager->create(\Vendor\ReviewCarousel\Model\CarouselConfig::class);
                $config->load($configId);
                if ($config->getId()) {
                    $filePath = $config->getFilePath();
                    if ($filePath) {
                        $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                        $absolutePath = $mediaDir->getAbsolutePath($filePath);
                        if ($mediaDir->isFile($absolutePath)) {
                            $mediaDir->delete($absolutePath);
                            $this->logger->debug('Deleted CSV file from filesystem: ' . $absolutePath);
                        }
                    }
                    $config->delete();
                    $this->logger->debug('CSV config deleted successfully: ID ' . $configId);
                    $this->messageManager->addSuccessMessage(__('The CSV file has been deleted.'));
                    return $resultRedirect->setPath('reviewcarousel/carousel/managecsv');
                }
            } catch (\Exception $e) {
                $this->logger->critical('Error deleting CSV file: ' . $e->getMessage());
                $this->messageManager->addErrorMessage(__('An error occurred while deleting the CSV file: %1', $e->getMessage()));
                return $resultRedirect->setPath('reviewcarousel/carousel/managecsv');
            }
        }
        $this->logger->warning('No config ID provided for CSV deletion');
        $this->messageManager->addErrorMessage(__('We can\'t find a CSV file to delete.'));
        return $resultRedirect->setPath('reviewcarousel/carousel/managecsv');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_ReviewCarousel::carousel_managecsv');
    }
}
