<?php
namespace Modulebazaar\Firstdataapi\Helper;

use Magento\Checkout\Model\Cart;

use Magento\Framework\Module\ModuleListInterface;

class License extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Catalog\Model\ProductFactory $product,
    \Magento\Framework\Data\Form\FormKey $formkey,
    \Magento\Quote\Model\QuoteFactory $quote,
    \Magento\Quote\Model\QuoteManagement $quoteManagement,
    \Magento\Customer\Model\CustomerFactory $customerFactory,
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
    \Magento\Sales\Model\Service\OrderService $orderService,
    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
    \Magento\Sales\Model\Service\InvoiceService $invoiceService,
    \Magento\Framework\DB\Transaction $transaction,
    \Magento\Sales\Api\Data\OrderInterface $order,
    \Magento\Framework\ObjectManagerInterface $objectmanager,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
    \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
    \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
    \Magento\Quote\Model\Quote\Address\Rate $shippingRate, 
	\Modulebazaar\Firstdataapi\Model\FirstdataApi $fdapi,
	\Modulebazaar\Firstdataapi\Model\Subscriptiontavia $Subscriptiontavia,
	\Modulebazaar\Firstdataapi\Model\SubscriptiontaviaFactory $SubscriptiontaviaFactory,
	\Magento\Sales\Model\Order\Payment\Transaction $paymentTransaction,
	\Magento\Sales\Model\Order\Email\Sender\OrderSender $ordersender,
	\Magento\Sales\Model\OrderNotifier $ordernotifier, ModuleListInterface $moduleList,	
	\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
	\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
	\Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer,
	\Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
	\Magento\Payment\Helper\Data $paymentHelper
) {
    $this->_storeManager = $storeManager;
    $this->_productFactory = $product;
    $this->_formkey = $formkey;
    $this->quote = $quote;
    $this->quoteManagement = $quoteManagement;
    $this->customerFactory = $customerFactory;
    $this->customerRepository = $customerRepository;
    $this->orderService = $orderService;
    $this->_orderRepository = $orderRepository;
	$this->_invoiceService = $invoiceService;
    $this->_transaction = $transaction;
    $this->order = $order;
    $this->_objectManager = $objectmanager;
    $this->productFactory = $productFactory;
    $this->cartRepositoryInterface = $cartRepositoryInterface;
    $this->cartManagementInterface = $cartManagementInterface;
    $this->quoteRepository = $cartRepositoryInterface;
    $this->shippingRate = $shippingRate;
	$this->fdapi  = $fdapi;
	$this->subscriptiontavia  = $Subscriptiontavia;
	$this->_subscriptiontaviaFactory = $SubscriptiontaviaFactory;
	$this->_paymentTransaction = $paymentTransaction;
	$this->ordernotifier = $ordernotifier;
	$this->ordersender = $ordersender;
	$this->inlineTranslation = $inlineTranslation;
	$this->_transportBuilder = $transportBuilder;
	$this->_paymentHelper = $paymentHelper;
	$this->_moduleList = $moduleList;
	$this->identityContainer = $identityContainer;
	$this->addressRenderer = $addressRenderer;
    parent::__construct($context);
}
 
 const MODULE_NAME = 'Modulebazaar_Firstdataapi';
    /**
     * Create Order On Your Store
     * 
     * @param array $orderData
     * @return array
     * 
    */
	  
	public function createOrder($originalOrder, $originalOrderItemId) {
        /**
         * @var $originalOrder \Magento\Sales\Model\Order
         * @var $quote         \Magento\Quote\Model\Quote
         * @var $customer
         */
        // Firstly make sure we can load the customer
        $customerId = $originalOrder->getCustomerId();
        //$customer = $this->_customerFactory->create()->load($customerId);
        $customer = $this->_customerInterface->getById($customerId);
        if ($customer->getEmail()) {
            // Get some data from the original order that we are going to need
            // @todo : load isn't deprecated it might be later
            $originalStoreId = $originalOrder->getStoreId();
			
            $store = $this->_store->getStore($originalStoreId);
            // Create a blank quote
            $quote = $this->_quoteFactory->create()->setStoreId($originalStoreId);

            /**
             * Added by Jaimin
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $quoteRepository = $objectManager->create(\Magento\Quote\Api\CartRepositoryInterface::class);
            $quoteRepository->save($quote);
            /**
             * End
             */
            // Assign the customer to the quote
            $quote->assignCustomer($customer);
            //@todo : fix error with below - must implement interface Magento\Quote\Api\Data\CurrencyInterface
            //$currency = $originalOrder->getBaseCurrencyCode();
            //$quote->setCurrency($currency);
            //temporary - currency needs to be the purchased currency
            $quote->setCurrency();
            // Build Billing Address
            $billingAddress = clone $originalOrder->getBillingAddress();
            $billingAddress->unsetData('entity_id')->unsetData('parent_id')
                    ->unsetData('customer_address_id')->unsetData('customer_id')
                    ->unsetData('quote_address_id');
            // Build Shipping Address
            // @todo : Customers are going to need the ability to change their shipping address come back and fix this once that's done
            $shippingAddress = clone $originalOrder->getShippingAddress();
            $shippingAddress->unsetData('entity_id');
            // Insert the address details
            $quote->getBillingAddress()->addData($billingAddress->getData());
            $quote->getShippingAddress()->addData($shippingAddress->getData());
            /**
             * @var $subscriptionOrderItem \Magento\Sales\Model\Order\Item
             */
            // Load the subscription product from original order
            $subscriptionOrderItem = $originalOrder->getItemById(
                    $originalOrderItemId
            );
            // Make sure we have a product
            if ($subscriptionOrderItem instanceof SalesOrderItem) {
                // Get params required to add product
                $params = $this->getProductParams($subscriptionOrderItem);
                // Load the product
                $product = $this->getSubscriptionProduct(
                        $subscriptionOrderItem, $originalStoreId
                );
                // Product may have been deleted
                if (!$product) {
                   /* checklater $message = sprintf(
                            __('Could not load product id %s for subscription %s'), $subscription->getProductId(), $subscription->getId()
                    );*/
                    $this->_logger->critical($message);
                   // throw new LocalizedException($message);
                }
                //$quote->addProduct($product, $params);
                $this->_quoteInitializer->init($quote, $product, $params);
                // Set the product price based on original order incase product price changed
                try {
                    /* checklater $this->setProductPrices(
                            $quote, $product, $subscription->getProductCost()
                    );*/
                } catch (LocalizedException $e) {
                    $message = __(
                            'Unable set custom price - cannot release subscription'
                    );
                    $this->_logger->critical($message);
                    //throw new LocalizedException($message, $e);
                }
                $quote->setTotalsCollectedFlag(false)->collectTotals();
            } else {
                $message = __(
                                'Could not load original order item for subscription : '
                        );/* checklater  . $subscription->getId();*/
                $this->_logger->critical($message);
               // throw new LocalizedException(__($message));
            }
			
			
			//add items in quote
			foreach($originalOrder->getAllItems() as $item){
				$product=$this->_product->load($item->getProductId());
				$product->setPrice($item->getPrice());
				$quote->addProduct(
					$product,
					intval($item->getQty())
				);
			}
			
			
			
            // Collect shipping rates
            $quote->getShippingAddress()->setCollectShippingRates(true)
                    ->collectShippingRates();
            // Apply the correct shipping rate to this order
            $quote->setShippingRate($quote, $originalOrder);
            // Set payment method - work still to be done here
			
			$payment = $originalOrder->getPayment();
			$method = $payment->getMethodInstance();
			$methodTitle = $method->getCode();
	
            $quote->setPaymentMethod($methodTitle);
            // Create the quote

            /**
             * Change by Jaimin
             */
            //$quote->save();

            /**
             * End
             */
            // Import payment data - Don't understand this yet
            $quote->getPayment()->importData(
                    array('method' => $methodTitle)
            );
            // Collect quote totals
            $quote->collectTotals()->save();
            /**
             * @var $order \Magento\Sales\Model\Order
             */
            // Convert the quote to an order
            $order = $this->_quoteManagement->submit($quote);

            /**
             * Added by Jaimin
             
            $quoteManagement = $objectManager->create(\Magento\Quote\Api\CartManagementInterface::class);
            $quote = $quoteRepository->get($quote->getId());
            $order = $quoteManagement->submit($quote);
            
             * End
             */
            // Prevent default order confirmation, we will make our own
            //$order->setEmailSent(0);
            // Make sure the order has been created properly
          //  if (!$order->getRealOrderId()) {
               // $order = false;
           // }
            /* checklater $this->_logger->info(
                    sprintf(
                            __('Order %s succesfully created for subscription %s'), $order->getRealOrderId(), $subscription->getId()
                    )
            );*/
            return $order;
        } else {
            $message = __('The customer no longer exists for subscription: ')
                   ;/* checklater  . $subscription->getId();*/
            $this->_logger->critical($message);
           // throw new LocalizedException(__($message));
        }
    
	}
	

	public function createOrderNew($orderData) {
		//init the store id and website id @todo pass from array
		$store = $this->_storeManager->getStore();
		$websiteId = $this->_storeManager->getStore()->getWebsiteId();
		//init the customer
		$customer=$this->customerFactory->create();
		$customer->setWebsiteId($websiteId);
		$customer->loadByEmail($orderData['email']);// load customet by email address
		//check the customer
		if(!$customer->getEntityId()){
			//If not avilable then create this customer
			$customer->setWebsiteId($websiteId)
				->setStore($store)
				->setFirstname($orderData['shipping_address']['firstname'])
				->setLastname($orderData['shipping_address']['lastname'])
				->setEmail($orderData['email'])
				->setPassword($orderData['email']);
			$customer->save();
		}
		//init the quote
		$cart_id = $this->cartManagementInterface->createEmptyCart();
		$cart = $this->cartRepositoryInterface->get($cart_id);
		$cart->setStore($store);
		// if you have already buyer id then you can load customer directly
		$customer= $this->customerRepository->getById($customer->getEntityId());
		$cart->setCurrency();
		$cart->assignCustomer($customer); //Assign quote to customer
		$cart->save();
		//add items in quote
		ob_start();
		foreach($orderData['items'] as $item){
			foreach($item as $item) {
				//echo $item['product_id'];
				$product = $this->_productFactory->create()->load($item['product_id']);
				$customOptions = $this->_objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);
				try {
					// print_r($item); die();
					$params = array('product' => $item['product_id'], 'qty' => $item['qty']);
					if (array_key_exists('options', $item) && $item['options']) {
						$params['options'] = json_decode(json_encode($item['options']), True);
					}
					if ($product->getTypeId() == 'configurable') {
						$params['super_attribute'] = $item['super_attribute'];
					} elseif ($product->getTypeId() == 'bundle') {
						$params['bundle_option'] = $item['bundle_option'];
						$params['bundle_option_qty'] = $item['bundle_option_qty'];
					} elseif ($product->getTypeId() == 'grouped') {
						$params['super_group'] = $item['super_group'];
					}

					$objParam = new \Magento\Framework\DataObject();
					$objParam->setData($params);
					$cart->addProduct($product, $objParam);

				} catch (Exception $e) {
					$response[$item['product_id']]= $e->getMessage();
				}
				unset($product);    
			}

		}
		$cart->save();
		ob_flush();
		$cart->getBillingAddress()->addData($orderData['shipping_address']);
		$cart->getShippingAddress()->addData($orderData['shipping_address']);
		// Collect Rates and Set Shipping & Payment Method
		$this->shippingRate
			->setCode('freeshipping_freeshipping')
			->getPrice(1);
		$shippingAddress = $cart->getShippingAddress();
		//@todo set in order data
		$shippingAddress->setCollectShippingRates(true)
			->collectShippingRates()
			->setShippingMethod('flatrate_flatrate'); //shipping method
		//$cart->getShippingAddress()->addShippingRate($this->rate);
		$cart->setPaymentMethod('cashondelivery'); //payment method
		//@todo insert a variable to affect the invetory
		$cart->setInventoryProcessed(false);
		// Set sales order payment
		$cart->getPayment()->importData(['method' => 'cashondelivery']);
		// Collect total and saeve
		$cart->collectTotals();
		// Submit the quote and create the order
		$cart->save();
		$cart = $this->cartRepositoryInterface->get($cart->getId());
		$order_id = $this->cartManagementInterface->placeOrder($cart->getId());
		if($orderData['status'] == 4) {
			return $this->createInvoice($order_id);
		}
		return $order_id;
	}
    public function getTest()
    {
		return 'from license helper';
    }
    public function getVersion()
    {
        return $this->_moduleList
            ->getOne(self::MODULE_NAME)['setup_version'];
    }
    public function createMageOrders($subscription) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();	
		$orderModel = $objectManager->create('\Magento\Sales\Model\Order');
		
		/*** Fetch Required info****/
		$orderId = $subscription->getOrderId();		
		$_order = $orderModel->load($orderId);
		$quote_id = $_order->getQuoteId();
		
		//$quote = $this->cartRepositoryInterface->get($quote_id);
		$quote = $this->cartRepositoryInterface->get($quote_id, [$_order->getStoreId()]);
		$quote->setIsActive(1)->setReservedOrderId('')->save();	
		$quote->getPayment(1)->setMethod('cashondelivery')->save();	
		
		
		$quoteRepository = $objectManager->create(\Magento\Quote\Api\CartRepositoryInterface::class);
		$quoteRepository->save($quote);
		$order_id = $this->cartManagementInterface->placeOrder($quote->getId());	
		
		/******** Fetch info for transaction ******/
		$totals = $quote->getBaseGrandTotal();
		$CcOwner=$subscription->getData('cc_owner');
		$cartType=$subscription->getData('cc_type');
		$expyear = $subscription->getCcExpDate();
		$cvv = $subscription->getCcCvv();
		$transToken = $subscription->getToken();
		
		
		/******** Fetch info for gateway info ******/
		$apiKey = trim($this->scopeConfig->getValue('payment/firstdataapi/gateway_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$apiSecret = trim($this->scopeConfig->getValue('payment/firstdataapi/gateway_pwd', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$token = trim($this->scopeConfig->getValue('payment/firstdataapi/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$transaction_mode     = trim($this->scopeConfig->getValue('payment/firstdataapi/transaction_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$tran_type            = trim($this->scopeConfig->getValue('payment/firstdataapi/transaction_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$api_version          = trim($this->scopeConfig->getValue('payment/firstdataapi/api_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
		$authNum              = '';
		
		$nonce = strval(hexdec(bin2hex(openssl_random_pseudo_bytes(4, $cstrong))));
		$timestamp = strval(time()*1000); //time stamp in milli seconds
		
		if($cartType=="AE"){
			$cartType="American Express";
		}
		if($cartType=="VI"){
			$cartType="Visa";
		}
		if($cartType=="MC"){
			$cartType="Mastercard";
		}
		if($cartType=="DI"){
			$cartType="Discover";
		}
		if($cartType=="JCB"){
			$cartType="JCB";
		}
		if($cartType=="DN"){
			$cartType="Diners Club";
		}
		$datas = array(
				  "transaction_type"=> "recurring",
				  "method"=> "token",
				  "amount"=> round($totals*100),
				  "currency_code"=> "USD",
				  "token"=> array(
					"token_type"=> "FDToken",
					"token_data"=> array(
						  "type"=> $cartType,
						  "cardholder_name"=> $CcOwner,
						  "exp_date"=> $expyear,
						  "cvv"=> $cvv,
						  "special_payment"=> "B",
						  "value"=> $transToken
						)
					)
				);
				
		if ($transaction_mode == 'test') {
                $post_url = 'https://api-cert.payeezy.com/v1/transactions';
            } else {
                $post_url = 'https://api.payeezy.com/v1/transactions';
            }
				
			$payload = json_encode($datas, JSON_FORCE_OBJECT);
			$datum = $apiKey . $nonce . $timestamp . $token . $payload;
			$hashAlgorithm = "sha256";
			### Make sure the HMAC hash is in hex -->
			$hmac = hash_hmac ( $hashAlgorithm , $datum , $apiSecret, false );
			### Authorization : base64 of hmac hash -->
			$hmac_enc = base64_encode($hmac);
			$curl = curl_init($post_url);
			$headers = array(
				  'Content-Type: application/json',
				  'apikey:'.strval($apiKey),
				  'token:'.strval($token),
				  'Authorization:'.$hmac_enc,
				  'nonce:'.$nonce,
				  'timestamp:'.$timestamp,
				);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($curl, CURLOPT_VERBOSE, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$json_response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$result = json_decode($json_response, true);
			
			$process = true;
			$msg ='';
			
			if ( $status != 201 ) {
				$process = false;
				if (array_key_exists("Error",$result)) {
					if (array_key_exists("messages",$result['Error'])) {
						foreach($result['Error']['messages'] as $error){
							 $msg .= $error['description'];
						}
					}
				}
			}
			curl_close($curl);
			
			
			if (array_key_exists('transaction_status',$result)) {
				if ($result['transaction_status'] == "approved") {
					$process = true;
				}
			}
			
			if ($result == "Unauthorized Request. Bad or missing credentials.") {
                //return $this->paymentCrendencialFailure($order);
                return false;
            }
            if ($process) {
				$order = $orderModel->load($order_id);
				
                $this->ordersender->sendConfirmationFinal($order);
				
				$order->setSubcriptionDay($subscription->getDay());
                $order->setState('processing', true, 'Gateway has authorized the payment.');
                $order->setStatus('processing');
                $order->setCanSendNewEmailFlag(1);
                $order->setEmailSent(true);
                $this->ordernotifier->notify($order);
                $order->getPayment()->setMethod('firstdataapi');
				$order->save();
				
                $data=['order_id'=>$order->getId(),'transaction_type'=>$result['transaction_type'],'transaction_tag'=>$result['transaction_tag'],'authorization_num'=>$result['amount'],'bank_response_code'=>$result['bank_resp_code'],'bank_response_msg'=>$result['bank_resp_code'],'client_ip'=>$result['gateway_resp_code'],'ctr'=>$result['gateway_resp_code']] ;
				
                $this->fdapi->setData($data)->save();
				
				$orderList = $subscription->getOrderList();
				$days = $subscription->getDay();
                $_subs = $this->_subscriptiontaviaFactory->create()->load($subscription->getId());
				$nextDate = date('Y-m-d H:i:s',strtotime($_subs->getNextAt()) + (24*3600*$days));
				
				$newToken = $result['token']['token_data']['value'];
				$neworderList = $orderList.','.$order_id;
				$_subs->setToken($newToken)
						->setOrderList($neworderList)
						->setUpdatedAt(date('Y-m-d H:i:s'))
						->setNextAt($nextDate)
						->save();
						
				$supplierEmail     = trim($this->scopeConfig->getValue('payment/firstdataapi/susbscription_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
				$supplierEmailSender     = trim($this->scopeConfig->getValue('payment/firstdataapi/sender_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
				$senderName     = trim($this->scopeConfig->getValue('payment/firstdataapi/sender_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
				$subject     = trim($this->scopeConfig->getValue('payment/firstdataapi/supplieremail_subject', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
				//$order->setCustomerEmail($supplierEmail);
               // $this->ordernotifier->notify($order);
				
				
				$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
				$templateVars = array(
					'order' => $order,
					'billing' => $order->getBillingAddress(),
					'payment_html' => $this->_paymentHelper->getInfoBlockHtml(
										$order->getPayment(),
										$this->identityContainer->getStore()->getStoreId()
										),
					'store' => $order->getStore(),
					'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
					'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
					'customsubject' => $subject,
				);
				/*$templateVars = array(
									'store' => $this->_storeManager->getStore(),
									'customer_name' => 'John Doe',
									'message'   => 'Hello World!!.'
								);*/
				$from = array('email' => $supplierEmailSender, 'name' => $senderName);
				$this->inlineTranslation->suspend();
				$to = array($supplierEmail);
				$transport = $this->_transportBuilder->setTemplateIdentifier('subscription_supplier')
								->setTemplateOptions($templateOptions)
								->setTemplateVars($templateVars)
								->setFrom($from)
								->addTo($to)
								->getTransport();
				$transport->sendMessage();
				$this->inlineTranslation->resume();
				
				if($order->canInvoice()) {
					$invoice = $this->_invoiceService->prepareInvoice($order);
					$invoice->register();
					$invoice->save();
					$transactionSave = $this->_transaction->addObject(
						$invoice
					)->addObject(
						$invoice->getOrder()
					);
					$transactionSave->save();
					$invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
					$invoiceSender->send($invoice);
					//send notification code
					$order->addStatusHistoryComment(
						__('Notified customer about invoice #%1.', $invoice->getId())
					)
					->setIsCustomerNotified(true)
					->save();
				} 
				$transData=['order_id'=>$order->getId(),'payment_id'=>$order->getPayment()->getId(),'txn_id'=>$result['transaction_id'],'txn_type'=>'capture','is_closed'=>1,'created_at'=>date('Y-m-d H:i:s')] ;
				$this->_paymentTransaction->setData($transData)->save();
						
                return $order_id;
            } else {
				if ( $status == 201 ) {
					$msg         = $result['gateway_message'];
				}
				$order = $orderModel->load($order_id);
                $order->getPayment()->setMethod('firstdataapi');
                $msg         = $result['gateway_message'];
                if ($order->getId()) {
                    if ($order->getState() != 'canceled') {
                        $order->registerCancellation($msg)->save();
                    }                    
                    return false;
                }
            }
		
		
		
		
		
        return $order_id;
	}
	
    public function invoiceOrder($order) {		
				
		if($order->canInvoice()) {
			$invoice = $this->_invoiceService->prepareInvoice($order);
			$invoice->register();
			$invoice->save();
			$transactionSave = $this->_transaction->addObject(
				$invoice
			)->addObject(
				$invoice->getOrder()
			);
			$transactionSave->save();
			$invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
			$invoiceSender->send($invoice);
			//send notification code
			$order->addStatusHistoryComment(
				__('Notified customer about invoice #%1.', $invoice->getId())
			)
			->setIsCustomerNotified(true)
			->save();
		}
	}
	
    public function transactOrder($transData) {	
		$this->_paymentTransaction->setData($transData)->save();	
	}
	
    public function createMageOrder($orderData) {
        $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customet by email address
        if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        }
         
        $cartId = $this->cartManagementInterface->createEmptyCart(); //Create empty cart
        $quote = $this->cartRepositoryInterface->get($cartId); // load empty cart quote
        $quote->setStore($store);
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
 
        //add items in quote
        foreach($orderData['items'] as $item){
            $product=$this->_product->load($item['product_id']);
            $product->setPrice(10);
            $quote->addProduct($product, intval($item['qty']));
        }
		$quote->save();
        //Set Address to quote
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
 
        //	 Collect Rates and Set Shipping & Payment Method
		 $this->shippingRate
				->setCode('freeshipping_freeshipping')
				->getPrice(1);
		
		$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('freeshipping_freeshipping'); //shipping method
        $quote->setPaymentMethod('cashondelivery'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
 
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'cashondelivery']);
        $quote->save(); //Now Save quote and your quote is ready
 
        // Collect Totals
        $quote->collectTotals();
 
 
 
        // Create Order From Quote
        $quote = $this->cartRepositoryInterface->get($quote->getId());
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->order->load($orderId);
        
        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'Your custom message'];
        }
        return $result;
    }
	protected function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }   
	protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }
	
    public function checklicense()
    {
		return 1;die();
        $input = [];
        $shopdomain =$this->remotehost->getRemoteHost();
        $sku        = 'FDAPI100';
        if ($shopdomain == 'localhost' || $shopdomain == '127.0.0.1') {
            return 1;
        }
        $url        = "http://www.dropshipmodule.com/user-license-validation.php";
        $input['domain']=$shopdomain;
        $input['module']=$sku;
        $this->_curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        $this->_curl->setOption(CURLOPT_VERBOSE, 1);
        $this->_curl->setOption(CURLOPT_SSL_VERIFYHOST, 0);
        $this->_curl->setOption(CURLOPT_RETURNTRANSFER, 1);
        
        $this->_curl->post($url, $input);
        $httpResponse = $this->_curl->getBody();
        
        if (!$httpResponse) {
            $response = "error";
        } else {
            $response = $httpResponse;
        }
        
        $p_res = json_decode($response);
        
        $r_status = strtolower($p_res->status);
        
        if ($r_status == "active") {
            return 1;
        } else {
            return 0;
        }
    }
}
