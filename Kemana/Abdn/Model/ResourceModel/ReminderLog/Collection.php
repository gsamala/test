<?php

namespace Kemana\Abdn\Model\ResourceModel\ReminderLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Kemana\Abdn\Model\ReminderLog', 'Kemana\Abdn\Model\ResourceModel\ReminderLog');
	}
}