<?php

namespace Kemana\Abdn\Model\ResourceModel;

class ReminderLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('kemana_abandonedcart_log', 'log_id');
	}
}