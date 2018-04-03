<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model;

use MageWorx\Donations\Api\DonationInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Store\Model\StoreManagerInterface;
use MageWorx\Donations\Helper\Donation as HelperDonation;
use MageWorx\Donations\Model\CharityRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class Donation implements DonationInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HelperDonation
     */
    protected $helperDonation;



    /**
     * @var CharityRepository
     */
    protected $charityRepository;

    /**
     * @param CheckoutSession       $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param HelperDonation        $helperDonation
     * @param CharityRepository     $charityRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        HelperDonation $helperDonation,
        CharityRepository $charityRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->helperDonation = $helperDonation;
        $this->charityRepository = $charityRepository;
    }

    /**
     * Add donation to quote and session
     *
     * @param float $donation
     * @param float $charityId
     *
     * @return void
     */
    public function addDonationToQuote($donation, $charityId = null)
    {
        if ($donation) {
            $donationQuoteData = $this->getQuoteDetailsDonation();
            $donationQuoteData = $this->modifyDonationDetailsByPostData($donationQuoteData, $donation, $charityId);

            $this->setQuoteDetailsDonation($donationQuoteData);
            $this->getCurrentSession()->setTotalsCollectedFlag(false)->getQuote()->collectTotals();
        }
    }

    /**
     * Delete donation to quote and session
     *
     * @return void
     */
    public function deleteDonationFromQuote()
    {
        $this->cleanQuoteDetailsDonation();
        $this->getCurrentSession()->setTotalsCollectedFlag(false)->getQuote()->collectTotals();
    }

    /**
     * Get current session
     *
     * @return \Magento\Backend\Model\Session\Quote|\Magento\Checkout\Model\Session
     */
    public function getCurrentSession()
    {
        return $this->helperDonation->getCurrentSession();
    }

    /**
     * @param array||null $donationQuoteData
     * @param float $donation
     *
     * @return array
     */
    protected function modifyDonationDetailsByPostData($donationQuoteData, $donation, $charityId = null)
    {
        if ($donation) {
            $donationQuoteData['donation'] = $donation;
        }
        if ($charityId) {
            $donationQuoteData['charity_id'] = $charityId;

            /** @var \MageWorx\Donations\Model\Charity $charity */
            $charity = $this->charityRepository->getById($charityId);
            $donationQuoteData['charity_title'] = $charity->getName();

        }

        return $donationQuoteData;
    }

    /**
     * Add donation data to session
     *
     * @param array $donationQuoteData
     *
     * @return void
     */
    protected function setQuoteDetailsDonation($donationQuoteData)
    {
        $session = $this->getCurrentSession();
        $session->setMageworxDonationDetails($donationQuoteData);
    }

    /**
     *  Clean donation data from session
     *
     * @return void
     */
    protected function cleanQuoteDetailsDonation()
    {
        $this->getCurrentSession()->setMageworxDonationDetails(null);
    }

    /**
     * Get donation data from session
     *
     * @return array|null
     */
    public function getQuoteDetailsDonation()
    {
        $session = $this->getCurrentSession();
        $donationDetails = $session->getMageworxDonationDetails();
        if (is_null($donationDetails)) {
            return null;
        }

        return $donationDetails;
    }
}
