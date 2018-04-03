<?php
/**
 * Narvar Activator Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model;

use Narvar\Connect\Helper\Handshake as HandshakeHelper;
use Narvar\Connect\Helper\Config\Account as AccountHelper;
use Narvar\Connect\Helper\Config\Returns as ReturnsHelper;
use Narvar\Connect\Helper\Config\Activation as ActivationHelper;
use Narvar\Connect\Helper\Base as ExtensionHelper;
use Narvar\Connect\Helper\Formatter;
use Narvar\Connect\Helper\Data as DataHelper;
use Narvar\Connect\Helper\ConnectorFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\App\ProductMetadataInterface;

class Activator
{

    /**
     * Constant String value
     */
    const VALUE = 'value';

    /**
     * Constant String fields
     */
    const FIELDS = 'fields';

    /**
     *
     * @var Narvar\Connect\Helper\Base as ExtensionHelper;
     */
    private $extensionHelper;

    /**
     *
     * @var Narvar\Connect\Helper\Config\Activation as ActivationHelper;
     */
    private $activationHelper;

    /**
     *
     * @var Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     *
     * @var \Narvar\Connect\Helper\Connector
     */
    private $connector;

    /**
     *
     * @var \Narvar\Connect\Helper\Formatter;
     */
    private $formatter;

    /**
     *
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $cacheTypeList;

    /**
     *
     * @var Magento\Framework\App\Cache\Frontend\Pool
     */
    private $cacheFrontendPool;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;
    
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     *
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;
    
    /**
     * Constructor
     *
     * @param ExtensionHelper $extensionHelper
     * @param EncryptorInterface $encryptor
     * @param ConnectorFactory $connector
     * @param ActivationHelper $activationHelper
     * @param Formatter $formatter
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param ManagerInterface $messageManager
     * @param JsonHelper $jsonHelper
     * @param ProductMetadataInterface $productMetaData
     */
    public function __construct(
        ExtensionHelper $extensionHelper,
        EncryptorInterface $encryptor,
        ConnectorFactory $connector,
        ActivationHelper $activationHelper,
        Formatter $formatter,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        ManagerInterface $messageManager,
        JsonHelper $jsonHelper,
        ProductMetadataInterface $productMetaData
    ) {
        $this->encryptor = $encryptor;
        $this->extensionHelper = $extensionHelper;
        $this->activationHelper = $activationHelper;
        $this->connector = $connector;
        $this->formatter = $formatter;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->messageManager = $messageManager;
        $this->jsonHelper = $jsonHelper;
        $this->productMetaData = $productMetaData;
    }

    /**
     * Mathod fire when the configuration updated
     *
     * @param \Magento\Config\Model\Config $config
     */
    public function activationProcess($config)
    {
        if ($config->getSection() === HandshakeHelper::CONFIG_SECTION) {
            $configGroups = $config->getGroups();
            $accountConfig = $configGroups[AccountHelper::CONFIG_GRP][self::FIELDS];
            $returnConfig = $configGroups[ReturnsHelper::CONFIG_GRP][self::FIELDS];
            $handshakeParams = $this->getHandshakeParams($returnConfig);
            $validationParams = $this->getAccountValidationParams($accountConfig);
            $responseMsg = $this->connector->create(['data' => $validationParams])->post(
                HandshakeHelper::SLUG,
                $this->jsonHelper->jsonEncode($handshakeParams)
            );
            $this->enableModule($config, $configGroups, $handshakeParams);
            $this->messageManager->addSuccess(nl2br($responseMsg));
        }
    }

    /**
     * Method to enable the module in configuration
     *
     * @param \Magento\Config\Model\Config $config
     * @param array $configGroups
     * @param array $postParams
     */
    private function enableModule($config, $configGroups, $postParams)
    {
        $returnGroup = $configGroups[ReturnsHelper::CONFIG_GRP][self::FIELDS];
        $returnGroup[ReturnsHelper::AUTH_KEY_ENCRYPT][self::VALUE] = $postParams[HandshakeHelper::AUTH_KEY];
        $returnGroup[ReturnsHelper::AUTH_TOKEN][self::VALUE] = $postParams[HandshakeHelper::AUTH_TOKEN];
        
        $activationGroup = $configGroups[ActivationHelper::CONFIG_GRP][self::FIELDS];
        
        $enablePath = $this->activationHelper->getIsActivated(ExtensionHelper::CONFIG_REQ_PATH);
        if (! $config->getConfigDataValue($enablePath)) {
            $activationGroup[ActivationHelper::ACTIVATION_DATE][self::VALUE] = $this->formatter->currentDate();
            $activationGroup[ActivationHelper::IS_ACTIVATED][self::VALUE] = DataHelper::ENABLE_VALUE;
        }
        
        $configGroups[ActivationHelper::CONFIG_GRP][self::FIELDS] = $activationGroup;
        $configGroups[ReturnsHelper::CONFIG_GRP][self::FIELDS] = $returnGroup;
        
        $this->cacheTypeList->cleanType('config');
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        
        $config->setGroups($configGroups);
    }

    /**
     * Method to get Narvar Account New Params
     *
     * @param type $accountConfig
     * @return type
     */
    private function getAccountValidationParams($accountConfig)
    {
        return [
            'url' => $accountConfig[AccountHelper::NARVAR_API_ENDPOINT][self::VALUE],
            'username' => $accountConfig[AccountHelper::NARVAR_ACCOUNT_ID][self::VALUE],
            'password' => $accountConfig[AccountHelper::NARVAR_AUTH_TOKEN][self::VALUE]
        ];
    }

    /**
     * Get the handshake params
     *
     * @param array $returnConfig
     * @return array of return configuration params
     */
    private function getHandshakeParams($returnConfig)
    {
        $version = sprintf('%s-%s', $this->productMetaData->getVersion(), $this->productMetaData->getEdition());
        $authKey = $returnConfig[ReturnsHelper::AUTH_KEY][self::VALUE];
        $baseUrl = $this->extensionHelper->getBaseUrl();
        $returnUrl = sprintf('%s%s%s', $baseUrl, HandshakeHelper::RETURN_SLUG_REST, HandshakeHelper::RETURN_SLUG);
        
        return [
            HandshakeHelper::VERSION => $version,
            HandshakeHelper::BASE_URL => $this->extensionHelper->getBaseUrl(),
            HandshakeHelper::RETURN_REQ_URL => $returnUrl,
            HandshakeHelper::AUTH_KEY => str_replace(':', '', $this->encryptor->encrypt($authKey)),
            HandshakeHelper::AUTH_TOKEN => $this->generateAuthToken()
        ];
    }

    /**
     * Method to generate the Auth Token for access Magento return request
     *
     * @return string
     */
    private function generateAuthToken()
    {
        return bin2hex(openssl_random_pseudo_bytes(48));
    }
}
