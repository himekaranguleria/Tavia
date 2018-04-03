<?php
namespace  Modulebazaar\Firstdataapi\Model\ResourceModel\FirstdataApi;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
/**
 * Resource initialization
 *
 * @return void
 */
    protected function _construct()
    {
        $this->_init('Modulebazaar\Firstdataapi\Model\FirstdataApi', 'Modulebazaar\Firstdataapi\Model\ResourceModel\FirstdataApi');
    }
}
