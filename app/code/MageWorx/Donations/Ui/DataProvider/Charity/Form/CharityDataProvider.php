<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Ui\DataProvider\Charity\Form;

use MageWorx\Donations\Model\ResourceModel\Charity\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\Donations\Helper\Image;

/**
 * Class CharityDataProvider
 */
class CharityDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Image
     */
    protected $helperImage;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param CollectionFactory     $collectionFactory
     * @param PoolInterface         $pool
     * @param StoreManagerInterface $storeManager
     * @param Image                 $helperImage
     * @param array                 $meta
     * @param array                 $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Image $helperImage,
        PoolInterface $pool,

        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->collection = $collectionFactory->create();
        $this->helperImage = $helperImage;
        $this->storeManager = $storeManager;
    }

    /**
     * Get all meta
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $modifiers = $this->pool->getModifiersInstances();
        /** @var ModifierInterface $modifier */
        foreach ($modifiers as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * Get charity data
     *
     * @return array
     */
    public function getData()
    {
        if (!empty($this->data)) {
            return $this->data;
        }

        $items = $this->collection->getItems();
        /** @var \MageWorx\Donations\Model\Charity $charity */
        foreach ($items as $charity) {
            $this->data[$charity->getId()] = $charity->getData();
            $image = $charity->getImage();
            if ($image) {
                $imagePathParts = explode('/', $image);
                $imageName = array_pop($imagePathParts);
                $imageData = [
                    'name' => $imageName,
                    'url' => $this->helperImage->getImageUrl($image, 'preview_in_form'),
                    'path' => $image,
                    'size' => $this->helperImage->getImageOrigSize($image)
                ];
                $this->data[$charity->getId()]['image'] = [$imageData];
            }
        }

        $modifiers = $this->pool->getModifiersInstances();
        /** @var ModifierInterface $modifier */
        foreach ($modifiers as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }
}
