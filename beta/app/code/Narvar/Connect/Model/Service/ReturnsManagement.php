<?php
/**
 * Returns Management Service Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Service;

use Narvar\Connect\Api\ReturnsManagementInterface;
use Narvar\Connect\Model\Service\Response as NarvarResponse;
use Narvar\Connect\Model\Returns\Community;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Webapi\Rest\Response;

class ReturnsManagement implements ReturnsManagementInterface
{
    /**
     *  Resource Not Found Message
     */
    const RESOURCE_NOT_FOUND = 'Resource Not Found';

    /**
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     *
     * @var \Narvar\Connect\Model\Service\Response
     */
    private $narvarApiResponse;

    /**
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    private $itemCollectionFactory;
    
    /**
     *
     * @var \Narvar\Connect\Model\Returns\Community
     */
    private $returnCommunity;
    
    /**
     *
     * @var \Magento\Framework\Webapi\Rest\Response
     */
    private $restResponse;
    
    /**
     * Constructor
     *
     * @param OrderFactory $orderFactory
     * @param NarvarResponse $narvarApiResponse
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param Community $returnCommunity
     * @param Response $restResponse
     */
    public function __construct(
        OrderFactory $orderFactory,
        NarvarResponse $narvarApiResponse,
        ItemCollectionFactory $itemCollectionFactory,
        Community $returnCommunity,
        Response $restResponse
    ) {
        $this->orderFactory = $orderFactory;
        $this->narvarApiResponse = $narvarApiResponse;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->returnCommunity = $returnCommunity;
        $this->restResponse = $restResponse;
    }

    /**
     *
     * @see \Narvar\Connect\Api\ReturnsManagementInterface::createReturn()
     */
    public function createReturn($orderNumber, $dateRequested, $orderItems)
    {
        $this->processReturn($orderNumber, $dateRequested, $orderItems);
        $this->restResponse->setHttpResponseCode($this->narvarApiResponse->getStatusCode());
        
        return $this->narvarApiResponse->getResponse();
    }

    /**
     * Method to process the return based on magento edition
     *
     * @param string $orderNumber
     * @param string $dateRequested
     * @param \Narvar\Connect\Api\Data\ReturnsItemsInterface[] $orderItems
     * @return mixed
     */
    private function processReturn($orderNumber, $dateRequested, $orderItems)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderNumber);
        
        if ($order->getId()) {
            $orderItemsData = $this->getOrderItemDetails($order->getId(), $orderItems);

            if (count($orderItemsData) != count($orderItems)) {
                return;
            }
            
            $this->returnCommunity->process($order, $orderItemsData, $this->narvarApiResponse, $dateRequested);
            
            return;
        }
        
        $this->narvarApiResponse->addNarvarErrorMessage(
            __('Order %1 %2', $orderNumber, self::RESOURCE_NOT_FOUND),
            WebApiException::HTTP_NOT_FOUND
        );
    }

    /**
     * Method to verify the order items are exist and it belongs to specific order
     *
     * @param int $orderId
     * @param \Narvar\Connect\Api\Data\ReturnsItemsInterface[] $orderItems
     * @return array
     */
    private function getOrderItemDetails($orderId, $orderItems)
    {
        $returnOrderItems = [];
        foreach ($orderItems as $key => $orderItem) {
            $orderItemData = $this->getOrderItem($orderId, $orderItem->getItemSku());
            
            if ($orderItemData->getId()) {
                $returnOrderItems[$key]['name'] = $orderItemData->getProduct()->getName();
                $returnOrderItems[$key]['order_item_id'] = $orderItemData->getId();
                if ($orderItemData->getParentItemId()) {
                    $returnOrderItems[$key]['order_item_id'] = $orderItemData->getParentItemId();
                }
                $returnOrderItems[$key]['item_sku'] = $orderItem->getItemSku();
                $returnOrderItems[$key]['qty'] = $orderItem->getQty();
                $returnOrderItems[$key]['condition'] = $orderItem->getCondition();
                $returnOrderItems[$key]['resolution'] = $orderItem->getResolution();
                $returnOrderItems[$key]['reason'] = $orderItem->getReason();
                $returnOrderItems[$key]['comment'] = $orderItem->getComment();
            } else {
                $this->narvarApiResponse->addNarvarErrorMessage(
                    __('Order Item %1 %2', $orderItem->getItemSku(), self::RESOURCE_NOT_FOUND),
                    WebApiException::HTTP_NOT_FOUND
                );
            }
        }
        
        return $returnOrderItems;
    }

    /**
     * Method to get the Order Item Information
     *
     * @param int $orderId
     * @param string $sku
     */
    private function getOrderItem($orderId, $sku)
    {
        return $this->itemCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('sku', $sku)
            ->addFieldToFilter('product_type', ProductType::TYPE_SIMPLE)
            ->setPageSize(1)
            ->getFirstItem();
    }
}
