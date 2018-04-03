<?php
namespace Modulebazaar\Firstdataapi\Model\ResourceModel;

class Subscriptiontavia extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
/**
 * Initialize resource model
 *
 * @return void
 */
    protected function _construct()
    {
        $this->_init('subscriptiontavia', 'id');
    }
}
