<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Controller\Adminhtml\Charity;

use MageWorx\Donations\Api\CharityRepositoryInterface;
use MageWorx\Donations\Controller\Adminhtml\Charity;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use MageWorx\Donations\Helper\Image as HelperImage;
use \Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends Charity
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var CharityRepositoryInterface
     */
    protected $charityRepository;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var HelperImage
     */
    protected $helper;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Registry                             $coreRegistry
     * @param CharityRepositoryInterface           $charityRepository
     * @param Context                              $context
     * @param \Psr\Log\LoggerInterface             $logger
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param File                                 $file
     * @param HelperImage                          $helper
     * @param Filesystem                           $filesystem
     */
    public function __construct(
        Registry $coreRegistry,
        CharityRepositoryInterface $charityRepository,
        Context $context,
        LoggerInterface $logger,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        File $file,
        HelperImage $helper,
        Filesystem $filesystem
    ) {
        parent::__construct($coreRegistry, $charityRepository, $context, $logger);
        $this->dataObjectFactory = $dataObjectFactory;
        $this->charityRepository = $charityRepository;
        $this->file = $file;
        $this->helper = $helper;
        $this->filesystem = $filesystem;
    }

    /**
     * Charity save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('mageworx_donations/*/');
        }

        try {
            $this->_eventManager->dispatch(
                'adminhtml_controller_mageworx_donations_charity_prepare_save',
                ['request' => $this->getRequest()]
            );
            $postData = $this->getRequest()->getPostValue();
            $id = $this->getRequest()->getParam('charity_id');

            if ($id) {
                /** @var $model \MageWorx\Donations\Model\Charity */
                $model = $this->charityRepository->getById($id);
            } else {
                /** @var $model \MageWorx\Donations\Model\Charity */
                $model = $this->charityRepository->getEmptyEntity();
            }
            $prepareData = $this->prepareData($postData);
            $model->addData($prepareData);
            $this->_session->setPageData($model->getData());

            if (!empty($postData['filename']) && empty($prepareData['filename'])) {
                /* delete image charity on server */
                $this->deleteFiles($postData['filename']);
            }

            $this->charityRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the charity.'));
            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('mageworx_donations/*/edit', ['charity_id' => $model->getId()]);
                return;
            }
            $this->_redirect('mageworx_donations/*/');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('charity_id');
            if (!empty($id)) {
                $this->_redirect('mageworx_donations/*/edit', ['charity_id' => $id]);
            } else {
                $this->_redirect('mageworx_donations/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the charity data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($prepareData) ? $prepareData : [];
            $this->_session->setPageData($data);
            $this->_redirect('mageworx_donations/*/edit', ['charity_id' => $this->getRequest()->getParam('charity_id')]);
            return;
        }
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareData($data)
    {
        if (!empty($data['image'][0]['file'])) {
            $data['filename'] = $data['image'][0]['file'];
        } elseif (!empty($data['image'][0]['path'])) {
            $data['filename'] = $data['image'][0]['path'];
        } else {
            $data['filename'] = '';
        }

        unset($data['date_added']);
        unset($data['date_updated']);

        return $data;
    }


    /**
     * Delete chariry files
     *
     * @param String $fileName
     */
    protected function deleteFiles($fileName)
    {
        $dir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        $pathFile = $this->helper->getImagePath($fileName, HelperImage::IMAGE_TYPE_THUMBNAIL);

        /* delete image 75x75px */
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }

        $pathFile = $this->helper->getImagePath($fileName, HelperImage::IMAGE_TYPE_FORM_PREVIEW);

        /* delete image 116x116px */
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }

        $pathFile = $this->helper->getImagePath($fileName, HelperImage::IMAGE_TYPE_FRONTEND_PREVIEW);

        /* delete image 150x150px */
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }

        $pathFile = $this->helper->getImagePath($fileName);

        /* delete image 300x300px*/
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }

        $pathFile = $this->helper->getMainImagePath($fileName);

        /* delete main image */
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }
    }
}

