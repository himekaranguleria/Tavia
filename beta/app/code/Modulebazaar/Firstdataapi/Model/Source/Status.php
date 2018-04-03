<?php
namespace Modulebazaar\Firstdataapi\Model\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return
        [
             ["value"=>0,"label"=> 'In Active'],
             ["value"=>1,"label"=> 'Active'],
        ];
    }
}
