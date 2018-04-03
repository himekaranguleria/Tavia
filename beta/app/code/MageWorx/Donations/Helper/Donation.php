<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use MageWorx\Donations\Model\ResourceModel\Charity\CollectionFactory as CharityCollectioFactory;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Helper\Context;
use MageWorx\Donations\Model\DonationFactory;


class Donation extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Quote
     */
    protected $adminQuoteSession;

    /**
     * @var CharityCollectioFactory
     */
    protected $charityCollectionFactory;

    /**
     * @var DonationFactory
     */
    protected $donationFactory;

    /**
     * Donation constructor
     *
     * @param Context                $context
     * @param CustomerSession        $customerSession
     * @param CheckoutSession        $checkoutSession
     * @param State                  $appState
     * @param Quote                  $adminQuoteSession
     * @param ObjectManagerInterface $objectManager
     * @param DonationFactory        $donationFactory
     *
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        State $appState,
        Quote $adminQuoteSession,
        ObjectManagerInterface $objectManager,
        DonationFactory $donationFactory
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->appState = $appState;
        $this->adminQuoteSession = $adminQuoteSession;
        $this->objectManager = $objectManager;
        $this->donationFactory = $donationFactory;


        parent::__construct($context);
    }

    /**
     * Return Donation data from session
     *
     * @return array
     */
    public function getDonation()
    {
        $donation = $this->donationFactory->create();
        $data = $donation->getQuoteDetailsDonation();

        return $data;
    }

    /**
     * Return donation
     *
     * @return \MageWorx\Donations\Model\Donation
     */
    public function getNewDonationObject()
    {

        return $this->donationFactory->create();
    }

    /**
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return Address
     */
    public function getSalesAddress($quote)
    {
        $address = $quote->getShippingAddress();
        if (!$address->getSubtotal()) {
            $address = $quote->getBillingAddress();
        }
        return $address;
    }

    /**
     * Get current checkout quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            $quote = $this->adminQuoteSession->getQuote();
        } else {
            $quote = $this->checkoutSession->getQuote();
        }
        return $quote;
    }

    /**
     * Ger current session
     *
     * @return \Magento\Backend\Model\Session\Quote|\Magento\Checkout\Model\Session
     */
    public function getCurrentSession()
    {
        $areaCode = $this->appState->getAreaCode();
        if ($areaCode == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->objectManager->get('Magento\Backend\Model\Session\Quote');
        }
        return $this->checkoutSession;
    }
}