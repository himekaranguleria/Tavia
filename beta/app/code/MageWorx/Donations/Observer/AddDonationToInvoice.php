<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddDonationToInvoice implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getBaseMageworxDonationAmount() > 0) {
            $order = $invoice->getOrder();
            $order->setMageworxDonationInvoiced($order->getMageworxDonationInvoiced() +
                $invoice->getMageworxDonationAmount());
            $order->setBaseMageworxDonationInvoiced($order->getBaseMageworxDonationInvoiced() +
                $invoice->getBaseMageworxDonationAmount());
        }

        return $this;
    }
}
