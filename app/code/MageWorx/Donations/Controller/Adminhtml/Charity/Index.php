<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Adminhtml\Charity;

use MageWorx\Donations\Controller\Adminhtml\Charity;

class Index extends Charity
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Charity'));
        $this->_view->renderLayout('root');
    }

}