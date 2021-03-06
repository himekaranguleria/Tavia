<?php
/**
 * Handshake Helper
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Helper;

use Narvar\Connect\Helper\Base;

class Handshake extends Base
{
    /**
     * Slug value for Account Authentication link
     */
    const SLUG = 'tracking/magento-handshake';

    /**
     * Narvar Connect Handshake API Parameter Version
     */
    const VERSION = 'version';

    /**
     * Narvar Connect Handshake API Parameter Base Url
     */
    const BASE_URL = 'base_url';

    /**
     * Narvar Connect Handshake API Parameter Return Request Url
     */
    const RETURN_REQ_URL = 'return_request_url';

    /**
     * Narvar Connect Handshake API Parameter Auth Key
     */
    const AUTH_KEY = 'auth_key';

    /**
     * Narvar Connect Handshake API Parameter Auth Token
     */
    const AUTH_TOKEN = 'auth_token';

    /**
     * Slug value for Magento return request url Rest
     */
    const RETURN_SLUG_REST = 'rest';
    
    /**
     * Slug value for Magento return request url
     */
    const RETURN_SLUG = '/V1/narvar/order/return';
}
