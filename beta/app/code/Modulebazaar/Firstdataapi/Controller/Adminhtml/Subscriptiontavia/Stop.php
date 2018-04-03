<?php 

namespace Modulebazaar\Firstdataapi\Controller\Adminhtml\Subscriptiontavia;

class Stop extends \Magento\Framework\App\Action\Action { 
  
 public function execute() { 
	$subscription_id = $this->getRequest()->getParam('subscription_id');
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$Subscription = $objectManager->get('\Swap\Tavia\Block\Subscription');
	$order_id = $Subscription->getForceStopSubscription($subscription_id);
	return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => $order_id]);    
    $this->_view->loadLayout(); 
    $this->_view->renderLayout(); 
  } 
  
} 