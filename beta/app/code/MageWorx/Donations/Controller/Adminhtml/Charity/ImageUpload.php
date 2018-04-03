<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Controller\Adminhtml\Charity;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\Donations\Api\CharityRepositoryInterface;
use MageWorx\Donations\Controller\Adminhtml\Charity;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use MageWorx\Donations\Helper\Image;

class ImageUpload extends Charity
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Image
     */
    protected $helper;

    /**
     * ImageUpload constructor.
     *
     * @param Registry                   $coreRegistry
     * @param CharityRepositoryInterface $charityRepository
     * @param Context                    $context
     * @param LoggerInterface            $logger
     * @param StoreManagerInterface      $storeManager
     * @param RawFactory                 $resultRawFactory
     * @param Image                      $helper
     */
    public function __construct(
        Registry $coreRegistry,
        CharityRepositoryInterface $charityRepository,
        Context $context,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        RawFactory $resultRawFactory,
        Image $helper
    ) {
        parent::__construct($coreRegistry, $charityRepository, $context, $logger);
        $this->storeManager = $storeManager;
        $this->resultRawFactory = $resultRawFactory;
        $this->helper = $helper;
    }

    /**
     * Upload image action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            /** @var Uploader $uploader */
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $result = $uploader->save($mediaDirectory->getAbsolutePath($this->helper->getBaseMediaPath()));
            unset($result['path']);

            $result['url'] = $this->helper->getMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));

        return $response;
    }
}
