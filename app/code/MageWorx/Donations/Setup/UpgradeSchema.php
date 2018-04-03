<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use MageWorx\Donations\Model\Charity;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /* update column in table mageworx_donations_charity*/
        $this->updateDonationsCharityTable($setup);

        /* modify column in table mageworx_donations_charity */
        $this->modifyColumnDonationsCharityTable($setup);

        /* modify column in table sales_order*/
        $this->modifySalesOrderTable($setup);

        $setup->endSetup();
    }

    /**
     * Adds date Update Time
     *
     * @param SchemaSetupInterface $setup
     */
    protected function updateDonationsCharityTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Charity::DONATIONS_CHARITY_TABLE_NAME),
            'date_updated',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'length' => 'null',
                'nullable' => false,
                'comment' => 'Date update charity',
            ]
        );
    }

    protected function modifyColumnDonationsCharityTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable(Charity::DONATIONS_CHARITY_TABLE_NAME),
            'date_added',
            [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT,
                'type' => Table::TYPE_TIMESTAMP,
                'comment' => 'Date added charity'
            ]
        );
    }

    protected function modifySalesOrderTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable('sales_order'),
            'mageworx_donation_invoiced',
            [
                'nullable' => true,
                'length' => '12,4',
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable('sales_order'),
            'base_mageworx_donation_invoiced',
            [
                'nullable' => true,
                'length' => '12,4',
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable('sales_order'),
            'mageworx_donation_refunded',
            [
                'nullable' => true,
                'length' => '12,4',
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable('sales_order'),
            'base_mageworx_donation_refunded',
            [
                'nullable' => true,
                'length' => '12,4',
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable('sales_order'),
            'mageworx_donation_cancelled',
            [
                'nullable' => true,
                'length' => '12,4',
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable('sales_order'),
            'base_mageworx_donation_cancelled',
            [
                'nullable' => true,
                'length' => '12,4',
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
            ]
        );
    }
}