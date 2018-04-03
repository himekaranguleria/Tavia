<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Api;

interface DonationInterface
{
    /**
     * Add donation
     *
     * @param float $donation
     * @param float $charityId
     */
    public function addDonationToQuote($donation, $charityId);

    /**
     * Delete donation
     *
     */
    public function deleteDonationFromQuote();
}