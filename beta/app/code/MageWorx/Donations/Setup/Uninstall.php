<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Module uninstall code
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $connection->dropTable($connection->getTableName('mageworx_donations_charity'));

        //delete from table sales_order
        $connection->dropColumn($this->getTable('sales_order'), 'mageworx_donation_invoiced');
        $connection->dropColumn($this->getTable('sales_order'), 'base_mageworx_donation_invoiced');
        $connection->dropColumn($this->getTable('sales_order'), 'mageworx_donation_refunded');
        $connection->dropColumn($this->getTable('sales_order'), 'base_mageworx_donation_refunded');
        $connection->dropColumn($this->getTable('sales_order'), 'mageworx_donation_cancelled');
        $connection->dropColumn($this->getTable('sales_order'), 'base_mageworx_donation_cancelled');
        $connection->dropColumn($this->getTable('sales_order'), 'mageworx_donation_amount');
        $connection->dropColumn($this->getTable('sales_order'), 'base_mageworx_donation_amount');
        $connection->dropColumn($this->getTable('sales_order'), 'mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('sales_order'), 'base_mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('sales_order'), 'mageworx_donation_details');


        //delete from table sales_invoice
        $connection->dropColumn($this->getTable('sales_invoice'), 'mageworx_donation_amount');
        $connection->dropColumn($this->getTable('sales_invoice'), 'base_mageworx_donation_amount');
        $connection->dropColumn($this->getTable('sales_invoice'), 'mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('sales_invoice'), 'base_mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('sales_invoice'), 'mageworx_donation_details');

        //delete from table quote_address
        $connection->dropColumn($this->getTable('quote_address'), 'mageworx_donation_amount');
        $connection->dropColumn($this->getTable('quote_address'), 'base_mageworx_donation_amount');
        $connection->dropColumn($this->getTable('quote_address'), 'mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('quote_address'), 'base_mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('quote_address'), 'mageworx_donation_details');

        //delete from table sales_creditmemo
        $connection->dropColumn($this->getTable('sales_creditmemo'), 'mageworx_donation_amount');
        $connection->dropColumn($this->getTable('sales_creditmemo'), 'base_mageworx_donation_amount');
        $connection->dropColumn($this->getTable('sales_creditmemo'), 'mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('sales_creditmemo'), 'base_mageworx_donation_tax_amount');
        $connection->dropColumn($this->getTable('sales_creditmemo'), 'mageworx_donation_details');

        $setup->endSetup();
    }
}