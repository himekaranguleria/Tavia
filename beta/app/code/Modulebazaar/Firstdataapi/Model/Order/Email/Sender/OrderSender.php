<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Modulebazaar\Firstdataapi\Model\Order\Email\Sender;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * Sends order email to the customer.
     *
     * Email will be sent immediately in two cases:
     *
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     *
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     *
     * @param Order $order
     * @param bool  $forceSyncMode
     *
     * @return bool
     */
    public function send(Order $order, $forceSyncMode = true)
    {
        if (!$this->globalConfig->getValue(
            'sales_email/general/async_sending'
        )
            || $forceSyncMode
        ) {
            $payment = $order->getPayment();
            if ($payment->getMethod() != 'firstdataapi') {
                if ($this->checkAndSend($order)) {
                    $this->orderResource->saveAttribute(
                        $order,
                        ['send_email', 'email_sent']
                    );
                    return true;
                }
                   return true;
            }
        }

        $this->orderResource->saveAttribute($order, 'send_email');

        return false;
    }

    public function sendConfirmationFinal(Order $order, $forceSyncMode = true)
    {
        if (!$this->globalConfig->getValue(
            'sales_email/general/async_sending'
        )
            || $forceSyncMode
        ) {
            if ($this->checkAndSend($order)) {
                $this->orderResource->saveAttribute(
                    $order,
                    ['send_email', 'email_sent']
                );
                return true;
            }
        }
        return false;
    }
}
