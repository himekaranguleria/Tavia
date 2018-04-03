<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CleanDonationDataFromSession implements ObserverInterface
{
    /**
     * @var \MageWorx\Donations\Helper\Donation
     */
    protected $donationHelper;

    /**
     * AddDonationToOrder constructor.
     *
     * @param \MageWorx\Donations\Helper\Donation $donationHelper
     */
    public function __construct(
        \MageWorx\Donations\Helper\Donation $donationHelper
    ) {
        $this->donationHelper = $donationHelper;
    }

    /**
     * Delete donation data from session
     *
     * @param $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->clearSessionMageworxDonation();
        return $this;
    }

    /**
     * Clear donation data from session
     *
     * @return void
     */
    protected function clearSessionMageworxDonation()
    {
        $this->donationHelper->getCurrentSession()->setMageworxDonationDetails(null);
    }
}
