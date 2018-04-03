<?php
/**
 * Returns Items Interface
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;
use Narvar\Connect\Api\Data\ReturnsItemsExtensionInterface;

interface ReturnsItemsInterface extends ExtensibleDataInterface
{
   
    /**
     * Method to get Item Sku
     *
     * @return string
     */
    public function getItemSku();
    
    /**
     * Method to set Item Sku
     *
     * @param string $itemSku
     * @return ReturnsItemsInterface
     */
    public function setItemSku($itemSku);
    
    /**
     * Method to get Qty
     *
     * @return int
     */
    public function getQty();
    
    /**
     * Method to Set Qty
     *
     * @param int $itemSku
     * @return ReturnsItemsInterface
     */
    public function setQty($qty);
    
    /**
     * Method to get Reason
     *
     * @return string
     */
    public function getReason();
    
    /**
     * Method to Set Reason
     *
     * @param string $reason
     * @return ReturnsItemsInterface
     */
    public function setReason($reason);
    
    /**
     * Method to Get Condition
     *
     * @return string
     */
    public function getCondition();
    
    /**
     * Method to Set Condition
     *
     * @param string $condition
     * @return ReturnsItemsInterface
     */
    public function setCondition($condition);
    
    /**
     * Method to Get Resolution
     *
     * @return string
     */
    public function getResolution();
    
    /**
     * Method to Set Resolution
     *
     * @param string $resolution
     * @return ReturnsItemsInterface
     */
    public function setResolution($resolution);
    
    /**
     * Method to Get Comment
     *
     * @return string
     */
    public function getComment();
    
    /**
     * Method to Set Comment
     *
     * @param string $comment
     * @return ReturnsItemsInterface
     */
    public function setComment($comment);
    
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return ReturnsItemsExtensionInterface|null
     */
    public function getExtensionAttributes();
    
    /**
     * Set an extension attributes object.
     *
     * @param ReturnsItemsExtensionInterface $extensionAttributes
     * @return ReturnsItemsInterface
     */
    public function setExtensionAttributes(
        ReturnsItemsExtensionInterface $extensionAttributes
    );
}
