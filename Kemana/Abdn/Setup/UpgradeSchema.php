<?php 

/**
 * Kemana_Abdn Magento 2
 *
 * @category    Kemana
 * @package     Kemana_Abdn
 * @author      Gidayu Samala Lendra
 * @copyright   Kemana (http://kemana.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Kemana\Abdn\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('quote'),
	            'kemana_abdn_date_at',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
	                'nullable' => true,
	                'default' => null,
	                'comment' => 'Kemana Abandoned Counter Date at'
	            ]
	        );

	        $table  = $setup->getConnection()
	            ->newTable($setup->getTable('kemana_abandonedcart_log'))
	            ->addColumn(
	                'log_id',
	                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	                null,
	                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
	                'Kemana Abandoned Log Id'
	            )
	            ->addColumn(
	                'quote_id',
	                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	                10,
	                ['nullable'=>true,'default'=>null],
	                'Kemana Abandoned Cart Id'
	            )
	            ->addColumn(
	                'email',
	                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                128,
	                ['nullable'=>true,'default'=>null],
	                'Kemana Abandoned Email Customer'
	            )
	            ->addColumn(
	                'token',
	                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                128,
	                ['nullable'=>true,'default'=>null],
	                'Kemana Abandoned Token'
	            )
	            ->addColumn(
	                'expire_at',
	                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
	                null,
	                ['nullable'=>true,'default'=>null],
	                'Kemana Abandoned Token Expire Date'
	            )
	            ->addColumn(
	                'created_at',
	                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
	                null,
	                ['nullable'=>true,'default'=>null],
	                'Kemana Abandoned Log Created Date'
	            );
	        $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('kemana_abandonedcart_log'),
	            'comment',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'default' => null,
	                'comment' => 'Kemana Abandoned Email Log Comment'
	            ]
	        );
	    }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('kemana_abandonedcart_log'),
	            'store_id',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	                'nullable' => true,
	                'default' => null,
	                'comment' => 'Kemana Abandoned Email Log Store Id'
	            ]
	        );
	    }

        $setup->endSetup();
    }

}
