<?php
/**
 * Data Transformer Object Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Data;

use Magento\Customer\Model\Customer;

class DTO
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    private $shipment;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;
    
    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customerModel;

    /**
     * Consturctor
     *
     * @param Customer $customerRepositoryInterface
     */
    public function __construct(
        Customer $customerModel
    ) {
        $this->customerModel = $customerModel;
    }
    
    /**
     * Method to Set DTO Object
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->setOrder($data['order']);
        $this->setShipment($data['shipment']);
        $this->setCustomer();
    }

    /**
     * Method to set the Order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function setOrder($order)
    {
        return $this->order = $order;
    }
    
    /**
     * Method to get the Order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * Method to set the Shipment
     *
     * @return \Magento\Sales\Model\Order\Shipment|NULL
     */
    public function setShipment($shipment)
    {
        $this->shipment = $shipment;
    }
    
    /**
     * Method to get the Shipment
     *
     * @return \Magento\Sales\Model\Order\Shipment|NULL
     */
    public function getShipment()
    {
        return $this->shipment;
    }
    
    /**
     * Method to set the customer
     */
    public function setCustomer()
    {
        $this->customer = $this->customerModel->setStore($this->order->getStore())
        ->loadByEmail($this->order->getCustomerEmail());
    }

    /**
     * Method to get the Customer
     *
     * @return \Magento\Customer\Model\Customer|NULL
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
