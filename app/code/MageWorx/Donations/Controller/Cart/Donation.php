<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Controller\Cart;

class Donation extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Donation factory
     *
     * @var \MageWorx\Donations\Model\DonationFactory
     */
    protected $donationFactory;

    /**
     * @var \MageWorx\Donations\Helper\Donation
     */
    protected $helperDonation;

    /**
     * donation constructor.
     *
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator
     * @param \Magento\Checkout\Model\Cart                       $cart
     * @param \MageWorx\Donations\Model\DonationFactory          $donationFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface         $quoteRepository
     * @param \MageWorx\Donations\Helper\Donation                $helperDonation
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \MageWorx\Donations\Model\DonationFactory $donationFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \MageWorx\Donations\Helper\Donation $helperDonation
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->helperDonation = $helperDonation;
        $this->donationFactory = $donationFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Initialize donation
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->getRequest()->getParams();
        $donationPost = $this->getRequest()->getPost('donation');

        /* @todo  add donation and charity*/
    }
}
