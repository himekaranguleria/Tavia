<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Adminhtml\Charity;

use MageWorx\Donations\Controller\Adminhtml\Charity;

class Edit extends Charity
{
    /**
     * Edit charity
     *
     * @return void
     */
    public function execute()
    {
        $this->initCharity();
        $model = $this->coreRegistry->registry('current_charity');
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();
        $breadcrumb = $id ? __('Edit Charity') : __('New Charity');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);

        $title = $model->getId() ? $model->getName() : __('New Charity');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }
}
