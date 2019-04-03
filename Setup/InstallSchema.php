<?php

namespace NetzExpert\MinimumOrderFee\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_logger = $logger;
    }

    /**
     * Function install
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        //START table setup

        $quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';

        //Setup two columns for quote, quote_address and order

        //Quote address tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Amount'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'base_minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Minimum order Fee Amount'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'minimum_order_fee_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Tax Amount'

                ]
            );

        //Order tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Amount'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'base_minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Fee Amount'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'minimum_order_fee_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Tax Amount'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'minimum_order_fee_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Minimum order Fee Amount Refunded'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'base_minimum_order_fee_refunded',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Minimum order Fee Amount Refunded'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'minimum_order_fee_invoiced',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Amount Invoiced'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'base_minimum_order_fee_invoiced',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Base Fee Amount Invoiced'
                ]
            );
        //Invoice tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Amount'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'base_minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Base Fee Amount'

                ]
            );
        //Credit memo tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Fee Amount'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'base_minimum_order_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Minimum order Base Fee Amount'

                ]
            );

        $installer->endSetup();
        //END   table setup
    }
}
