<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model;

use MageWorx\Donations\Api\Data\CharityInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;

class Charity extends AbstractModel implements CharityInterface, IdentityInterface
{

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    const CURRENT_CHARITY = 'current_charity';
    const CACHE_TAG       = 'mageworx_donations_charity';
    protected $_eventPrefix = 'mageworx_donations_charity';

    const DONATIONS_CHARITY_TABLE_NAME       = 'mageworx_donations_charity';
    const DONATIONS_CHARITY_STORE_TABLE_NAME = 'mageworx_donations_charity_store';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\Donations\Helper\Image
     */
    protected $helperImage;


    /**
     * Charity constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \MageWorx\Donations\Helper\Image                             $helperImage
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\Donations\Helper\Image $helperImage,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\Donations\Model\ResourceModel\Charity');
        $this->setIdFieldName('charity_id');
    }

    public function afterLoad()
    {
        $this->getResource()->afterLoad($this);
        parent::afterLoad();
    }

    /**
     * Set sort order
     *
     * @param string $sortOrder
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(static::SORT_ORDER, $sortOrder);
    }

    /**
     * Get status
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData(static::IS_ACTIVE);
    }

    /**
     * Set status
     *
     * @param int|bool $isActive
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(static::IS_ACTIVE, $isActive);
    }

    /**
     * Get date added field
     *
     * @return int|null
     */
    public function getDateAdded()
    {
        return $this->getData(static::DATE_ADDED);
    }

    /**
     * Get last updated date
     *
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->getData(static::DATE_UPDATED);
    }

    /**
     * Set enable date field
     *
     * @param int $dateAdded
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     */
    public function setDateAdded($dateAdded)
    {
        return $this->setData(static::DATE_ADDED, $dateAdded);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [static::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Image path.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData('filename');
    }

    /**
     * Get url to the image
     *
     * @return string
     */
    public function getImageUrl()
    {
        $imagePath = $this->getImage();
        if (!$imagePath) {
            return '';
        }

        return $this->helperImage->getMediaUrl($imagePath);
    }


}

