<?php

namespace Swap\Tavia\Controller\Index;

class Remove extends \Magento\Framework\App\Action\Action {

    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        $DynamicBox = $checkoutSession->getDynamicBox();
        $SavedDynamicBox = $checkoutSession->getSavedDynamicBox();
//        print_r($DynamicBox);
//        print_r($SavedDynamicBox);
//        print_r($_REQUEST['box']);
        if (@$DynamicBox) {
            if (@$_REQUEST['box']) {
                $box_array = explode('box-', $_REQUEST['box']);
//                print_r($box_array);
                $DynamicBox = $checkoutSession->getDynamicBox();
//                if (@$DynamicBox) {
                array_splice($DynamicBox, $box_array['1'], 1);
                $counts = count($DynamicBox);
                if (@$counts) {
                    $checkoutSession->setDynamicBox($DynamicBox);
                    echo "success";
                } else {
                    $checkoutSession->unsDynamicBox();
                    $checkoutSession->unsSavedDynamicBox();
//                header('Location:' . 'https://www.mytavia.com/tavia/index/index');
                    echo "empty";
                    die();
                }
//                } else {
//                    echo "empty";
//                    die();
//                }
            }
        } else {
            $checkoutSession->unsDynamicBox();
            $checkoutSession->unsSavedDynamicBox();
        }
?>
        <?php

    }

}

        ?>