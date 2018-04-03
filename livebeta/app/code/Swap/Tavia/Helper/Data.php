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
    public function createNew() {
		return 'hi';
	}
    public function createOrders($orderData) {
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
            
//            print_r($item);
        }
//die("sad");
        foreach ($orderData['items'] as $item) {
            
//            print_r($item);
            $product = $this->_product->load($item['product_id']);
//            $product->setPrice($item['price']);
            $quote->addProduct(
                    $product, intval($item['qty'])
            );
//        print_r($quote-());
        }
//        die("ASd");
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
	
	
public function createOrder(Subscriptions $subscription, $originalOrder, $originalOrderItemId) {
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
            $store = $this->_store->load($originalStoreId);
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
                    $message = sprintf(
                            __('Could not load product id %s for subscription %s'), $subscription->getProductId(), $subscription->getId()
                    );
                    $this->_logger->critical($message);
                    throw new LocalizedException($message);
                }
                //$quote->addProduct($product, $params);
                $this->_quoteInitializer->init($quote, $product, $params);
                // Set the product price based on original order incase product price changed
                try {
                    $this->setProductPrices(
                            $quote, $product, $subscription->getProductCost()
                    );
                } catch (LocalizedException $e) {
                    $message = __(
                            'Unable set custom price - cannot release subscription'
                    );
                    $this->_logger->critical($message);
                    throw new LocalizedException($message, $e);
                }
                $quote->setTotalsCollectedFlag(false)->collectTotals();
            } else {
                $message = __(
                                'Could not load original order item for subscription : '
                        ) . $subscription->getId();
                $this->_logger->critical($message);
                throw new LocalizedException(__($message));
            }
            // Collect shipping rates
            $quote->getShippingAddress()->setCollectShippingRates(true)
                    ->collectShippingRates();
            // Apply the correct shipping rate to this order
            $this->getShippingRate($quote, $originalOrder);
            // Set payment method - work still to be done here
            $quote->setPaymentMethod($this->_getPaymentMethod());
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
                    array('method' => $this->_getPaymentMethod())
            );
            // Collect quote totals
            $quote->collectTotals()->save();
            /**
             * @var $order \Magento\Sales\Model\Order
             */
            // Convert the quote to an order
            //$order = $this->_quoteManagement->submit($quote);

            /**
             * Added by Jaimin
             */
            $quoteManagement = $objectManager->create(\Magento\Quote\Api\CartManagementInterface::class);
            $quote = $quoteRepository->get($quote->getId());
            $order = $quoteManagement->submit($quote);
            /**
             * End
             */
            // Prevent default order confirmation, we will make our own
            $order->setEmailSent(0);
            // Make sure the order has been created properly
            if (!$order->getRealOrderId()) {
                $order = false;
            }
            $this->_logger->info(
                    sprintf(
                            __('Order %s succesfully created for subscription %s'), $order->getRealOrderId(), $subscription->getId()
                    )
            );
            return $order;
        } else {
            $message = __('The customer no longer exists for subscription: ')
                    . $subscription->getId();
            $this->_logger->critical($message);
            throw new LocalizedException(__($message));
        }
    }

}
