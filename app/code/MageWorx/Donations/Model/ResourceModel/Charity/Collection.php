<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Model\ResourceModel\Charity;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory             $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param mixed                                                        $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\Donations\Model\Charity', 'MageWorx\Donations\Model\ResourceModel\Charity');
        $this->_map['fields']['charity_id'] = 'main_table.charity_id';
        $this->_setIdFieldName('charity_id');
    }

}