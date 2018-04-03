<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddDonationToOrderAdmin implements ObserverInterface
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

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // check submit donation when admin/sales_order_create
        $post = $observer->getEvent()->getRequest();

        if (!empty($post['donation'])) {
            $donation = $post['donation'];
            $charity_id = !empty($post['charity_id']) ? $post['charity_id'] : null;

            $this->donationHelper->getNewDonationObject()->addDonationToQuote($donation, $charity_id);
        } elseif (isset($post['delete_donation']) && $post['delete_donation']) {
            $this->donationHelper->getNewDonationObject()->deleteDonationFromQuote();
        }

        return $this;

    }
}
