<?php
namespace Modulebazaar\Firstdataapi\Block\Adminhtml\Order;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;


class View extends Template{
	
    protected $orders;	
    private $orderCollectionFactory;
	
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Modulebazaar\Firstdataapi\Model\SubscriptiontaviaFactory $SubscriptiontaviaFactory,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_subscriptiontaviaFactory = $SubscriptiontaviaFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        parent::__construct($context, $data);
    }
 
	/********* get Subscription By Order*******/
	public function getSubscriptionByOrder($orderId)
    {
		
		$subscription = $this->_subscriptiontaviaFactory->create()->getCollection()->addFieldToSelect(
                '*'
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
		
		$subscription = $this->getSubscriptionByOrder($orderId);
		
        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create($subscription->getCustomerId())->addFieldToSelect(
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
	
	private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }
}