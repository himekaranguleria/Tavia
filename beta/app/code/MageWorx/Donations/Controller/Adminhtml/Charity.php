<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\Donations\Api\CharityRepositoryInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

abstract class Charity extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CharityRepositoryInterface
     */
    protected $charityRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Charity constructor.
     *
     * @param Registry                   $coreRegistry
     * @param CharityRepositoryInterface $charityRepository
     * @param Context                    $context
     * @param LoggerInterface            $logger
     */
    public function __construct(
        Registry $coreRegistry,
        CharityRepositoryInterface $charityRepository,
        Context $context,
        LoggerInterface $logger
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->charityRepository = $charityRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Init charity
     *
     * @return void
     */
    protected function initCharity()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id && $this->getRequest()->getParam('charity_id')) {
            $id = (int)$this->getRequest()->getParam('charity_id');
        }

        if ($id) {
            /** @var \MageWorx\Donations\Model\Charity $charity */
            $charity = $this->charityRepository->getById($id);
        } else {
            /** @var \MageWorx\Donations\Model\Charity $charity */
            $charity = $this->charityRepository->getEmptyEntity();
        }

        $this->coreRegistry->register(
            \MageWorx\Donations\Model\Charity::CURRENT_CHARITY,
            $charity,
            true
        );
    }

    /**
     * Initiate action
     *
     * @return Quote
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('MageWorx_Donations::donations_charity')
            ->_addBreadcrumb(__('Charity'), __('Charity'));

        return $this;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Donations::charity');
    }
}