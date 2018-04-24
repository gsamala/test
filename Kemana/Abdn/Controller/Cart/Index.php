<?php
namespace Kemana\Abdn\Controller\Cart;


class Index extends \Magento\Checkout\Controller\Cart\Index
{
	protected $objectManager;
    protected $resultPageFactory;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $resultPageFactory
        );
        $this->objectManager = $context->getObjectManager();
        $this->resultPageFactory = $resultPageFactory;
    }

	public function execute()
    {
        $token = $this->getRequest()->getParam('token', false);
        if($token) {
        	$log = $this->_objectManager->create('\Kemana\Abdn\Model\ResourceModel\ReminderLog\Collection')
        		->addFieldToFilter('token', array('eq' => $token))
        		->addFieldToFilter('expire_at', array('gteq' => date('Y-m-d H:i:s')))
        		->load()
                ->getFirstItem()
                ->getData();


                if($log){
        			$quote = $this->_objectManager->create('\Magento\Quote\Model\Quote')->load($log['quote_id']);
                    if($quote->getCustomerId())
                    {
                        $customerSession = $this->_getCustomerSession();
                        if($customerSession->isLoggedIn())
                        {
                        	if($quote->getCustomerId() == $customerSession->getCustomer()->getId()){
                        		$resultPage = $this->resultPageFactory->create();
	                            $resultPage->getConfig()->getTitle()->set(__('Shopping Cart'));
	                            return $resultPage;
                        	}else{
                        		$this->messageManager->addNotice("Invalid token."); 
                   				$this->_redirect('/');
                        	}
                            

                        }else{
                            $this->_redirect('customer/account');
                        }
                        
                    }else{
                        $this->_redirect('customer/account');
                    }
                }else{
                   $this->messageManager->addNotice("Invalid token or token already expired."); 
                   $this->_redirect('/');
                }
        	
        }
    }

    protected function _getCustomerSession()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }
}