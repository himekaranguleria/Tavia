<?php

namespace Swap\Tavia\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Ambassador extends \Magento\Framework\App\Action\Action {

    public function execute() {


        $this->_view->loadLayout();

        $this->_view->renderLayout();
    }

}

?>