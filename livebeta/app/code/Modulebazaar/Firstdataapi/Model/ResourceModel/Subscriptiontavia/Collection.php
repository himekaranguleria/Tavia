<?php
namespace  Modulebazaar\Firstdataapi\Model\ResourceModel\Subscriptiontavia;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
/**
 * Resource initialization
 *
 * @return void
 */
    protected function _construct()
    {
        $this->_init('Modulebazaar\Firstdataapi\Model\Subscriptiontavia', 'Modulebazaar\Firstdataapi\Model\ResourceModel\Subscriptiontavia');
    }
}
