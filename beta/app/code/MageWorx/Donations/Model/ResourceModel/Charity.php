<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Event\ManagerInterface;
use MageWorx\Donations\Api\Data\CharityInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

class Charity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param Context               $context
     * @param DateTime              $date
     * @param StoreManagerInterface $storeManager
     * @param LibDateTime           $dateTime
     * @param ManagerInterface      $eventManager
     * @param EntityManager         $entityManager
     */
    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        LibDateTime $dateTime,
        ManagerInterface $eventManager,
        EntityManager $entityManager
    ) {
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->eventManager = $eventManager;
        $this->entityManager = $entityManager;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_donations_charity', CharityInterface::CHARITY_ID);
    }

    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\MageWorx\Donations\Model\Charity $object
     *
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setId(null);
            $object->isObjectNew(true);
        }

        $object->setData('date_updated', time());
        parent::_beforeSave($object);

        return $this;
    }

}
