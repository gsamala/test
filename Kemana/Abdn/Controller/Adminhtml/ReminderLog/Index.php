<?php

namespace Kemana\Abdn\Controller\Adminhtml\ReminderLog;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Kemana_Abdn::reminderlog');
		$resultPage->addBreadcrumb(__('Abandoned Cart Reminder Logs'), __('Abandoned Cart Reminder Logs'));
		$resultPage->getConfig()->getTitle()->prepend((__('Abandoned Cart Reminder Logs')));

		return $resultPage;
	}

	/**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kemana_Abdn::reminderlog');
    }


}