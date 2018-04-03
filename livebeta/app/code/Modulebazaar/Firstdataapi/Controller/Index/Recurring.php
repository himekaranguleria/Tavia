<?php
namespace Modulebazaar\Firstdataapi\Controller\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;

class Recurring extends \Magento\Framework\App\Action\Action
{
    protected $scopeConfig;
    protected $storeManager;
    protected $_escaper;
    protected $quote;
    protected $order;
    protected $session;
    protected $license;
    protected $resources;
    protected $ordernotifier;
    protected $ordersender;
    protected $fpapi;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Escaper $escaper, \Magento\Framework\HTTP\Client\Curl $curl, \Magento\Quote\Model\Quote $quote, \Magento\Sales\Model\Order $order, \Magento\Checkout\Model\Session $session, \Modulebazaar\Firstdataapi\Helper\License $license, \Magento\Framework\App\ResourceConnection $resources, \Magento\Sales\Model\Order\Email\Sender\OrderSender $ordersender, \Magento\Sales\Model\OrderNotifier $ordernotifier, \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remotehost, \Modulebazaar\Firstdataapi\Model\FirstdataApi $fdapi)
    {
        parent::__construct($context);
        $this->remotehost = $remotehost;
        $this->scopeConfig  = $scopeConfig;
        $this->quote  = $quote;
        $this->fdapi  = $fdapi;
        $this->order  = $order;
        $this->session  = $session;
        $this->license  = $license;
        $this->storeManager = $storeManager;
        $this->_escaper     = $escaper;
        $this->_curl = $curl;
        $this->resources = $resources;
        $this->ordersender = $ordersender;
        $this->ordernotifier = $ordernotifier;
    }

    public function execute()
    {	
		
      // echo  $this->license->getVersion(); //die();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$SubscriptionBlock = $objectManager->get('\Swap\Tavia\Block\Subscription');
		//$orderModel = $objectManager->create('\Magento\Sales\Model\Order');

		
		$subscriptions = $SubscriptionBlock->getSubscriptionOrdersToBeCreated();
		foreach($subscriptions as $subscription){
			//echo $subscription->getId().'<br/>';
			//$orderId = $subscription->getOrderId();
			//$order = $orderModel->load($orderId);
			//$quote_id = $order->getQuoteId();
			echo $allow       = $this->license->createMageOrders($subscription);
			
		}
	//	$orderId = 174;

		//$order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
		
	//	$quote_id = $order->getQuoteId();
      //  echo $allow       = $this->license->createMageOrders($quote_id);
    }

    public function paymentCrendencialFailure($order)
    {
        $msg         = "Unauthorized Request. Bad or missing credentials.";
        $gotoSection = false;
        if ($order->getId()) {
            if ($order->getState() != 'canceled') {
                $order->registerCancellation($msg)->save();
            }
            $quotes = $this->quote->load($order->getQuoteId());
            if ($quotes->getId()) {
                $quotes->setIsActive(1)->setReservedOrderId(null)->save();

                $this->session->replaceQuote($quotes);
            }
            $this->session->unsLastRealOrderId();
            $this->messageManager->addError($msg);
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('checkout/cart/index');
            $this->messageManager->addError($msg);
            return $resultRedirect;
        }
    }
}
