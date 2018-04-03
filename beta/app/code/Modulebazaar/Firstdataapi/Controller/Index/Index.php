<?php
namespace Modulebazaar\Firstdataapi\Controller\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
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
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Escaper $escaper, \Magento\Framework\HTTP\Client\Curl $curl, \Magento\Quote\Model\Quote $quote, \Magento\Sales\Model\Order $order, \Magento\Checkout\Model\Session $session, \Modulebazaar\Firstdataapi\Helper\License $license, \Magento\Framework\App\ResourceConnection $resources, \Magento\Sales\Model\Order\Email\Sender\OrderSender $ordersender, \Magento\Sales\Model\OrderNotifier $ordernotifier, \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remotehost, \Modulebazaar\Firstdataapi\Model\FirstdataApi $fdapi, \Modulebazaar\Firstdataapi\Model\Subscriptiontavia $Subscriptiontavia)
    {
        parent::__construct($context);
        $this->remotehost = $remotehost;
        $this->scopeConfig  = $scopeConfig;
        $this->quote  = $quote;
        $this->fdapi  = $fdapi;
        $this->subscriptiontavia  = $Subscriptiontavia;
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

        $allow       = $this->license->checklicense();
        if ($allow) {
            $quote           = $this->session->getLastRealOrder();
            $order           = $this->order->loadByIncrementId($quote->getData('increment_id'));
            $billingaddress  = $order->getBillingAddress();
            $shippingaddress = $order->getShippingAddress();
            $currencyDesc    = $order->getOrderCurrencyCode();
            $totals          = $order->getGrandTotal();
            if (!empty($billingaddress)) {
                $address = $billingaddress->getStreet();
            } else {
                $address = '';
            }
            if (!empty($shippingaddress)) {
                $address1 = $shippingaddress->getStreet();
            } else {
                $address1 = '';
            }
            $month = $order->getPayment()->getCcExpMonth();
            $year  = substr($order->getPayment()->getCcExpYear(), 2, 2);
            if (strlen($month) == 1) {
                $expyear = "0" . $month . $year;
            } else {
                $expyear = $month . "" . $year;
            }
            
            $data = [
                'vpc_Version' => 1,
                'vpc_Command' => 'firstdata',
                'vpc_Merchant' => trim($this->scopeConfig->getValue('payment/firstdataapi/gateway_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
                'vpc_AccessCode' => trim($this->scopeConfig->getValue('payment/firstdataapi/gateway_pwd', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
                'vpc_Amount' => $totals,
                'vpc_CardName' => 'firstdataapi',
                'vpc_Cardtypename' => $order->getPayment()->getData('cc_type'),
                'vpc_CardNum' => $this->session->getCcnumber(),
                'vpc_CardExp' => $expyear,
                'vpc_Message' => 'transaction',
                'vpc_MerchTxnRef' => 'test',
                'billing_cust_name' => $order->getCustomerFirstname(),
                'billing_last_name' => $order->getCustomerLastname(),
                'billing_cust_tel_No' => $billingaddress->getTelephone(),
                'billing_cust_email' => $order->getCustomerEmail(),
                'billing_cust_address' => '',
                'billing_cust_city' => $billingaddress->getCity(),
                'billing_cust_country' => $billingaddress->getCountryId(),
                'billing_cust_state' => $billingaddress->getRegion(),
                'billing_cust_zip' => $billingaddress->getPostcode(),
                'Order_Id' => $order->getIncrementId()
            ];
            
            $cvv = $this->session->getCcid();
            if ($cvv) {
                $data['vpc_CardSecurityCode'] = $cvv;
            }
            $data['vpc_Currency'] = $currencyDesc;
			
			
				
			$apiKey = trim($this->scopeConfig->getValue('payment/firstdataapi/gateway_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
			$apiSecret = trim($this->scopeConfig->getValue('payment/firstdataapi/gateway_pwd', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
			$token = trim($this->scopeConfig->getValue('payment/firstdataapi/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $transaction_mode     = trim($this->scopeConfig->getValue('payment/firstdataapi/transaction_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $tran_type            = trim($this->scopeConfig->getValue('payment/firstdataapi/transaction_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $api_version          = trim($this->scopeConfig->getValue('payment/firstdataapi/api_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $authNum              = '';
            $amount               = str_replace(",", "", number_format($totals, 2));
			
			$nonce = strval(hexdec(bin2hex(openssl_random_pseudo_bytes(4, $cstrong))));
			$timestamp = strval(time()*1000); //time stamp in milli seconds
			$cartType=$order->getPayment()->getData('cc_type');
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
              'merchant_ref'=> 'sample txn',
              'transaction_type'=> "recurring",
              'method'=> 'credit_card',
              'amount'=> round($totals*100),
              'currency_code'=> $currencyDesc,
              'credit_card'=> array(
                      'type'=> $cartType,
                      'cardholder_name'=> 'JohnSmith',
                      'card_number'=> $this->session->getCcnumber(),
                      'exp_date'=> $expyear,
                      'cvv'=> $cvv,
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
			//var_dump($datas);
			//var_dump($result);
			//die();
			if ( $status != 201 ) {
				//var_dump($result);
				//die("Error: call to URL $post_url  failed with status $payload $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
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
			//echo "JSON response is: ".$json_response."\n";
			
			if (array_key_exists('transaction_status',$result)) {
				if ($result['transaction_status'] == "approved") {
					$process = true;
				}
			}

            if ($result == "Unauthorized Request. Bad or missing credentials.") {
                return $this->paymentCrendencialFailure($order);
            }
            if ($process) {
                $order         = $this->order->loadByIncrementId($quote->getData('increment_id'));

                $data=['order_id'=>$result['correlation_id'],'transaction_type'=>$result['transaction_type'],'transaction_tag'=>$result['transaction_tag'],'authorization_num'=>$result['amount'],'bank_response_code'=>$result['bank_resp_code'],'bank_response_msg'=>$result['bank_resp_code'],'client_ip'=>$result['gateway_resp_code'],'ctr'=>$result['gateway_resp_code']] ;
                $this->fdapi->setData($data)->save();
				
				$day = 0;
				if(!is_null($this->session->getSusbcriptionDays())){
					$day = $this->session->getSusbcriptionDays();
					$this->session->unsSusbcriptionDays();						
				}				
				if($day>0){
					$nextDate = date('Y-m-d H:i:s',strtotime($order->getCreatedAt()) + (24*3600*$day));
					$subsData=['order_id'=>$order->getId(),'cc_owner'=>$result['card']['cardholder_name'],'cc_last_four'=>$result['card']['card_number'],'cc_exp_date'=>$result['card']['exp_date'],'cc_cvv'=>$cvv,'cc_type'=>$result['card']['type'],'order_number'=>$order->getIncrementId(),'order_amount'=>$totals,'order_list'=>$order->getId(),'day'=>$day,'token'=>$result['token']['token_data']['value'],'customer_id'=>$order->getCustomerId(),'status'=>1,'created_at'=>$order->getCreatedAt(),'updated_at'=>date('Y-m-d H:i:s'),'next_at'=>$nextDate] ;
					$this->subscriptiontavia->setData($subsData)->save();
					$order->setSubcriptionDay($day);
				}
				
                $this->ordersender->sendConfirmationFinal($order);
                $order->setState('processing', true, 'Gateway has authorized the payment.');
                $order->setCanSendNewEmailFlag(1);
                $order->setEmailSent(true);
                $this->ordernotifier->notify($order);
				
				$this->license->invoiceOrder($order);
				
				$transData=['order_id'=>$order->getId(),'payment_id'=>$order->getPayment()->getId(),'txn_id'=>$result['transaction_id'],'txn_type'=>'capture','is_closed'=>1,'created_at'=>date('Y-m-d H:i:s')] ;
				
				$this->license->transactOrder($transData);
				
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('checkout/onepage/success');
                return $resultRedirect;
            } else {
				if ( $status == 201 ) {
					$msg         = $result['gateway_message'];
				}
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
                    //$this->messageManager->addError($msg);
                    
                    return $resultRedirect;
                }
            }
        } else {
            $quote = $this->session->getLastRealOrder();
            
            $order = $this->order->loadByIncrementId($quote->getData('increment_id'));
            
            if ($order->getId()) {
                if ($order->getState() != 'canceled') {
                    $order->registerCancellation('Pls add domain license in backend')->save();
                }
                $quotes = $this->quote->load($order->getQuoteId());
                if ($quotes->getId()) {
                    $quotes->setIsActive(1)->setReservedOrderId(null)->save();

                    $this->session->replaceQuote($quotes);
                }
                $this->session->unsLastRealOrderId();
                
                
                $this->messageManager->addError('Pls add domain license in backend');
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('checkout/cart/index');
                return $resultRedirect;
            }
        }
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
