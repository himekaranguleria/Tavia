<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model\Total\Quote;

use Magento\Tax\Model\Config as TaxConfig;

class Donation extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \MageWorx\Donations\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\Donations\Model\Donation
     */
    protected $modelDonation;

    /**
     * @var \MageWorx\Donations\Helper\Donation
     */
    protected $helperDonation;

    /**
     * @var bool
     */
    protected $isCollected;

    /**
     * Donation constructor
     *
     * @param \Magento\Framework\Event\ManagerInterface         $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \MageWorx\Donations\Model\Donation                $modelDonation
     * @param \MageWorx\Donations\Helper\Data                   $helperData
     * @param \MageWorx\Donations\Helper\Donation               $helperDonation
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \MageWorx\Donations\Model\Donation $modelDonation,
        \MageWorx\Donations\Helper\Data $helperData,
        \MageWorx\Donations\Helper\Donation $helperDonation
    ) {
        $this->setCode('mageworx_donation');
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->helperDonation = $helperDonation;
        $this->modelDonation = $modelDonation;
    }

    /**
     * Collect address donation amount
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();

        if ($this->checkShipping($address, $shippingAssignment)) {
            return $this;
        }

        $donationDetails = $this->modelDonation->getQuoteDetailsDonation();

        if (empty($donationDetails)) {
            return $this;
        }

        $store = $quote->getStore();
        if (isset($donationDetails['donation'])) {
            $basePrice = $donationDetails['donation'];
            $price = $this->priceCurrency->convert($basePrice, $store);

            $mageworxDonationAmount = $this->priceCurrency->convertAndRound($price, $store);
            $baseMageworxDonationAmount = $this->priceCurrency->round($basePrice);

            $this->addPricesToAddress($total, $address, $mageworxDonationAmount);
            $this->addBasePricesToAddress($total, $address, $baseMageworxDonationAmount);
            $this->addDonationDetailsToAddress($total, $address, $donationDetails);

            $this->isCollected = true;
        }

        return $this;

    }

    /**
     * Add donation total information to address
     *
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if (!$this->isCollected) {
            $quote->collectTotals();
        }

        if ($total->getMageworxDonationAmount()) {
            return [
                'code' => $this->getCode(),
                'title' => __('Donation'),
                'value' => $total->getMageworxDonationAmount(),
            ];
        }

        return null;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address                  $address
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     *
     * @return bool
     */
    protected function checkShipping($address, $shippingAssignment)
    {
        if ($address->getSubtotal() == 0) {
            return true;
        }

        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address       $address
     * @param array                                    $donationData
     *
     * @return $this
     */
    protected function addDonationDetailsToAddress($total, $address, $donationData)
    {
        $address->setMageworxDonationDetails(serialize($donationData));
        $total->setMageworxDonationDetails(serialize($donationData));
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address       $address
     * @param double                                   $mageworxDonationAmount
     *
     * @return $this
     */
    protected function addPricesToAddress($total, $address, $mageworxDonationAmount)
    {
        $total->setMageworxDonationAmount($mageworxDonationAmount);
        $total->setTotalAmount('mageworx_donation', $mageworxDonationAmount);

        $address->setMageworxDonationAmount($mageworxDonationAmount);
        $address->setTotalAmount('mageworx_donation', $mageworxDonationAmount);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address       $address
     * @param float                                    $baseMageworxDonationAmount
     *
     * @return $this
     */
    protected function addBasePricesToAddress($total, $address, $baseMageworxDonationAmount)
    {
        $total->setBaseMageworxDonationAmount($baseMageworxDonationAmount);
        $total->setBaseTotalAmount('mageworx_donation', $baseMageworxDonationAmount);

        $address->setBaseMageworxDonationAmount($baseMageworxDonationAmount);
        $address->setBaseTotalAmount('mageworx_donation', $baseMageworxDonationAmount);

        return $this;
    }
}
