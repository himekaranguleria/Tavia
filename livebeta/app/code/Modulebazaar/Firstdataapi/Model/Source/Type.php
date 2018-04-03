<?php
namespace Modulebazaar\Firstdataapi\Model\Source;

class Type
{
    
    public function toOptionArray()
    {
        return
        [
             ["value"=>'01',"label"=> 'Authorize Only'],
             ["value"=>'00',"label"=> 'Authorize and Capture'],
        ];
    }
}
