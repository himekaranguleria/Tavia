<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Totals\Order;

use Magento\Framework\DataObject\Factory as DataObjectFactory;
use MageWorx\Donations\Model\CharityRepository;

class Donation extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var CharityRepository
     */
    protected $charityRepository;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param DataObjectFactory                                $dataObjectFactory
     * @param CharityRepository                                $charityRepository
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        DataObjectFactory $dataObjectFactory,
        CharityRepository $charityRepository,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->charityRepository = $charityRepository;
        parent::__construct($context, $registry, $data);
    }


    /**
     * Add MageWorx Donation Amount to Order
     *
     * @return void
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Order\Totals $totalsBlock */
        $totalsBlock = $this->getParentBlock();
        $order = $totalsBlock->getOrder();

        $label = __('Donation');
        $donationDetails = unserialize($order->getMageworxDonationDetails());

        if (!empty($donationDetails['charity_id'])) {
            $charityId = $donationDetails['charity_id'];

            /** @var \MageWorx\Donations\Model\Charity $charity */
            $charity = $this->charityRepository->getById($charityId);
            $charityTitle = $charity->getName();

            if ($charityTitle) {
                $label .= ' (' . $charityTitle . ')';
            } else {
                $label .= ' (' . $donationDetails['charity_title'] . ')';
            }
        }

        if ((float)$order->getMageworxDonationAmount()) {
            $data = [
                'code' => 'mageworx_donation_amount',
                'label' => $label,
                'value' => $order->getMageworxDonationAmount(),
                'base_value' => $order->getBaseMageworxDonationAmount()
            ];

            /** @var \Magento\Framework\DataObject $dataObject */
            $dataObject = $this->dataObjectFactory->create($data);

            $totalsBlock->addTotalBefore($dataObject, 'grand_total');
        }
    }
}