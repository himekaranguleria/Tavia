<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Api;

use MageWorx\Donations\Model\Charity;

interface CharityRepositoryInterface
{
    /**
     * Save charity.
     *
     * @param Charity $charity
     *
     * @return Charity
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Charity $charity);

    /**
     * Retrieve charity.
     *
     * @param int $charityId
     *
     * @return Charity
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($charityId);

    /**
     * Get empty Charity
     *
     * @return Charity|Data\CharityInterface
     */
    public function getEmptyEntity();

    /**
     * Delete Charity
     *
     * @param Charity $charity
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(Charity $charity);

    /**
     * Delete Charity by given Charity Identity
     *
     * @param string $charityId
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($charityId);
    
    /**
     * Create a new charity
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCharity();

    /**
     * Get list charity
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListCharity();

}
