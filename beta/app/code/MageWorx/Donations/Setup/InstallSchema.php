<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'mageworx_donations_charity'
         */
        $tableDonationsCharity = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_donations_charity')
        )->addColumn(
            'charity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Charity ID'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [
                'nullable' => false,
                'default' => ''
            ],
            'Name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [
                'nullable' => false,
                'default' => ''
            ],
            'Description'
        )->addColumn(
            'filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'default' => '',
            ],
            'File Name'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
            ],
            'Sort Order'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
            ],
            'Is Active'
        )->addColumn(
            'date_added',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Added Date'
        );
        $installer->getConnection()->createTable($tableDonationsCharity);


        $this->extendSalesOrderTable($installer);
        $this->extendQuoteAddressTable($installer);
        $this->extendSalesInvoiceTable($installer);
        $this->extendSalesCreditMemoTable($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     */

    protected function extendSalesOrderTable($installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'mageworx_donation_invoiced',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'MageWorx Donation Invoiced'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_mageworx_donation_invoiced',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'Base MageWorx Donation Invoiced'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'mageworx_donation_refunded',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'MageWorx Donation Refunded'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_mageworx_donation_refunded',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'Base MageWorx Donation Refunded'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'mageworx_donation_cancelled',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'MageWorx Donation Canceled'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_mageworx_donation_cancelled',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'Base MageWorx Donation Canceled'
            ]
        );

        $this->extendTable($installer, 'sales_order');
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    protected function extendSalesInvoiceTable($installer)
    {
        $this->extendTable($installer, 'sales_invoice');
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param
     */
    protected function extendQuoteAddressTable($installer)
    {
        $this->extendTable($installer, 'quote_address');
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    protected function extendSalesCreditMemoTable($installer)
    {
        $this->extendTable($installer, 'sales_creditmemo');
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param null|string          $tableName
     */
    protected function extendTable($installer, $tableName)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable($tableName),
            'mageworx_donation_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'MageWorx Donation Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable($tableName),
            'base_mageworx_donation_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Base MageWorx Donation Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable($tableName),
            'mageworx_donation_tax_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Mageworx Donation Tax Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable($tableName),
            'base_mageworx_donation_tax_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Base MageWorx Donation Tax Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable($tableName),
            'mageworx_donation_details',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'MageWorx Donation Details'
            ]
        );
    }

}