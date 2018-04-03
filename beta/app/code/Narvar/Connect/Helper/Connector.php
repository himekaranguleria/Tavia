<?php
/**
 * Narvar Api Connector Helper
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Helper;

use Narvar\Connect\Exception\ConnectorException;
use Narvar\Connect\Helper\Base as ExtensionHelper;
use Narvar\Connect\Helper\Config\Account as AccountHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Request;
use Zend\Uri\Http as HttpUri;
use Zend\Http\Response;

class Connector extends \Magento\Framework\DataObject
{

    /**
     * Status Error Value
     */
    const STATUS_ERROR = 'error';

    /**
     * Status Variable Value
     */
    const STATUS = 'status';

    /**
     * Response Array Key Name Messages
     */
    const MESSAGES = 'messages';

    /**
     * Response Array Key Name Message
     */
    const MSG_MESSAGE = 'message';

    /**
     * Narvar endpoint api Url
     *
     * @var url
     */
    private $narvarApiUrl;

    /**
     * The Adapter is used to perform connect the given url
     *
     * @var \Zend\Http\Client\Adapter\Curl
     */
    private $curl;

    /**
     * CURL Connection Time Out
     *
     * @var integer
     */
    private $timeout = 60;

    /**
     * Receive the the Response form API Call
     *
     * @var multitype
     */
    private $response;

    /**
     * List of headers for api call
     *
     * @var array
     */
    private static $headers = [
        'Content-Type' => 'application/json',
        'charset' => 'utf-8',
        'Accept-language' => 'en-US'
    ];

    /**
     * List of headers for file upload api call
     * @var array
     */
    private static $fileUploadHeaders = [
        'Accept-Encoding' => 'application/gzip',
        'Content-Type' => 'multipart/form-data'
    ];

    /**
     * Values of allowed constuctor parameter keys
     *
     * @var array
     */
    private $validParams = [
        'url',
        'username',
        'password'
    ];
    
    /**
     * @var \Narvar\Connect\Helper\Config\Account as AccountHelper;
     */
    private $accountHelper;
    
    /**
     * @var \Narvar\Connect\Helper\Base as ExtensionHelper;
     */
    private $extensionHelper;
    
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    
    /**
     * @var \Zend\Uri\Http
     */
    private $httpUri;
    
    /**
     * Constructor
     *
     * @param ExtensionHelper $extensionHelper
     * @param AccountHelper $accountHelper
     * @param JsonHelper $jsonHelper
     * @param HttpUri $httpUri
     * @param array $data
     */
    public function __construct(
        ExtensionHelper $extensionHelper,
        AccountHelper $accountHelper,
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
        $this->extensionhelper = $extensionHelper;
        $this->jsonHelper = $jsonHelper;
        $this->validate($data);
        $this->initCurl();
        $this->setNarvarApiUrl($data);
        $this->setCurlTimeOut();
        $this->setBasicAuthAccount($data);
    }
    
    /**
     * Method to valid given constructor paramter is valid
     *
     * @param array $data
     * @throws ConnectorException
     */
    private function validate($data)
    {
        if (! empty($data)) {
            foreach ($data as $key => $value) {
                if (! in_array($key, $this->validParams)) {
                    throw new ConnectorException(__('Invalid Parameters Passed'));
                }
            }
        }
    }

    /**
     * Initialize the Curl Adapter
     */
    private function initCurl()
    {
        $this->curl = new Curl();
    }

    /**
     * Set the Narvar End Point API Url
     *
     * @param array $data
     * @return string
     */
    private function setNarvarApiUrl($data)
    {
        if (isset($data['url'])) {
            $this->narvarApiUrl = $data['url'];
        } else {
            $this->narvarApiUrl = $this->accountHelper->getNarvarApiEndpoint();
        }

        return $this->narvarApiUrl;
    }

    /**
     * Set the Time out Configuration for Curl
     */
    private function setCurlTimeOut()
    {
        $this->curl->setOptions(
            [
                'timeout' => $this->timeout
            ]
        );
    }

    /**
     * Set the Basic Authentication
     *
     * @param array $data
     */
    private function setBasicAuthAccount($data)
    {
        $username = $this->accountHelper->getNarvarAccountId();
        $password = $this->accountHelper->getNarvarAuthToken();
        
        if (isset($data['username']) && isset($data['password'])) {
            $username = $data['username'];
            $password = $data['password'];
        }

        $this->curl->setCurlOption(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Method Get the Headers for Curl Call
     *
     * @return multitype:string
     */
    private function getHeaders()
    {
        return self::$headers;
    }

    /**
     * Method the Headers for Curl Call Bulk Upload
     *
     * @return multitype:string
     */
    private function getFileUploadHeaders()
    {
        return self::$fileUploadHeaders;
    }

    /**
     * Method to connect the curl
     *
     * @param HttpUri $uri
     */
    private function curlConnect(HttpUri $uri)
    {
        $this->curl->connect($uri->getHost(), $uri->getPort());
    }

    /**
     * Method to form Uri for given url
     *
     * @param string $url
     * @return HttpUri
     */
    private function getHttpUri($url)
    {
        $this->httpUri = new HttpUri();
        $this->httpUri->parse($url);
        
        return $this->httpUri->resolve($url);
    }

    /**
     * Method to perform Post Request of Narvar API
     *
     * @param string $slug
     * @param json $postData
     * @return multitype <string, mixed>
     */
    public function post($slug, $postData)
    {
        $url = $this->narvarApiUrl . $slug;
        $uri = $this->getHttpUri($url);
        $this->curlConnect($uri);
        $this->curl->setCurlOption(CURLOPT_CUSTOMREQUEST, Request::METHOD_POST);
        $this->curl->write(Request::METHOD_POST, $uri, Request::VERSION_11, $this->getHeaders(), $postData);

        return $this->responseHandling();
    }

    /**
     * Method to perform PUT (Update) Request of Narvar API
     *
     * @param string $slug
     * @param json $postData
     * @return multitype <string, mixed>
     */
    public function put($slug, $postData)
    {
        $url = $this->narvarApiUrl . $slug;
        $uri = $this->getHttpUri($url);
        $this->curlConnect($uri);

        $this->curl->write(Request::METHOD_PUT, $uri, Request::VERSION_11, $this->getHeaders(), $postData);

        return $this->responseHandling();
    }

    /**
     * Method to perform Delete Request of Narvar API
     *
     * @param string $slug
     * @return multitype <string, mixed>
     */
    public function delete($slug)
    {
        $url = $this->narvarApiUrl . $slug;
        $uri = $this->getHttpUri($url);
        $this->curlConnect($uri);
        $this->curl->write(Request::METHOD_DELETE, $uri, Request::VERSION_11, $this->getHeaders(), '');

        return $this->responseHandling();
    }

    /**
     * Method to perform Get Request of Narvar API
     *
     * @param string $slug
     * @return multitype <string, mixed>
     */
    public function get($slug)
    {
        $url = $this->narvarApiUrl . $slug;
        $uri = $this->getHttpUri($url);
        $this->curlConnect($uri);
        $this->curl->write(Request::METHOD_GET, $uri, Request::VERSION_11, $this->getHeaders(), '');

        return $this->responseHandling();
    }

    /**
     * Method to perform Post Request of Narvar API
     *
     * @param string $slug
     * @param json $postData
     * @return multitype <string, mixed>
     */
    public function upload($slug, $postData)
    {
        $url = $this->narvarApiUrl . $slug;
        $uri = $this->getHttpUri($url);
        $this->curlConnect($uri);

        $this->curl->write(
            Request::METHOD_POST,
            $uri,
            Request::VERSION_11,
            $this->getFileUploadHeaders(),
            $postData
        );
                
        return $this->responseHandling();
    }

    /**
     * Handle the Response and throws exception if any failure met
     * Otherwise return success message
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    private function responseHandling()
    {
        try {
            $this->response = $this->curl->read();
            $this->curl->close();
            $responseMsg = Response::fromString($this->response)->getReasonPhrase();
            if (Response::fromString($this->response)->getStatusCode() >= 400) {
                throw new ConnectorException(__('%1', $responseMsg));
            }

            return $this->processResponse();
        } catch (ConnectorException $e) {
            throw new ConnectorException(__('Call to Narvar API Failed: %1', $e->getMessage()));
        }
    }

    /**
     * Process the Response
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    private function processResponse()
    {
        $responseJson = Response::fromString($this->response)->getBody();

        $response = $this->jsonHelper->jsonDecode($responseJson);

        if ($this->isError($response)) {
            throw new ConnectorException($this->parseResponseMessage($response));
        }

        return $this->parseResponseMessage($response);
    }

    /**
     * Method to check Narvar API Response is Error
     *
     * @return boolean
     */
    private function isError($response)
    {
        return ($response[self::STATUS] == self::STATUS_ERROR) || ($response[self::STATUS] == null) ? true : false;
    }

    /**
     * Method to retrive the message to display to user from whole messages
     *
     * @return string
     */
    private function parseResponseMessage($response)
    {
        $responseMsg = '';

        foreach ($response[self::MESSAGES] as $message) {
            if ($responseMsg != '') {
                $responseMsg .= sprintf('%s%s', PHP_EOL, $message[self::MSG_MESSAGE]);
            } else {
                $responseMsg = $message[self::MSG_MESSAGE];
            }
        }

        return $responseMsg;
    }
}
