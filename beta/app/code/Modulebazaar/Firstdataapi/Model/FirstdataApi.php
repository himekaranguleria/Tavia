<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Modulebazaar\Firstdataapi\Model;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Payment\Model\Method\TransparentInterface;

/**
 * Pay In Store payment method model
 */
class FirstdataApi extends \Magento\Payment\Model\Method\Cc
{

    /**
     * Payment code
     *
     * @var string
     */
    const CODE = 'firstdataapi';
    protected $_code = 'firstdataapi';

    protected $_formBlockType = 'Magento\Payment\Block\Transparent\Info';

  
    protected $_infoBlockType = 'Modulebazaar\Firstdataapi\Block\Info';
  
    protected $_isOffline = false;

    protected $_canAuthorize            = false;
    protected $_canCapture              = false;

    protected $_canRefund               = false;


    protected $_canSaveCc = false;
    protected $_canUseInternal          = true;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        ModuleListInterface $moduleList,
        TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );
    }
    protected function _construct()
    {
        $this->_init('Modulebazaar\Firstdataapi\Model\ResourceModel\FirstdataApi');
    }
}
