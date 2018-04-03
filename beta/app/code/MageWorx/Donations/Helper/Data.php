<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use MageWorx\Donations\Helper\PredefinedDonation;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config paths to settings
     */
    const SHOW_DONATIONS_CART                   = 'mageworx_donations/main/show_donations_cart';
    const SHOW_CHARITY_CART                     = 'mageworx_donations/main/show_charity_cart';
    const MINIMUM_DONATIONS                     = 'mageworx_donations/main/minimum_donations';
    const DONATION_TAX_CALCULATION_INCLUDES_TAX = 'mageworx_donations/main/tax_calculation_includes_tax';
    const SHOW_DONATIONS_ADMIN                  = 'mageworx_donations/main/show_donations_admin';
    const DEFAULT_DESCRIPTION                   = 'mageworx_donations/main/default_description_donations';
    const AMOUNT_PLACEHOLDER                    = 'mageworx_donations/main/donations_amount_placeholder';
    const SHOW_PREDEFINED_VALUES                = 'mageworx_donations/main/show_predefined_values';
    const PREDEFINED_VALUES_DONATION            = 'mageworx_donations/main/predefined_values_donation';

    /**
     * @var PredefinedDonation
     */
    protected $helperPredefinedDonation;

    /**
     * Data constructor
     *
     * @param Context            $context
     * @param PredefinedDonation $helperPredefinedDonation
     *
     */
    public function __construct(
        Context $context,
        PredefinedDonation $helperPredefinedDonation
    ) {
        $this->helperPredefinedDonation = $helperPredefinedDonation;
        parent::__construct($context);
    }


    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isShowDonationCart($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::SHOW_DONATIONS_CART,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isShowDonationAdmin($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::SHOW_DONATIONS_ADMIN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isShowCharityCart($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::SHOW_CHARITY_CART,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get minimum donation
     *
     * @param null|int $storeId
     *
     * @return float
     */
    public function getMinimumDonation($storeId = null)
    {
        return (float)$this->scopeConfig->getValue(
            self::MINIMUM_DONATIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default description
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getDefaultDescription($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::DEFAULT_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default amount placeholder
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getAmountPlaceholder($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::AMOUNT_PLACEHOLDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Show predefined values donation
     *
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isShowPredefinedValues($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::SHOW_PREDEFINED_VALUES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get predefined values donation
     *
     * @param null $storeId
     *
     * @return array|null
     */
    public function getPredefinedValuesDonation($storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::PREDEFINED_VALUES_DONATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $value = $this->helperPredefinedDonation->unserializeValue($value);

        return $value;
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isTaxCalculationIncludesTax($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DONATION_TAX_CALCULATION_INCLUDES_TAX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

}