<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model\Total\Invoice;

class Donation extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();

        if ($order->getMageworxDonationAmount() > 0 && $order->getMageworxDonationInvoiced() <
            ($order->getMageworxDonationAmount() - $order->getMageworxDonationCanceled())
        ) {
            $invoice->setMageworxDonationAmount($order->getMageworxDonationAmount() -
                $order->getMageworxDonationInvoiced() - $order->getMageworxDonationCanceled());
            $invoice->setBaseMageworxDonationAmount($order->getBaseMageworxDonationAmount() -
                $order->getBaseMageworxDonationInvoiced() - $order->getBaseMageworxDonationCanceled());
            $invoice->setMageworxDonationDetails($order->getMageworxDonationDetails());

            $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getMageworxDonationAmount());
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getBaseMageworxDonationAmount());
        } else {
            $invoice->setMageworxDonationAmount(0);
            $invoice->setBaseMageworxDonationAmount(0);
            $invoice->setMageworxDonationDetails('');
        }
        return $this;
    }
}
