<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model\Total\Creditmemo;

class Donation extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getMageworxDonationAmount() > 0 && $order->getMageworxDonationRefunded() <
            $order->getMageworxDonationInvoiced()
        ) {
            $creditmemo->setMageworxDonationAmount($order->getMageworxDonationInvoiced() -
                $order->getMageworxDonationRefunded());
            $creditmemo->setBaseMageworxDonationAmount($order->getBaseMageworxDonationInvoiced() -
                $order->getBaseMageworxDonationRefunded());
            $creditmemo->setMageworxDonationDetails($order->getMageworxDonationDetails());

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getMageworxDonationAmount());
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() +
                $creditmemo->getBaseMageworxDonationAmount());
        } else {
            $creditmemo->setMageworxDonationAmount(0);
            $creditmemo->setBaseMageworxDonationAmount(0);
            $creditmemo->setMageworxDonationDetails('');
        }
        return $this;
    }
}
