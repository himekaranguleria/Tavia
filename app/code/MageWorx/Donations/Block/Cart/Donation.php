<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Cart;

class Donation extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \MageWorx\Donations\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Checkout\Model\CompositeConfigProvider
     */
    protected $configProvider;

    /**
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    protected $layoutProcessors;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Checkout\Model\CompositeConfigProvider  $configProvider
     * @param \MageWorx\Donations\Helper\Data                  $helperData
     * @param array                                            $layoutProcessors
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        \MageWorx\Donations\Helper\Data $helperData,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        $this->layoutProcessors = $layoutProcessors;
        $this->helperData = $helperData;
        parent::__construct($context, $customerSession, $checkoutSession, $data);

    }

    /**
     * Show donations in Cart
     *
     * @return bool
     */
    public function showDonationCart()
    {
        return $this->helperData->isShowDonationCart();
    }

    /**
     * Retrieve checkout configuration
     *
     * @return array
     */
    public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Get base url for block.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }


}