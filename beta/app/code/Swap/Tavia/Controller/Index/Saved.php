<?php

namespace Swap\Tavia\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Saved extends \Magento\Framework\App\Action\Action {

    public function execute() {
		
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        $data = $_REQUEST['q'];
		$checkoutSession->setSavedDynamicBox($data);
		
		
			$DynamicBox = $checkoutSession->getDynamicBox();
                if( ! is_null($DynamicBox)){
				$sessionValue = $DynamicBox;
				$index = count($sessionValue);
				
				foreach($data as $key=>$categoryArray){
					$totalsum = 0;
					foreach($categoryArray as $category=>$pId){
						$sum = 0;
						foreach($pId as $qty){
							$sum += $qty;					
						}
						if (array_key_exists($category,$DynamicBox)){ 
							if($sum>$DynamicBox[$key][$category]){
								$DynamicBox[$key][$category] = $sum;
							}
						}else{
							$DynamicBox[$key][$category] = $sum;						
						}
					}
					
					if (array_key_exists('Pads',$DynamicBox[$key])){ 
						$totalsum += $DynamicBox[$key]['Pads'];
					}
					if (array_key_exists('Tampons',$DynamicBox[$key])){ 
						$totalsum += $DynamicBox[$key]['Tampons'];
					}
					if (array_key_exists('Liners',$DynamicBox[$key])){ 
						$totalsum += $DynamicBox[$key]['Liners'];
					}
					$DynamicBox[$key]['Total'] = $totalsum;
				}				
			}
		
           
			$checkoutSession->setDynamicBox($DynamicBox);
		
    }

}
?>