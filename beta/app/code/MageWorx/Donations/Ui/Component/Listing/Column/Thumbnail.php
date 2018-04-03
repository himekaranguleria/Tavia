<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Ui\Component\Listing\Column;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use MageWorx\Donations\Helper\Image as Helper;

class Thumbnail extends Column
{
    const ALT_FIELD = 'name';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param ContextInterface      $context
     * @param UiComponentFactory    $uiComponentFactory
     * @param Image                 $imageHelper
     * @param UrlInterface          $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param Helper                $helper
     * @param array                 $components
     * @param array                 $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Image $imageHelper,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        Helper $helper,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = 'filename';
            foreach ($dataSource['data']['items'] as &$item) {
                $url = '';
                $thumbnailUrl = '';
                $link = $this->urlBuilder->getUrl(CharityActions::URL_PATH_EDIT, ['charity_id' => $item['charity_id']]);
                if ($item[$fieldName] != '') {
                    $url = $this->helper->getImageUrl($item[$fieldName]);
                    $thumbnailUrl = $this->helper->getImageUrl($item[$fieldName], Helper::IMAGE_TYPE_THUMBNAIL);
                }
                $item['image_src'] = $thumbnailUrl;
                $item['image_alt'] = $this->getAlt($item) ?: '';
                $item['image_link'] = $link;
                $item['image_orig_src'] = $url;
            }
        }
        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        return isset($row[static::ALT_FIELD]) ? $row[static::ALT_FIELD] : null;
    }
}
