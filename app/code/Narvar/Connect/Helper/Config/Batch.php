<?php
/**
 * Configuration Batch Helper
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Helper\Config;

use Narvar\Connect\Helper\Base;

/**
 * Below methods will used to get configuration value
 *
 * @method string getBatchBulkPushFreq()
 * @method string getBatchFirstPushTime()
 * @method string getBatchAuditCleanInterval()
 */
class Batch extends Base
{
    /**
     * Narvar Account Connect Config Group
     */
    const CONFIG_GRP = 'batch';

    /**
     * Batch Process Bulk Upload Frequency config path
     */
    const BATCH_BULK_PUSH_FREQ = 'bulk_push_frequency';

    /**
     * Batch Process Bulk Upload Start Time config path
     */
    const BATCH_FIRST_PUSH_TIME = 'first_push_start_time';

    /**
     * Batch Process Audit Clean Interval config path
     */
    const BATCH_AUDIT_CLEAN_INTERVAL = 'audit_cleanup_interval';
}
