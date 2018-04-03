<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Adminhtml\Charity\Edit\Button;

use Magento\Framework\Registry;
use MageWorx\Donations\Model\Charity;
use MageWorx\Donations\Model\CharityFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var CharityFactory
     */
    protected $charityFactory;

    /**
     * Generic constructor
     *
     * @param Context        $context
     * @param Registry       $registry
     * @param CharityFactory $charityFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CharityFactory $charityFactory
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->charityFactory = $charityFactory;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        /** @var Context $context */
        $context = $this->context;
        /** @var string $url */
        $url = $context->getUrl($route, $params);

        return $url;
    }

    /**
     * Get charity
     *
     * @return \MageWorx\Donations\Model\Charity
     */
    public function getCharity()
    {
        $charity = $this->registry->registry(Charity::CURRENT_CHARITY);
        if (!$charity) {
            $charity = $this->charityFactory->create();
        }

        return $charity;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
