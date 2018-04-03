<?php
namespace Modulebazaar\Firstdataapi\Model\Source;

class Mode
{
    
    public function toOptionArray()
    {
        return
        [
             ["value"=>'test',"label"=> 'Test Mode'],
             ["value"=>'live',"label"=> 'Live Mode'],
        ];
    }
}
