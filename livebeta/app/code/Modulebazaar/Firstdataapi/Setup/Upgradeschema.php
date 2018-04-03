<?php
namespace Modulebazaar\Firstdataapi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
 
class Upgradeschema implements UpgradeSchemaInterface
{
	 public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();
		
		$orderTable = 'sales_order';
 
        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            //Order Grid table
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable($orderTable),
                    'subcription_day',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => 4,
                        'comment' => 'Subscription Days'
                    ]
                );
        $installer->endSetup();
        }

		$tableName = $installer->getTable('subscriptiontavia');
		if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Order Id'
                )
                ->addColumn(
                    'order_number',
                    Table::TYPE_TEXT,
                    32,
                    ['nullable' => false, 'default' => ''],
                    'Order Number'
                )
                ->addColumn(
                    'cc_owner',
                    Table::TYPE_TEXT,
                    128,
                    ['nullable' => false, 'default' => ''],
                    'Cc Owner'
                )
                ->addColumn(
                    'cc_last_four',
                    Table::TYPE_TEXT,
                    12,
                    ['nullable' => false, 'default' => ''],
                    'Cc Last 4'
                )
                ->addColumn(
                    'cc_exp_date',
                    Table::TYPE_TEXT,
                    4,
                    ['nullable' => false, 'default' => ''],
                    'Cc Exp Date'
                )
                ->addColumn(
                    'cc_cvv',
                    Table::TYPE_TEXT,
                    4,
                    ['nullable' => false, 'default' => ''],
                    'Cc CVV'
                )
                ->addColumn(
                    'cc_type',
                    Table::TYPE_TEXT,
                    32,
                    ['nullable' => false, 'default' => ''],
                    'Cc Type'
                )
                ->addColumn(
                    'order_amount',
                    Table::TYPE_DECIMAL,
					'12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Order Number'
                )
                ->addColumn(
                    'order_list',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Order List'
                )
                ->addColumn(
                    'day',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Day'
                )
                ->addColumn(
                    'token',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Token'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Customer Id'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Staus'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Updated At'
                )
                ->addColumn(
                    'next_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Next At'
                )
              
                ->setComment('News Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
    }
}
