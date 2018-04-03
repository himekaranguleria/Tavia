<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Checkout;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Donation extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\Donations\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\Donations\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \MageWorx\Donations\Model\Donation
     */
    protected $modelDonation;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator
     * @param \Magento\Checkout\Model\Cart                       $cart
     * @param \Magento\Quote\Api\CartRepositoryInterface         $quoteRepository
     * @param \MageWorx\Donations\Helper\Data                    $helperData
     * @param \MageWorx\Donations\Helper\Price                   $helperPrice
     * @param \MageWorx\Donations\Model\Donation                 $modelDonation
     * @param PriceCurrencyInterface                             $priceCurrency
     * @param \Magento\Framework\Escaper                         $escaper
     *
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \MageWorx\Donations\Helper\Data $helperData,
        \MageWorx\Donations\Helper\Price $helperPrice,
        \MageWorx\Donations\Model\Donation $modelDonation,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->quoteRepository = $quoteRepository;
        $this->helperData = $helperData;
        $this->helperPrice = $helperPrice;
        $this->modelDonation = $modelDonation;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Processing of donation requests
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->getRequest()->getParams();
        $donation = $this->getRequest()->getParam('donation');
        $charity = $this->getRequest()->getParam('charity');
        $isDonationDelete = $this->getRequest()->getParam('deleteDonation');

        /* if press button deleteDonation */
        if ($donation == 0 && $isDonationDelete) {
            $this->modelDonation->deleteDonationFromQuote();
            $cartQuote = $this->cart->getQuote();
            $cartQuote->collectTotals();
            $this->quoteRepository->save($cartQuote);

            return $this->getResponse()->setBody(\Zend_Json::encode(['result' => 'true']));
        }

        $minDonations = $this->helperData->getMinimumDonation();

        /* if press button AddDonation */
        if ($minDonations < 0) {
            $this->messageManager->addErrorMessage(
                __(
                    'Minimum donations should be more than 0'
                )
            );

            return $this->getResponse()->setBody(\Zend_Json::encode(['result' => 'false']));
        } elseif (empty($donation)) {
            $this->messageManager->addErrorMessage(
                __(
                    'Please specify a donation amount.'
                )
            );
            return $this->getResponse()->setBody(\Zend_Json::encode(['result' => 'false']));
        } elseif ($donation < $minDonations) {
            $price = $this->helperPrice->getFormatPrice($minDonations, 2);
            $this->messageManager->addErrorMessage(
                __(
                    'Minimum accepted donation is %1',
                    $this->escaper->escapeHtml($price)
                )
            );
            return $this->getResponse()->setBody(\Zend_Json::encode(['result' => 'false']));
        } else {
            $this->modelDonation->addDonationToQuote($donation, $charity);
            $cartQuote = $this->cart->getQuote();
            $cartQuote->collectTotals();
            $this->quoteRepository->save($cartQuote);
        }

        return $this->getResponse()->setBody(\Zend_Json::encode(['result' => 'true']));

    }
}
