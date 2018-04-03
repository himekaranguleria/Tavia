<?php
/**
 * Data Transformer Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Data;

use Narvar\Connect\Exception\ConnectorException;
use Narvar\Connect\Helper\Audit\Type as AuditType;
use Narvar\Connect\Helper\Audit\Action as AuditAction;
use Narvar\Connect\Helper\Audit\Log as AuditLogHelper;
use Narvar\Connect\Helper\Audit\Status as AuditStatusHelper;
use Narvar\Connect\Helper\ConnectorFactory;
use Narvar\Connect\Helper\Config\Activation;
use Narvar\Connect\Model\Data\DTO;
use Narvar\Connect\Helper\Payment;
use Narvar\Connect\Model\Data\Transformer\Order as OrderTransformer;
use Narvar\Connect\Model\Data\Transformer\Order\Items as OrderItemsTransformer;
use Narvar\Connect\Model\Data\Transformer\Customer as CustomerTransformer;
use Narvar\Connect\Model\Data\Transformer\Billing as BillingTransformer;
use Narvar\Connect\Model\Data\Transformer\Address\Location as AddressLocation;
use Narvar\Connect\Model\Data\Transformer\Address\Billing as BillingAddress;
use Narvar\Connect\Model\Data\Transformer\Address\Shipping as ShippingAddress;
use Narvar\Connect\Model\Data\Transformer\Shipments as ShipmentsTransformer;
use Narvar\Connect\Model\Delta\Validator as DeltaValidator;
use Narvar\Connect\Model\Audit\Log as AuditLog;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Event\Manager as EventManager;

class Transformer
{

    /**
     * Slug value for Order Creation API
     */
    const ORDER_SLUG = '/orders/';

    /**
     * Slug value for Order Shipment Creation API
     */
    const SHIPMENT_SLUG = '/shipments/';

    /**
     * @var \Narvar\Connect\Helper\Config\Activation
     */
    private $activationHelper;

    /**
     * @var \Narvar\Connect\Model\Data\DTO
     */
    private $dto;

    /**
     * @var \Narvar\Connect\Helper\Payment
     */
    private $paymentHelper;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Order
     */
    private $orderTransformer;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Customer
     */
    private $customerTransformer;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Shipments
     */
    private $shipmentsTransformer;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Address\Location
     */
    private $addressLocation;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Address\Billing
     */
    private $billingAddress;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Address\Shipping
     */
    private $shippingAddress;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Billing
     */
    private $billing;

    /**
     * @var \Narvar\Connect\Model\Data\Transformer\Order\Items
     */
    private $orderItemsTransformer;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Narvar\Connect\Helper\Audit\Log
     */
    private $auditLogHelper;

    /**
     * @var \Narvar\Connect\Model\Delta\Validator
     */
    private $deltaValidator;

    /**
     * @var \Narvar\Connect\Helper\Audit\Status
     */
    private $auditStatusHelper;

    /**
     * @var \Narvar\Connect\Helper\Connector
     */
    private $connector = null;
    
    /**
     * @var \Magento\Framework\Event\Manager
     */
    private $eventManager;

    /**
     * Constructor
     *
     * @param Activation $activationHelper
     * @param DTO $dto
     * @param Payment $paymentHelper
     * @param OrderTransformer $orderTransformer
     * @param OrderItemsTransformer $orderItemsTransformer
     * @param CustomerTransformer $customerTransformer
     * @param AddressLocation $addressLocation
     * @param BillingAddress $billingAddress
     * @param BillingTransformer $billing
     * @param ShipmentsTransformer $shipmentsTransformer
     * @param ShippingAddress $shippingAddress
     * @param JsonHelper $jsonHelper
     * @param AuditLogHelper $auditLogHelper
     * @param AuditStatusHelper $auditStatusHelper
     * @param DeltaValidator $deltaValidator
     * @param ConnectorFactory $connector
     * @param $eventManager $eventManager
     */
    public function __construct(
        Activation $activationHelper,
        DTO $dto,
        Payment $paymentHelper,
        OrderTransformer $orderTransformer,
        OrderItemsTransformer $orderItemsTransformer,
        CustomerTransformer $customerTransformer,
        AddressLocation $addressLocation,
        BillingAddress $billingAddress,
        BillingTransformer $billing,
        ShipmentsTransformer $shipmentsTransformer,
        ShippingAddress $shippingAddress,
        JsonHelper $jsonHelper,
        AuditLogHelper $auditLogHelper,
        AuditStatusHelper $auditStatusHelper,
        DeltaValidator $deltaValidator,
        ConnectorFactory $connector,
        EventManager $eventManager
    ) {
        $this->activationHelper = $activationHelper;
        $this->dto = $dto;
        $this->paymentHelper = $paymentHelper;
        $this->orderTransformer = $orderTransformer;
        $this->customerTransformer = $customerTransformer;
        $this->addressLocation = $addressLocation;
        $this->billingAddress = $billingAddress;
        $this->billing = $billing;
        $this->orderItemsTransformer = $orderItemsTransformer;
        $this->shipmentsTransformer = $shipmentsTransformer;
        $this->shippingAddress = $shippingAddress;
        $this->jsonHelper = $jsonHelper;
        $this->auditLogHelper = $auditLogHelper;
        $this->deltaValidator = $deltaValidator;
        $this->auditStatusHelper = $auditStatusHelper;
        $this->connector = $connector;
        $this->eventManager = $eventManager;
    }

    /**
     * Method to check entity type and call respective
     *
     * @param string $entityType
     * @param multitype $entityType
     */
    public function transform($entityType, $data)
    {
        if (! $this->activationHelper->getIsActivated()) {
            return;
        }
        
        $this->dto->setData($data);
        
        if ($entityType === AuditType::ENT_TYPE_ORDER) {
            $this->transformOrder($entityType);
        }
        
        if ($entityType === AuditType::ENT_TYPE_SHIPMENT) {
            $this->transformShipment($entityType);
        }
    }

    /**
     * Method to transform order data
     * by using order, billing address, address location and order items transformer
     * And call the post api
     *
     * @param string $entityType
     */
    public function transformOrder($entityType)
    {
        if (! $this->isPaymentSuccess($this->dto->getOrder())) {
            return;
        }
                
        $requestData = $this->prepareOrder();
        
        $this->processTransform($entityType, AuditAction::ACTION_CREATE, self::ORDER_SLUG, $requestData);
    }

    /**
     * Method to transform shipping data
     * by using shipment and shipping address transformer
     * and call the update api
     *
     * @param string $entityType
     */
    public function transformShipment($entityType)
    {
        if (count($this->dto->getShipment()->getAllTracks()) <= 0) {
            return;
        }
        
        $updateSlug = sprintf(
            '%s%s%s',
            self::ORDER_SLUG,
            $this->dto->getOrder()->getIncrementId(),
            self::SHIPMENT_SLUG
        );
        
        $shipmentInfo = $this->prepareShipment();
        if (!empty($shipmentInfo)) {
            $requestData = $this->jsonHelper->jsonEncode($shipmentInfo);
            $this->processTransform($entityType, AuditAction::ACTION_UPDATE, $updateSlug, $requestData);
            $this->eventManager->dispatch('sales_order_save_after', ['order' => $this->dto->getOrder()]);
        }
    }

    /**
     * Method to process transform the data to narvar and give entry in log data
     *
     * @param string $entityType
     * @param string $action
     * @param string $slug
     * @param json $requestData
     */
    private function processTransform($entityType, $action, $slug, $requestData)
    {
        $orderId = $this->dto->getOrder()->getId();
        $lastRequestData = $this->auditLogHelper->lastCallRequestData($orderId, $entityType);
        $validate = $this->deltaValidator->isIdentical($lastRequestData, $requestData);
        if (! $validate) {
            $logData = $this->prepareLog($entityType, $action, $slug, $requestData);
            
            if ($this->auditLogHelper->hasFailures($orderId)) {
                $logData[AuditLog::RESPONSE] = __('Previous Order Data is not pushed to narvar');
                $logData[AuditLog::STATUS] = $this->auditStatusHelper->getFailure();
                $logModel = $this->auditLogHelper->create($logData);
            } else {
                $logModel = $this->auditLogHelper->create($logData);
                $this->callApiWithRetry($logModel, true);
            }
        }
    }

    /**
     * Method to prepare the log data for new record
     *
     * @param string $entityType
     * @param string $action
     * @param string $slug
     * @param string $requestData
     * @return multitype:NULL unknown string mixed
     */
    private function prepareLog($entityType, $action, $slug, $requestData)
    {
        return [
            AuditLog::ORDER_ID => $this->dto->getOrder()->getId(),
            AuditLog::ORDER_INC_ID => $this->dto->getOrder()->getIncrementId(),
            AuditLog::ENT_TYPE => $entityType,
            AuditLog::ACTION => $action,
            AuditLog::STATUS => $this->auditStatusHelper->getPending(),
            AuditLog::SLUG => $slug,
            AuditLog::REQ_DATA => $requestData,
            AuditLog::RESPONSE => null
        ];
    }

    /**
     * Method to verify if payment is success or not
     *
     * @param Mage_Sales_Model_Order $order
     * @return boolean
     */
    private function isPaymentSuccess(\Magento\Sales\Model\Order $order)
    {
        $payment = $order->getPayment();
        $paymentMethodCode = $payment->getMethodInstance()->getCode();
        
        $offlinePaymentMethods = $this->paymentHelper->getOfflinePayMethods();
        $baseTotalPaid = $order->getBaseTotalPaid();
        if (in_array($paymentMethodCode, $offlinePaymentMethods)) {
            return $baseTotalPaid > 0 ? true : false;
        } else {
            return ($baseTotalPaid > 0 && $baseTotalPaid == $payment->getAmountOrdered()) ? true : false;
        }
    }

    /**
     * Method to prepare the order data into required API Format
     *
     * @return json
     */
    private function prepareOrder()
    {
        $order = $this->orderTransformer->transform($this->dto);
        
        $customer = [
            'customer' => array_merge(
                $this->customerTransformer->transform($this->dto),
                $this->addressLocation->transform($this->dto)
            )
        ];
        $billing = [
            'billing' => array_merge(
                $this->billing->transform($this->dto),
                $this->billingAddress->transform($this->dto)
            )
        ];
        $orderItems = $this->orderItemsTransformer->transform($this->dto);
        $orderInfo = [
            'order_info' => array_merge($order, $customer, $billing, $orderItems)
        ];
        
        return $this->jsonHelper->jsonEncode($orderInfo);
    }

    /**
     * Method to prepare the shipment data into required API Format
     *
     * @return array
     */
    private function prepareShipment()
    {
        $shipmentInfo = [];
        $shipments = $this->shipmentsTransformer->transform($this->dto);
        if (! empty($shipments['shipments'])) {
            $shippingAddress = $this->shippingAddress->transform($this->dto);
            foreach ($shipments['shipments'] as $key => $shipment) {
                $shipments['shipments'][$key]['shipped_to'] = $shippingAddress['shipped_to'];
            }
            $shipmentInfo['order_info'] = $shipments;
        }
        
        return $shipmentInfo;
    }

    /**
     * Method to call the Post/Put API with given parameter and retry mechanism will work for once
     *
     * @param AuditLog $logModel
     * @param boolean $retry
     */
    public function callApiWithRetry(AuditLog $logModel, $retry = false)
    {
        $this->auditLogHelper->updateProcessing($logModel);
        try {
            if ($logModel->getAction() === AuditAction::ACTION_CREATE) {
                $responseMsg = $this->connector->create()->post($logModel->getSlug(), $logModel->getRequestData());
            }
            
            if ($logModel->getAction() === AuditAction::ACTION_UPDATE) {
                $responseMsg = $this->connector->create()->put($logModel->getSlug(), $logModel->getRequestData());
            }
            
            $this->auditLogHelper->updateSuccess($logModel, $responseMsg);
        } catch (ConnectorException $e) {
            $this->auditLogHelper->updateFailure($logModel, $e->getMessage());
            if ($retry) {
                $this->auditLogHelper->updateOnHold($logModel, $e->getMessage());
                $this->callApiWithRetry($logModel);
            }
        }
    }
}
