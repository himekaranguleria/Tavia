<?php

namespace Swap\Tavia\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_storeManager;
    protected $_product;
    protected $_formkey;
    protected $quote;
    protected $quoteManagement;
    protected $customerFactory;
    protected $customerRepository;
    protected $cartRepositoryInterface;
    protected $cartManagementInterface;
    protected $orderService;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Product $product, \Magento\Quote\Model\QuoteFactory $quote, \Magento\Quote\Model\QuoteManagement $quoteManagement, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Sales\Model\Service\OrderService $orderService, \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface, \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
    ) {
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
    }

    /**
     * Create Order On Your Store
     *
     * @param array $orderData
     * @return array
     *
     */
    public function createOrder($orderData) {
        //init the store id and website id @todo pass from array
        $store = $this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        //init the customer
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']); // load customet by email address
        //check the customer
        if (!$customer->getEntityId()) {
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
        $quote = $this->quote->create();
        $quote->setStore($store);
        // if you have already buyer id then you can load customer directly
        $customer = $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $this->cartRepositoryInterface->save($quote);
        $quote->assignCustomer($customer); //Assign quote to customer
        //add items in quote
        foreach ($orderData['items'] as $item) {
            $product = $this->_product->load($item['product_id']);
            $product->setPrice(22);
            $quote->addProduct(
                    $product, intval($item['qty'])
            );
        }
        //Set Address to quote @todo add section in order data for seperate billing and handle it
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress = $quote->getShippingAddress();
        //@todo set in order data
        $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod('flatrate_flatrate'); //shipping method
        $quote->setPaymentMethod('checkmo'); //payment method
        //@todo insert a variable to affect the invetory
        $quote->setInventoryProcessed(false);
        // Set sales order payment
        $quote->getPayment()->importData(['method' => 'checkmo']);
//        $quote->save();
        $this->cartRepositoryInterface->save($quote);
        // Collect total and saeve
        $quote->collectTotals();
          // Submit the quote and create the order
        $quote->save();
//        $quote = $this->cartRepositoryInterface->get($quote->getId());
//        $order_id = $this->cartManagementInterface->placeOrder($quote->getId());

//        return $order_id;
        $order = $this->quoteManagement->submit($quote);
//        print_r($order);
        // Submit the quote and create the order
//        $quote = $this->cartRepositoryInterface->get($order->getId());
//        $order = $this->quoteManagement->placeOrder($order->getId());
        //do not send the email
//        $order->setEmailSent(0);
        //give a result back
        if ($order->getEntityId()) {
            $result['order_id'] = $order->getEntityId();
        } else {
            $result = ['error' => 1, 'msg' => 'Your custom message'];
        }
        return $result;
    }

}
