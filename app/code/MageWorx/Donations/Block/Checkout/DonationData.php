<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Checkout;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class DonationData extends \Magento\Payment\Block\Form
{
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \MageWorx\Donations\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\Donations\Model\
     */
    protected $modelDonation;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * Form constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\Donations\Model\Donation               $modelDonation
     * @param \MageWorx\Donations\Helper\Data                  $helperData
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Directory\Model\Currency                $currency
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param PriceCurrencyInterface                           $priceCurrency
     * @param \Magento\Framework\Session\SessionManager        $sessionManager
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorx\Donations\Model\Donation $modelDonation,
        \MageWorx\Donations\Helper\Data $helperData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Customer\Model\Session $customerSession,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Session\SessionManager $sessionManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->currency = $currency;
        $this->priceCurrency = $priceCurrency;
        $this->modelDonation = $modelDonation;
        $this->helperData = $helperData;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Get Store
     *
     * @param null $storeId
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Get donation data
     *
     * @return array
     */
    public function getDonationData()
    {
        $result = [];
        $donationDetails = $this->modelDonation->getQuoteDetailsDonation();

        $result['is_enable'] = true;
        if (!empty($donationDetails)) {
            $result['donation'] = $donationDetails['donation'];
            $result['is_donation_use'] = ($donationDetails['donation'] > 0) ? true : false;
        }

        $storeId = $this->_storeManager->getStore()->getId();
        $result['url'] = $this->getUrl('donations/checkout/donation');
        $result['is_display_title'] = ($result['is_enable'] == false) ? false : $this->getIsDisplayTitle();
        $result['is_show_donation_cart'] = $this->helperData->isShowDonationCart($storeId);
        $result['minimum_donation'] = $this->helperData->getMinimumDonation($storeId);
        $result['default_description_donation'] = $this->helperData->getDefaultDescription($storeId);

        return $result;
    }

    /**
     * Check if display title
     * On the cart page we use the external title wrapper.
     *
     * @return boolean
     */
    public function getIsDisplayTitle()
    {
        $actionList = [];
        if (!empty($this->_data['cart_full_actions']) && is_array($this->_data['cart_full_actions'])) {
            $actionList = $this->_data['cart_full_actions'];
        }
        $actionList[] = 'checkout_cart_index';
        return !in_array($this->_request->getFullActionName(), $actionList);
    }
}
