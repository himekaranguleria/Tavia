<?php
/**
 * Cron Log Helper
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Helper\Cron;

use Magento\Framework\App\Helper\Context;
use Narvar\Connect\Model\Cron\Log as CronLog;
use Narvar\Connect\Model\Cron\LogFactory as CronLogFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Log extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * Constant value for Job Code Bulk Push
     */
    const BULK_PUSH = 'bulk_push';

    /**
     * Constant value for Job Code Audit Clean
     */
    const AUDIT_CLEAN = 'audit_clean';
    
    /**
     * @var \Narvar\Connect\Model\Cron\LogFactory
     */
    private $cronLogFactory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CronLogFactory $cronLogFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        CronLogFactory $cronLogFactory,
        DateTime $dateTime
    ) {
        $this->cronLogFactory = $cronLogFactory;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }
    
    /**
     * Method to update the last execution time for job code audit clean
     *
     * @return \Narvar\Connect\Helper\Cron\Log
     */
    public function updateAuditClean()
    {
        $this->update(self::AUDIT_CLEAN);
        
        return $this;
    }
    
    /**
     * Method to update the last execution time for job code Bulkpush
     *
     * @return \Narvar\Connect\Helper\Cron\Log
     */
    public function updateBulkPush()
    {
        $this->update(self::BULK_PUSH);
        
        return $this;
    }

    /**
     * Method to get the last execution time of job code Bulkpush
     *
     * @return DateTime
     */
    public function getBulkPush()
    {
        return $this->lastExecutedTime(self::BULK_PUSH);
    }

    /**
     * Method to get the last execution time of job code audit clean
     *
     * @return DateTime
     */
    public function getAuditClean()
    {
        return $this->lastExecutedTime(self::AUDIT_CLEAN);
    }

    /**
     * Method to update the last executed time
     *
     * @param string $jobCode
     */
    public function update($jobCode)
    {
        $this->cronLogFactory->create()
            ->load($jobCode, CronLog::JOB_CODE)
            ->setLastExecutedAt($this->dateTime->date())
            ->save();
    }

    /**
     * Method to get the last executed time
     *
     * @param string $jobCode
     * @return DateTime
     */
    public function lastExecutedTime($jobCode)
    {
        return $this->cronLogFactory->create()
            ->load($jobCode, CronLog::JOB_CODE)
            ->getLastExecutedAt();
    }
}
