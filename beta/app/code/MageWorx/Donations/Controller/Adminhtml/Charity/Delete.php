<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Adminhtml\Charity;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\Donations\Controller\Adminhtml\Charity;

class Delete extends Charity
{
    /**
     * Delete charity
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('charity_id');
        if ($id) {
            try {
                $this->charityRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the charity'));
                $this->_redirect('mageworx_donations/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the charity right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('mageworx_donations/*/edit', ['id' => $this->getRequest()->getParam('charity_id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a carrier to delete.'));
        $this->_redirect('mageworx_donations/*/');


    }
}
