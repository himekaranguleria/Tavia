<?php

namespace Swap\Tavia\Controller\Index;

class Display extends \Magento\Framework\App\Action\Action {

    public function execute() {
//        echo "<pre>";
//        print_r($_REQUEST);
//        echo "</pre>";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');


        if (@$_REQUEST) {
//            if (@$_REQUEST['reset']) {
//                if ($_REQUEST['reset'] == 1) {
                    $checkoutSession->unsDynamicBox();
                    $checkoutSession->unsSavedDynamicBox();
//                }
//            } else {
                $index = 0;
                $DynamicBox = $checkoutSession->getDynamicBox();
                if (!is_null($DynamicBox)) {
                    $sessionValue = $DynamicBox;
                    $index = count($sessionValue);
                }
                if (@$_REQUEST['tempons_sum'])
                    $session['Tampons'] = $_REQUEST['tempons_sum'];
                if (@$_REQUEST['pads_sum'])
                    $session['Pads'] = $_REQUEST['pads_sum'];
                if (@$_REQUEST['liners_sum'])
                    $session['Liners'] = $_REQUEST['liners_sum'];
                if (@$_REQUEST['total_sum'])
                    $session['Total'] = $_REQUEST['total_sum'];
                $session['prompt'] = 1;
                $sessionValue[$index] = $session;
                $checkoutSession->setDynamicBox($sessionValue);
//            }
            if (@$_REQUEST['days']) {
                $checkoutSession->setSusbcriptionDays($_REQUEST['days']);
            }
            echo "success";
        }
        /* if (@$_REQUEST) {
          if (@$_REQUEST['tempons_sum'])
          $checkoutSession->setTempons($_REQUEST['tempons_sum']);
          if (@$_REQUEST['pads_sum'])
          $checkoutSession->setPads($_REQUEST['pads_sum']);
          if (@$_REQUEST['liners_sum'])
          $checkoutSession->setLiners($_REQUEST['liners_sum']);
          if (@$_REQUEST['total_sum'])
          $checkoutSession->setTotal($_REQUEST['total_sum']);
          echo "success";
          } */

//        $catRequest = $_REQUEST['catRequest'];
?>
        <?php

    }

}

        ?>