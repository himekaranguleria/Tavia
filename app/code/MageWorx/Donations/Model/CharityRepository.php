<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Model;

use MageWorx\Donations\Api\CharityRepositoryInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\Donations\Model\ResourceModel\Charity as ResourceCharity;
use MageWorx\Donations\Model\ResourceModel\Charity\CollectionFactory as CharityCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CharityRepository implements CharityRepositoryInterface
{
    /**
     * @var ResourceCharity
     */
    protected $resource;

    /**
     * @var CharityFactory
     */
    protected $charityFactory;

    /**
     * @var CharityCollectionFactory
     */
    protected $charityCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceCharity               $resource
     * @param CharityFactory                $charityFactory
     * @param CharityCollectionFactory      $charityCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper              $dataObjectHelper
     * @param DataObjectProcessor           $dataObjectProcessor
     * @param StoreManagerInterface         $storeManager
     */
    public function __construct(
        ResourceCharity $resource,
        CharityFactory $charityFactory,
        CharityCollectionFactory $charityCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->charityFactory = $charityFactory;
        $this->charityCollectionFactory = $charityCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save charity.
     *
     * @param Charity $charity
     *
     * @return Charity
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Charity $charity)
    {
        try {
            /** @var \MageWorx\Donations\Model\Charity $charity */
            $this->resource->save($charity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the charity: %1',
                $exception->getMessage()
            ));
        }

        return $charity;
    }

    /**
     * Load Charity data by given Charity Identity
     *
     * @param string $charityId
     *
     * @return Charity
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($charityId)
    {
        try {
            /** @var Charity $charity */
            $charity = $this->charityFactory->create();
            $charity->getResource()->load($charity, $charityId);

            return $charity;
        } catch (NoSuchEntityException $e) {
            if (!$charity->getId()) {
                throw new NoSuchEntityException(__('Charity with id "%1" does not exist.', $charityId));
            }
        }
    }

    /**
     * Get empty Charity
     *
     * @return Charity
     */
    public function getEmptyEntity()
    {
        /** @var Charity $charity */
        $charity = $this->charityFactory->create();

        return $charity;
    }

    /**
     * Delete Charity
     *
     * @param Charity $charity
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(Charity $charity)
    {
        try {
            $this->resource->delete($charity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the charity: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

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
    public function deleteById($charityId)
    {
        return $this->delete($this->getById($charityId));
    }

    /**
     * Create a new charity
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCharity()
    {
        // TODO: Implement createCharity() method.
    }

    /**
     * Get list charity
     *
     * @return \MageWorx\Donations\Api\Data\CharityInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListCharity()
    {
        try {
            /** @var \MageWorx\Donations\Model\ResourceModel\Charity\Collection $charityCollection */
            $charityCollection = $this->charityCollectionFactory->create();
            $charityCollection->addFieldToFilter('is_active', 1);
            $charityCollection->load();

            return $charityCollection;
        } catch (NoSuchEntityException $e) {
                throw new NoSuchEntityException(__('Problem with charity collection'));
        }
    }
}