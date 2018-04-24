<?php

namespace Kemana\Abdn\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * @param $label
     * @param null $store
     * @return mixed
     */
    public function getConfig($label,$store=null)
    {
        return $this->scopeConfig->getValue($label,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
}