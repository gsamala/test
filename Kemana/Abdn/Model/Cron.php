<?php

namespace Kemana\Abdn\Model;

class Cron{

    // protected $unit;
    // protected $max_times;
    // protected $reminder_time;
    // protected $customergroups;
    protected $_storeManager;
    protected $_helper;
    protected $_objectManager;
    protected $_logger;
    protected $_transportBuilder;


    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Kemana\Abdn\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Kemana\Abdn\Logger\Logger $logger
    ) 
    {
        $this->_storeManager    = $storeManager;
        $this->_helper          = $helper;
        $this->_objectManager   = $objectManager;
        $this->_logger          = $logger;
        $this->_transportBuilder= $transportBuilder;
    }

    /**
    * Write to system.log
    *
    * @return void
    */

    public function execute() {
        foreach($this->_storeManager->getStores() as $storeId => $val)
        {
            $this->_storeManager->setCurrentStore($storeId);
            if($this->_helper->getConfig(\Kemana\Abdn\Model\Config::ACTIVE)) {
                $this->_process($storeId);
            }
        }
    }

    /**
     * to process send email
     * @param  [type] $storeId [description]
     * @return [type]          [description]
     */
    public function _process($storeId){
        $unit = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::UNIT, $storeId);
        $max_times = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::MAX_NUMBER_TIMES, $storeId);
        $reminder_time = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::REMINDER_TIME, $storeId);

        if($this->_helper->getConfig(\Kemana\Abdn\Model\Config::CUSTOMER_GROUPS, $storeId)){
            $customergroups   = explode(",", $this->_helper->getConfig(\Kemana\Abdn\Model\Config::CUSTOMER_GROUPS, $storeId));
        } else {
            $customergroups   = array();
        }

        // get collection of abandoned carts with cart_counter == $run
        $collection = $this->_objectManager->create('Magento\Reports\Model\ResourceModel\Quote\Collection');
        $collection->prepareForAbandonedReport($storeId);

        if (count($customergroups)) {
            $collection->addFieldToFilter('main_table.customer_group_id', array('in' => $customergroups));
        }

        // send email
        $senderName = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::SENDER_NAME, $storeId);
        $senderEmail = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::SENDER_EMAIL, $storeId);
        $sender = array(
            'name' => $senderName,
            'email' => $senderEmail
        );
        $subject = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::EMAIL_SUBJECT, $storeId);
        $templateId = $this->_helper->getConfig(\Kemana\Abdn\Model\Config::EMAIL_TEMPLATE, $storeId);

        
            
        // for each cart of the current run
        foreach ($collection as $key => $quote) {
            $customer_email = $quote->getCustomerEmail();
            $customer_name = $quote->getCustomerFirstname() . ' ' . $quote->getCustomerMiddlename() . ' ' . $quote->getCustomerLastname();
            $token = $this->generateToken(); 
            $url = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . 'kemanaabdn/cart?token=' . $token;
            $vars = array(
                'quote' => $quote,
                'subject' => $subject,
                'name' => $customer_name,
                'url' => $url,
                'items' => $quote->getAllVisibleItems()
            );

            $items = $this->getProductQuote($quote);

            $created_at = $quote->getCreatedAt();
            $updated_at = $quote->getUpdatedAt();
            $abdn_at = $quote->getKemanaAbdnDateAt();
            $counter = $quote->getKemanaAbdnCounter() + 1;
            $current_date = strtotime(date('Y-m-d H:i:s'));

            $quote2 = $this->_objectManager->create('\Magento\Quote\Model\Quote')->loadByIdWithoutStore($quote->getId());
            if($updated_at != null && strtotime($updated_at) != strtotime('0000-00-00 00:00:00')){
                if(strtotime($updated_at) <= strtotime($abdn_at)){
                    $counter = $counter;
                    $last_date = strtotime($abdn_at);
                }else{
                    $counter = 1;
                    $last_date = strtotime($updated_at);
                }
                
            }else{
                $counter = $counter;
                if($abdn_at != null){
                    $last_date = strtotime($abdn_at);
                }else{
                    $last_date = strtotime($created_at);
                }
            }

            if($unit == 0){
                $unit_text = 'days';
                $diff_date = ($current_date - $last_date) / (60 * 60 * 24);
            }else{
                $unit_text = 'hours';
                $diff_date = ($current_date - $last_date) / (60 * 60);
            }

            if($counter <= $max_times && $diff_date >= $reminder_time){
                $quote2->setKemanaAbdnCounter($counter);
                $quote2->setKemanaAbdnDateAt(date('Y-m-d H:i:s'));
                $quote2->save();

                $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                    ->setTemplateVars($vars)
                    ->setFrom($sender)
                    ->addTo($customer_email, $customer_name)
                    ->getTransport();

                $transport->sendMessage();

                $comment = sprintf(
                    "Email sent to %s; product: %s; url: %s",
                    $customer_name,
                    implode(',', $items['product']),
                    $url
                );

                $reminderLog = $this->_objectManager->create('Kemana\Abdn\Model\ReminderLog');
                $reminderLog->setQuoteId($quote->getId());
                $reminderLog->setEmail($customer_email);
                $reminderLog->setToken($token);
                $reminderLog->setExpireAt(date('Y-m-d H:i:s', strtotime("+ ".$reminder_time." ".$unit_text)));
                $reminderLog->setComment($comment);
                $reminderLog->setStoreId($storeId);
                $reminderLog->setCreatedAt(date('Y-m-d H:i:s'));
                $reminderLog->save();
            }

            $this->_logger->info('Cron Abandoned Cart Works');
        }
    }

    /**
     * To get product in array
     * @param  [type] $quote [description]
     * @return [type]        [description]
     */
    public function getProductQuote($quote)
    {
        $products = $quote->getAllVisibleItems();

        $items = array();
        foreach ($products as $key => $value) {
            $items['product'][] = $value->getName()." (".$value->getQty().")";
        }

        return $items;
    }

    
    /**
     * Generate Token for url
     * @return [type] [description]
     */
    function generateToken()
    {
        $date = date('Y-m-d H:i:s');
        $token = md5($date);
        $codeAlphabet = "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < 3; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        return $token;
    }
}