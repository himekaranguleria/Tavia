<?php
/**
 * Shipment Data Transformer
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Data\Transformer;

use Magento\Directory\Model\Country as CountryModel;
use Narvar\Connect\Helper\Formatter;
use Narvar\Connect\Helper\Config\Status as OrderStatusHelper;
use Narvar\Connect\Helper\Config\Attribute as AttributeHelper;
use Narvar\Connect\Model\Data\DTO;
use Narvar\Connect\Model\Delta\Validator;
use Narvar\Connect\Model\Data\Transformer\AbstractTransformer;
use Narvar\Connect\Model\Data\Transformer\TransformerInterface;
use Magento\Sales\Model\Order\ItemFactory as OrderItem;
use Narvar\Connect\Helper\Config\Carriers as CarrierHelper;

class Shipments extends AbstractTransformer implements TransformerInterface
{
    /**
     *
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    private $orderItem;

    /**
     *
     * @var \Narvar\Connect\Helper\Config\Carriers
     */
    private $carrierHelper;

    /**
     * Constructor
     *
     * @param OrderItem $orderItem
     * @param CarrierHelper $carrierHelper
     * @param Formatter $formatter
     * @param Validator $deltaValidator
     * @param OrderStatusHelper $orderStatusHelper
     * @param AttributeHelper $configAttributes
     * @param CountryModel $countryModel
     */
    public function __construct(
        OrderItem $orderItem,
        CarrierHelper $carrierHelper,
        Formatter $formatter,
        Validator $deltaValidator,
        OrderStatusHelper $orderStatusHelper,
        AttributeHelper $configAttributes,
        CountryModel $countryModel
    ) {
        $this->orderItem = $orderItem;
        $this->carrierHelper = $carrierHelper;
    
        parent::__construct(
            $formatter,
            $deltaValidator,
            $orderStatusHelper,
            $configAttributes,
            $countryModel
        );
    }

    /**
     * Method to perpare the Order data in Narvar API Format
     *
     * @see \Narvar\Connect\Model\Data\Transformer\TransformerInterface::transform()
     */
    public function transform(DTO $dto)
    {
        return [
            'order_number' => $this->formatter->format(
                Formatter::FIELDSET_ORDER,
                'order_number',
                $dto->getOrder()->getIncrementId()
            ),
            'shipments' => $this->formShipmentData($dto)
        ];
    }

    /**
     * Method to form the Shipment data in required API Format
     *
     * @param DTO $dto
     * @return multitype
     */
    private function formShipmentData(DTO $dto)
    {
        $shipItems = $dto->getShipment()->getAllItems();
        
        $shipSource = $this->getShipSource($shipItems, $dto);
        $itemsInfo = $this->prepareShipmentItems($shipItems);
        $tracks = $dto->getShipment()->getAllTracks();
        $shipmentData = [];
        $allowedCarriers = $this->carrierHelper->getAllowedCarriers();
        foreach ($tracks as $track) {
            if (in_array(strtoupper($track->getCarrierCode()), $allowedCarriers)) {
                $trackInfo = null;
                $trackInfo = [
                    'ship_method' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'ship_method',
                        $dto->getOrder()->getShippingDescription()
                    ),
                    'carrier' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'carrier',
                        strtoupper($track->getCarrierCode())
                    ),
                    'carrier_service' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'carrier_service',
                        ''
                    ),
                    'ship_source' => $shipSource,
                    'items_info' => $itemsInfo,
                    'ship_date' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'ship_date',
                        $track->getCreatedAt()
                    ),
                    'ship_discount' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'ship_discount',
                        $dto->getOrder()->getShippingDiscountAmount()
                    ),
                    'ship_tax' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'ship_tax',
                        $dto->getOrder()->getShippingTaxAmount()
                    ),
                    'ship_total' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'ship_total',
                        $dto->getOrder()->getShippingInclTax()
                    ),
                    'tracking_number' => $this->formatter->format(
                        Formatter::FIELDSET_SHIPMENT,
                        'tracking_number',
                        $track->getTrackNumber()
                    )
                ];
                array_push($shipmentData, $trackInfo);
            }
        }
        
        return $shipmentData;
    }

    /**
     * Method to get the ship source value
     *
     * @param array $shipItems
     * @param DTO $dto
     * @return string|Ambigous <string, Ambigous>
     */
    private function getShipSource($shipItems, DTO $dto)
    {
        $shipSource = '';
        if ($this->configAttributes->getAttrShipSource() == '' ||
            $this->configAttributes->getAttrShipSource() == '-1') {
            return $shipSource;
        }
        
        foreach ($shipItems as $shipItem) {
            $orderItem = $this->getOrderItem($shipItem->getOrderItemId());
            $tempShipSource = $this->getAttributeValue(
                Formatter::FIELDSET_SHIPMENT,
                $this->configAttributes->getAttrShipSource(),
                $dto,
                $orderItem,
                'ship_source'
            );
            if (! empty($tempShipSource)) {
                $shipSource = $tempShipSource;
            }
        }
        
        return $shipSource;
    }

    /**
     * Method to get the order Item Id
     *
     * @param int $orderItemId
     * @return \Magento\Framework\Model\$this
     */
    private function getOrderItem($orderItemId)
    {
        return $this->orderItem->create()->load($orderItemId);
    }
    
    /**
     * Method to prepare the Shipment Item data for API
     *
     * @param array $shipItems
     * @return multitype:
     */
    private function prepareShipmentItems($shipItems)
    {
        $shipItemsData = [];
        
        $flag = false;
        foreach ($shipItems as $shipItem) {
            $shipItemData = [
                'sku' => $this->formatter->format(
                    Formatter::FIELDSET_SHIPMENT,
                    'sku',
                    $shipItem->getSku()
                ),
                'qty' => $this->formatter->format(
                    Formatter::FIELDSET_SHIPMENT,
                    'qty',
                    $shipItem->getQty()
                )
            ];
            
            array_push($shipItemsData, $shipItemData);
        }
        
        return $shipItemsData;
    }
}
