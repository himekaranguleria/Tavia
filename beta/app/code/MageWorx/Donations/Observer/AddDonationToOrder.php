<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddDonationToOrder implements ObserverInterface
{
    /**
     * Add donation data to order data
     *
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();

        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        if ($address->getMageworxDonationAmount()) {
            $order->setMageworxDonationAmount($address->getMageworxDonationAmount());
            $order->setBaseMageworxDonationAmount($address->getBaseMageworxDonationAmount());
            $order->setMageworxDonationDetails($address->getMageworxDonationDetails());
        }

        return $this;
    }
}
