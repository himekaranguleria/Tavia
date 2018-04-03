<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Ui\DataProvider\Charity\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\Donations\Model\Charity;
use MageWorx\Donations\Model\CharityFactory;

/**
 * Class AbstractModifier
 *
 */
abstract class AbstractModifier implements ModifierInterface
{
    const FORM_NAME           = 'mageworx_donations_charity_form';
    const DATA_SOURCE_DEFAULT = 'charity';
    const DATA_SCOPE_CHARITY  = 'data.charity';

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * @var CharityFactory
     */
    protected $charityFactory;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ArrayManager          $arrayManager
     * @param UrlInterface          $urlBuilder
     * @param Registry              $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param CharityFactory        $charityFactory
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CharityFactory $charityFactory
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->registry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->charityFactory = $charityFactory;
    }

    /**
     * Get current charity
     *
     * @return Charity|null
     */
    protected function getCharity()
    {
        $registry = $this->registry;
        $charity = $registry->registry(Charity::CURRENT_CHARITY);
        if (!$charity) {
            $charity = $this->charityFactory->create();
        }

        return $charity;
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    protected function getBaseCurrencySymbol()
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }
}
