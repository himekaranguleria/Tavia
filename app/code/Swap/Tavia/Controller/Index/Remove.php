<?php

namespace Swap\Tavia\Controller\Index;

class Remove extends \Magento\Framework\App\Action\Action {

    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        $DynamicBox = $checkoutSession->getDynamicBox();
        if (@$_REQUEST['box']) {
            $box_array = explode('box-', $_REQUEST['box']);
            $DynamicBox = $checkoutSession->getDynamicBox();
            array_splice($DynamicBox, $box_array['1'], 1);
            $counts = count($DynamicBox);
            if (@$counts) {
                $checkoutSession->setDynamicBox($DynamicBox);
                echo "success";
            } else {
                $checkoutSession->unsDynamicBox();
                $checkoutSession->unsSavedDynamicBox();
                header('Location:' . 'https://www.mytavia.com/tavia/index/index');
                echo "empty";
                die();
            }
        }
?>
        <?php

    }

}

        ?>