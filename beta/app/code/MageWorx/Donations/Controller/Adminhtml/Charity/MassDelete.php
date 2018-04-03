<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Controller\Adminhtml\Charity;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\Donations\Api\CharityRepositoryInterface;
use Magento\Framework\Filesystem\Driver\File;
use MageWorx\Donations\Helper\Image;
use Magento\Framework\App\Filesystem\DirectoryList;
use MageWorx\Donations\Model\Charity;
use MageWorx\Donations\Helper\Image as Helper;
use \Magento\Framework\Filesystem;
use MageWorx\Donations\Model\ResourceModel\Charity\CollectionFactory;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $charityCollectionFactory;

    /**
     * @var CharityRepositoryInterface
     */
    protected $charityRepository;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Image
     */
    protected $helper;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Context                                                           $context
     * @param Filter                                                            $filter
     * @param CollectionFactory $charityCollectionFactory
     * @param CharityRepositoryInterface                                        $charityRepository
     * @param File                                                              $file
     * @param Image                                                             $helper
     * @param Filesystem                                                        $filesystem
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $charityCollectionFactory,
        CharityRepositoryInterface $charityRepository,
        File $file,
        Image $helper,
        Filesystem $filesystem
    ) {
        $this->filter = $filter;
        $this->charityRepository = $charityRepository;
        $this->charityCollectionFactory = $charityCollectionFactory;
        $this->file = $file;
        $this->helper = $helper;
        $this->filesystem = $filesystem;

        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->charityCollectionFactory->create()
        );
        $size = $collection->getSize();
        /** @var \MageWorx\Donations\Model\Charity $charity */
        foreach ($collection as $charity) {
            $this->charityRepository->delete($charity);

            /* delete images */
            $this->deleteFiles($charity);
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $this->messageManager
            ->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $size)
            );
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Donations::charity');
    }

    /**
     * Delete chariry files
     *
     * @param Charity $charity
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function deleteFiles(Charity $charity)
    {
        $dir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = $charity->getFilename();

        $pathFile = $this->helper->getImagePath($fileName, Helper::IMAGE_TYPE_THUMBNAIL);

        /* delete image 75x75px */
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }

        $pathFile = $this->helper->getImagePath($fileName, Helper::IMAGE_TYPE_FORM_PREVIEW);

        /* delete image 116x116px */
        if ($dir->isFile($dir->getRelativePath($pathFile))) {
            $this->file->deleteFile($pathFile);
        }

        $pathFile = $this->helper->getImagePath($fileName, Helper::IMAGE_TYPE_FRONTEND_PREVIEW);

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
