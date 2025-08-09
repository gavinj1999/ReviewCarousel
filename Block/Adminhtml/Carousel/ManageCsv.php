<?php
namespace Vendor\ReviewCarousel\Block\Adminhtml\Carousel;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Vendor\ReviewCarousel\Model\ResourceModel\CarouselConfig\CollectionFactory;
use Psr\Log\LoggerInterface;

class ManageCsv extends Extended
{
    protected $collectionFactory;
    protected $logger;

    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        parent::__construct($context, $backendHelper, $data);
        $this->logger->debug('ManageCsv block initialized with CollectionFactory: ' . get_class($collectionFactory));
    }

    protected function _construct()
    {
        parent::_construct();
        try {
            $this->setId('manageCsvGrid');
            $this->setDefaultSort('config_id');
            $this->setDefaultDir('ASC');
            $this->logger->debug('ManageCsv block constructed successfully');
        } catch (\Exception $e) {
            $this->logger->critical('Error in ManageCsv block _construct: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    protected function _prepareCollection()
    {
        try {
            $collection = $this->collectionFactory->create();
            $this->logger->debug('ManageCsv collection created, count: ' . count($collection));
            $this->setCollection($collection);
            $this->logger->debug('ManageCsv collection prepared: ' . count($collection) . ' items');
            return parent::_prepareCollection();
        } catch (\Exception $e) {
            $this->logger->critical('Error preparing ManageCsv collection: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    protected function _prepareColumns()
    {
        try {
            $this->addColumn('config_id', ['header' => __('ID'), 'index' => 'config_id']);
            $this->addColumn('file_path', ['header' => __('File Path'), 'index' => 'file_path']);
            $this->addColumn('action', [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    ['caption' => __('Delete'), 'url' => ['base' => '*/*/deletecsv'], 'field' => 'config_id', 'confirm' => __('Are you sure you want to delete this CSV file?')]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]);
            $this->logger->debug('ManageCsv columns prepared successfully');
            return parent::_prepareColumns();
        } catch (\Exception $e) {
            $this->logger->critical('Error preparing ManageCsv columns: ' . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
