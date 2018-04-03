<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

class Image extends AbstractHelper
{
    const MEDIA_TYPE_CONFIG_NODE      = 'images';
    const IMAGE_TYPE_THUMBNAIL        = 'thumbnail_image';
    const IMAGE_TYPE_FORM_PREVIEW     = 'preview_in_form';
    const IMAGE_TYPE_FRONTEND_PREVIEW = 'preview_frontend';
    const BASE_MEDIA_PATH_CHARITY     = 'mageworx/donations/charity';

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Image constructor.
     *
     * @param Context                       $context
     * @param StoreManagerInterface         $storeManager
     * @param ImageFactory                  $imageFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ImageFactory $imageFactory,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Get image url for specified type, width or height
     *
     * @param      $path
     * @param null $type
     * @param int  $height
     * @param int  $width
     *
     * @return string
     */
    public function getImageUrl($path, $type = null, $height = 300, $width = 300)
    {
        if (!$path) {
            return '';
        }

        if ($type !== null) {
            $attributes = $this->getAttributesByType($type);
            $height = !empty($attributes['height']) ? $attributes['height'] : $height;
            $width = !empty($attributes['width']) ? $attributes['width'] : $width;
        }

        $filePath = $this->getMediaPath($path);
        $pathArray = explode('/', $filePath);
        $fileName = array_pop($pathArray);
        $directoryPath = implode('/', $pathArray);
        $imagePath = $directoryPath . '/' . $width . 'x' . $height . '/';

        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $imgAbsolutePath = $mediaDirectory->getAbsolutePath($imagePath);
        $fileAbsolutePath = $mediaDirectory->getAbsolutePath($filePath);

        $imgFilePath = $imgAbsolutePath . $fileName;
        if (!file_exists($imgFilePath)) {
            $this->createImageFile($fileAbsolutePath, $imgAbsolutePath, $fileName, $width, $height);
        }

        return $this->getUrl($imagePath . $fileName);
    }


    /**
     * Get image path
     *
     * @param      $path
     * @param null $type
     * @param int  $height
     * @param int  $width
     *
     * @return string
     */
    public function getImagePath($path, $type = null, $height = 300, $width = 300)
    {
        if (!$path) {
            return '';
        }

        if ($type !== null) {
            $attributes = $this->getAttributesByType($type);
            $height = !empty($attributes['height']) ? $attributes['height'] : $height;
            $width = !empty($attributes['width']) ? $attributes['width'] : $width;
        }

        $filePath = $this->getMediaPath($path);
        $pathArray = explode('/', $filePath);
        $fileName = array_pop($pathArray);
        $directoryPath = implode('/', $pathArray);
        $imagePath = $directoryPath . '/' . $width . 'x' . $height . '/'. $fileName;

        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $imgAbsolutePath = $mediaDirectory->getAbsolutePath($imagePath);

        return $imgAbsolutePath;
    }


    /**
     * Get main image path
     *
     * @param      $path
     *
     * @return string
     */
    public function getMainImagePath($path)
    {
        if (!$path) {
            return '';
        }

        $filePath = $this->getMediaPath($path);
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileAbsolutePath = $mediaDirectory->getAbsolutePath($filePath);

        return $fileAbsolutePath;
    }

    /**
     * Get image attributes by type
     *
     * @param $type
     *
     * @return array
     */
    private function getAttributesByType($type)
    {
        $data = [];
        switch ($type) {
            case static::IMAGE_TYPE_THUMBNAIL:
                $data['width'] = 75;
                $data['height'] = 75;
                break;
            case static::IMAGE_TYPE_FORM_PREVIEW:
                $data['width'] = 116;
                $data['height'] = 148;
                break;
            case static::IMAGE_TYPE_FRONTEND_PREVIEW:
                $data['width'] = 150;
                $data['height'] = 150;
                break;
            default:
                $data['width'] = 300;
                $data['height'] = 300;
                break;
        }

        return $data;
    }

    /**
     * Create image based on size
     *
     * @param string $origFilePath
     * @param string $imagePath
     * @param string $newFileName
     * @param        $width
     * @param        $height
     *
     */
    private function createImageFile($origFilePath, $imagePath, $newFileName, $width, $height)
    {
        try {
            $image = $this->imageFactory->create($origFilePath);
            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->keepTransparency(true);
            $image->constrainOnly(false);
            $image->backgroundColor([255, 255, 255]);
            $image->quality(100);
            $image->resize($width, $height);
            $image->constrainOnly(true);
            $image->keepAspectRatio(true);
            $image->keepFrame(false);
            $image->save($imagePath, $newFileName);
        } catch (\Exception $e) {
            $this->_logger->error($e);
        }
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/' . $this->prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl('media') . $this->getBaseMediaPath();
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getUrl($file)
    {
        return rtrim($this->getBaseUrl(), '/') . '/' . ltrim($this->prepareFile($file), '/');
    }

    /**
     * Filesystem directory path of option value images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaPath()
    {
        return static::BASE_MEDIA_PATH_CHARITY;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getMediaPath($file)
    {
        return $this->getBaseMediaPath() . '/' . $this->prepareFile($file);
    }

    /**
     * Get file size in bytes. Used in uploader element (form)
     *
     * @param $image
     *
     * @return int
     */
    public function getImageOrigSize($image)
    {
        $fullPathToImage = $this->getMediaPath($image);
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileAbsolutePath = $mediaDirectory->getAbsolutePath($fullPathToImage);
        if (file_exists($fileAbsolutePath)) {
            $fileSize = @filesize($fileAbsolutePath);
        } else {
            return 0;
        }

        return $fileSize;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl('media');
    }

}
