<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Adminhtml\Charity;

class Create extends \MageWorx\Donations\Controller\Adminhtml\Charity
{
    /**
     * Create charity
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }

}