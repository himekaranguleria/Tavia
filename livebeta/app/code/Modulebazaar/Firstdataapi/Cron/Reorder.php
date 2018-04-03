<?php
namespace Modulebazaar\Firstdataapi\Cron;
class Reorder {
 
    protected $_logger;
    protected $license;
 
    public function __construct(\Psr\Log\LoggerInterface $logger, \Modulebazaar\Firstdataapi\Helper\License $license) {
        $this->_logger = $logger;
        $this->license  = $license;
    }
 
    public function execute() {  
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$SubscriptionBlock = $objectManager->get('\Swap\Tavia\Block\Subscription');	
		$subscriptions = $SubscriptionBlock->getSubscriptionOrdersToBeCreated();
		
		$msg='';
		$r='';
		
		foreach($subscriptions as $subscription){
			//$this->license->createMageOrders($subscription);
			$r = $SubscriptionBlock->getHelperForCron($subscription);
			$msg.=$subscription->getId().',';
		}
		$this->_logger->debug('Message: '.$msg.' Order #'.$r.' created');
		$this->_logger->debug($subscriptions->getSelect());
		
        return $this;
    }
}