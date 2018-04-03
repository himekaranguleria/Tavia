<?php
/**
 * Audit Entity Type Helper
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Helper\Audit;

use Narvar\Connect\Helper\Base;

class Type extends Base
{
    
     /**
     * Entity type order for audit log
     */
    const ENT_TYPE_ORDER = 'order';

    /**
     * Entity type shipment for audit log
     */
    const ENT_TYPE_SHIPMENT = 'shipment';
}
