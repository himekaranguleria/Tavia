<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Adminhtml\Order\View;

class Info extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \MageWorx\Donations\Helper\Price
     */
    protected $helperPrice;

    /**
     * Info constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MageWorx\Donations\Helper\Price        $helperPrice
     * @param \Magento\Framework\Registry             $registry
     * @param array                                   $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MageWorx\Donations\Helper\Price $helperPrice,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->helperPrice = $helperPrice;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

    /**
     * Get Donation Details
     *
     * @return array | null
     */
    public function getDonationDetails()
    {
        $donationDetails = ['donation_title' => 'Donation Amount', 'charity_title' => 'Charity'];
        $basePrice = $this->getOrder()->getBaseMageworxDonationAmount();


        if ($basePrice > 0) {
            $price = $this->helperPrice->getFormatPrice($basePrice, 2);
            $donationDetails['donation_value'] = $price;

            $details = unserialize($this->getOrder()->getMageworxDonationDetails());
            if (!empty($details) && !empty($details['charity_title'])){
                $donationDetails['charity_value'] = $details['charity_title'];
            }
            return $donationDetails;
        } else return null;
    }
}