<?php
namespace Vendor\ReviewCarousel\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable($installer->getTable('vendor_review_carousel'))
            ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID')
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Carousel Name')
            ->addColumn('default_ratings', Table::TYPE_TEXT, 255, ['nullable' => true], 'Default Ratings')
            ->addColumn('exclude_no_text', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => 1], 'Exclude Reviews Without Text')
            ->addColumn('default_sort', Table::TYPE_TEXT, 50, ['nullable' => true], 'Default Sort')
            ->addColumn('bg_color', Table::TYPE_TEXT, 7, ['nullable' => true], 'Background Color')
            ->addColumn('text_color', Table::TYPE_TEXT, 7, ['nullable' => true], 'Text Color')
            ->addColumn('star_color', Table::TYPE_TEXT, 7, ['nullable' => true], 'Star Color')
            ->addColumn('featured_bg_color', Table::TYPE_TEXT, 7, ['nullable' => true], 'Featured Background Color')
            ->addColumn('star_size', Table::TYPE_INTEGER, null, ['nullable' => true], 'Star Size')
            ->addColumn('font_size', Table::TYPE_INTEGER, null, ['nullable' => true], 'Font Size')
            ->addColumn('font_family', Table::TYPE_TEXT, 50, ['nullable' => true], 'Font Family')
            ->addColumn('featured_review_index', Table::TYPE_INTEGER, null, ['nullable' => true], 'Featured Review Index')
            ->setComment('Review Carousel Configurations');
        $installer->getConnection()->createTable($table);

        $reviewsTable = $installer->getConnection()
            ->newTable($installer->getTable('vendor_review_carousel_reviews'))
            ->addColumn('review_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Review ID')
            ->addColumn('place_id', Table::TYPE_TEXT, 255, ['nullable' => true], 'Place ID')
            ->addColumn('place_name', Table::TYPE_TEXT, 255, ['nullable' => true], 'Place Name')
            ->addColumn('review_link', Table::TYPE_TEXT, 255, ['nullable' => true], 'Review Link')
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => true], 'Reviewer Name')
            ->addColumn('reviewer_id', Table::TYPE_TEXT, 255, ['nullable' => true], 'Reviewer ID')
            ->addColumn('reviewer_profile', Table::TYPE_TEXT, 255, ['nullable' => true], 'Reviewer Profile')
            ->addColumn('rating', Table::TYPE_DECIMAL, '3,1', ['nullable' => true], 'Rating')
            ->addColumn('review_text', Table::TYPE_TEXT, null, ['nullable' => true], 'Review Text')
            ->addColumn('published_at', Table::TYPE_TEXT, 255, ['nullable' => true], 'Published At')
            ->addColumn('published_at_date', Table::TYPE_TEXT, 255, ['nullable' => true], 'Published At Date')
            ->addColumn('response_from_owner_text', Table::TYPE_TEXT, null, ['nullable' => true], 'Response From Owner Text')
            ->addColumn('response_from_owner_ago', Table::TYPE_TEXT, 255, ['nullable' => true], 'Response From Owner Ago')
            ->addColumn('response_from_owner_date', Table::TYPE_TEXT, 255, ['nullable' => true], 'Response From Owner Date')
            ->addColumn('total_number_of_reviews_by_reviewer', Table::TYPE_INTEGER, null, ['nullable' => true], 'Total Reviews by Reviewer')
            ->addColumn('total_number_of_photos_by_reviewer', Table::TYPE_INTEGER, null, ['nullable' => true], 'Total Photos by Reviewer')
            ->addColumn('is_local_guide', Table::TYPE_TEXT, 255, ['nullable' => true], 'Is Local Guide')
            ->addColumn('review_translated_text', Table::TYPE_TEXT, null, ['nullable' => true], 'Review Translated Text')
            ->addColumn('response_from_owner_translated_text', Table::TYPE_TEXT, null, ['nullable' => true], 'Response From Owner Translated Text')
            ->addColumn('experience_details', Table::TYPE_TEXT, null, ['nullable' => true], 'Experience Details')
            ->addColumn('review_photos', Table::TYPE_TEXT, null, ['nullable' => true], 'Review Photos')
            ->setComment('Review Carousel Reviews');
        $installer->getConnection()->createTable($reviewsTable);

        $installer->endSetup();
    }
}