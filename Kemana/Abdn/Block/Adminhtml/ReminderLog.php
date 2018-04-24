<?php
namespace Kemana\Abdn\Block\Adminhtml;

class ReminderLog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_reminderlog';
        $this->_blockGroup = 'Kemana_Abdn';
        $this->_headerText = __('Abandonded Cart Reminder Logs');	
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
