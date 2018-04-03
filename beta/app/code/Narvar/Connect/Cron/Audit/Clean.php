<?php
/**
 * Audit Clean Cron
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Cron\Audit;

use Narvar\Connect\Model\Batch\Audit\Clean as AuditCleanModel;

class Clean
{
    
    /**
     *
     * @var \Narvar\Connect\Model\Batch\Audit\Clean
     */
    private $auditClean;

    /**
     * Constructor
     *
     * @param AuditCleanModel $auditClean
     */
    public function __construct(
        AuditCleanModel $auditClean
    ) {
        $this->auditClean = $auditClean;
    }

    /**
     * Method to clean the Audit Log Entries based on configured days interval
     */
    public function execute()
    {
        $this->auditClean->process();
        
        return $this;
    }
}
