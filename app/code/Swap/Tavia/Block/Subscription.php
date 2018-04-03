<?php

namespace Swap\Tavia\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use \Modulebazaar\Firstdataapi\Helper\License;

class Subscription extends Template {
	
	protected $subscriptions;
	protected $recentsubscriptions;
	
    private $orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	
/** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;	
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Modulebazaar\Firstdataapi\Model\SubscriptiontaviaFactory $SubscriptiontaviaFactory,
        \Modulebazaar\Firstdataapi\Helper\License $helperData,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_subscriptiontaviaFactory = $SubscriptiontaviaFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
		 $this->_helperData = $helperData;
        parent::__construct($context, $data);
    }
	
	protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Subscriptions'));
    }
  public function getHelperForCron($subscription)
   {

	return $this->_helperData->createMageOrders($subscription);
	}
	
	private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }
	
	private function getViewUrl($_subscription)
    {
        return $this->getUrl('tavia/customer/subscription', ['subscription_id' => $_subscription->getId()]);
    }
	
	private function getStopUrl($orderId)
    {
        return $this->getUrl('tavia/customer/subscription', ['subscription_id' => $orderId]);
    }
	
	public function getSubscriptions()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->subscriptions) {
		
            $this->subscriptions = $this->_subscriptiontaviaFactory->create()->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                ['eq' => $customerId]
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->subscriptions;
    }
	
	public function getRecentSubscriptions()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->recentsubscriptions) {
		
            $this->recentsubscriptions = $this->_subscriptiontaviaFactory->create()->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                ['eq' => $customerId]
            )->setPageSize(
            '5'
			)->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->recentsubscriptions;
    }
	
	public function getForceStopSubscription($subscription_id)
    {		
		$_subscription = $this->_subscriptiontaviaFactory->create()->load($subscription_id);
		$_subscription->setStatus(0)->save();
        return $_subscription->getOrderId();
    }
	
	public function getStopSubscription($subscription_id)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create('Magento\Customer\Model\Session');
		if($customerSession->isLoggedIn()) {
		   $customerId = $customerSession->getId();
		}
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }		
		$_subscription = $this->_subscriptiontaviaFactory->create()->load($subscription_id);
		$_subscription->setStatus(0)->save();
        return $_subscription->getOrderId();
    }
	
	/********* get Subscription To Be Ordered*******/
	public function getSubscriptionOrdersToBeCreated()
    {
		$now = new \DateTime();
		$today = $now->format('Y-m-d');
		$from = $today.' 00:00:00';
		$to = $today.' 23:59:59';
		$subscriptions = $this->_subscriptiontaviaFactory->create()->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'next_at',
                ['gteq' => $from]
            )->addFieldToFilter(
                'next_at',
                ['lteq' => $to]
            )->addFieldToFilter(
                'status',
                ['eq' => 1]            
            )->setOrder(
                'created_at',
                'desc'
            );
			
        return $subscriptions;
    }
	
	/********* get Subscription By Order*******/
	public function getSubscriptionByOrder($orderId)
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
		
		$subscription = $this->_subscriptiontaviaFactory->create()->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                ['eq' => $customerId]
            )->addFieldToFilter(
                'order_id',
                ['eq' => $orderId]            
            )->setOrder(
                'created_at',
                'desc'
            )->getFirstItem();
			
        return $subscription;
    }
	
	/********* get Order By Subscription*******/
	public function getOrderBySubscription($orderId)
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }		
		$subscription = $this->getSubscriptionByOrder($orderId);
		
        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'entity_id',
                ['in' => $subscription->getOrderList()]
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
    }
	
	
}

?>