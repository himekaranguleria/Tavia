<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Helper;

class Price extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Price constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Framework\App\Helper\Context             $context
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Format price
     *
     * @param $price
     * @param $decimal
     *
     * @return float
     */
    public function getFormatPrice($price, $decimal)
    {
        $currency = $this->storeManager->getStore()->getCurrentCurrency();
        $formatPrice = $this->priceCurrency->format($price, false, $decimal, null, $currency);

        return $formatPrice;
    }
}