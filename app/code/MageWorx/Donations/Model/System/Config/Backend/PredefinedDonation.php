<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Model\System\Config\Backend;

use MageWorx\Donations\Helper\PredefinedDonation as HelperPredefinedDonation;

/**
 * Backend for serialized array data
 */
class PredefinedDonation extends \Magento\Framework\App\Config\Value
{
    /**
     * @var HelperPredefinedDonation
     */
    protected $helperPredefinedDonation;

    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface      $config
     * @param \Magento\Framework\App\Cache\TypeListInterface          $cacheTypeList
     * @param HelperPredefinedDonation                                $helperPredefinedDonation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        HelperPredefinedDonation $helperPredefinedDonation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperPredefinedDonation = $helperPredefinedDonation;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     *
     * @return array
     */
    protected function makeArrayFieldValue($value)
    {
        $value = $this->helperPredefinedDonation->unserializeValue($value);
        if (!$this->helperPredefinedDonation->isEncodedArrayFieldValue($value)) {
            $value = $this->helperPredefinedDonation->encodeArrayFieldValue($value);
        }
        $value = $this->helperPredefinedDonation->filterCorrectData($value);
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     *
     * @return string
     */
    protected function makeStorableArrayFieldValue($value)
    {
        if ($this->helperPredefinedDonation->isEncodedArrayFieldValue($value)) {
            $value = $this->helperPredefinedDonation->filterCorrectData($value);
            $value = $this->helperPredefinedDonation->decodeArrayFieldValue($value);
        }
        $value = $this->helperPredefinedDonation->serializeValue($value);
        return $value;
    }
}
