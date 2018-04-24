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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'kemana_abdn_counter',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Kemana Abandoned Counter Notification'
            ]
        );

        $installer->endSetup();
    }
}