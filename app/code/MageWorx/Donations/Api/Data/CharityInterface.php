<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Api\Data;

/**
 * Charity interface
 * @api
 */
interface CharityInterface
{
    /**
     * Constants for keys of data array.
     */
    const CHARITY_ID   = 'charity_id';
    const NAME         = 'name';
    const DESCRIPTION  = 'description';
    const FILENAME     = 'filename';
    const SORT_ORDER   = 'sort_order';
    const IS_ACTIVE    = 'is_active';
    const DATE_ADDED   = 'date_added';
    const DATE_UPDATED = 'date_updated';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     */
    public function setId($id);

    /**
     * Set sort order
     *
     * @param string $sortOrder
     */
    public function setSortOrder($sortOrder);

    /**
     * Get status
     *
     * @return int|null
     */
    public function getIsActive();

    /**
     * Set status
     *
     * @param int|bool $isActive
     */
    public function setIsActive($isActive);

    /**
     * Get is enable date field
     *
     * @return int|null
     */
    public function getDateAdded();

    /**
     * Set enable date field
     *
     * @param int $dateAdded
     */
    public function setDateAdded($dateAdded);

    /**
     * Get url to the image, if exist
     *
     * @return string
     */
    public function getImageUrl();

}