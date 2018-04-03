<?php
/**
 * Batch Audit Clean Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Batch\Audit;

use Narvar\Connect\Model\Audit\Log as AuditLog;
use Narvar\Connect\Helper\Config\Batch as BatchHelper;
use Narvar\Connect\Helper\Cron\Log as CronLogHelper;
use Narvar\Connect\Helper\Config\Activation;
use Narvar\Connect\Model\ResourceModel\Audit\Log\CollectionFactory as AuditLogCollectionFactory;
use Magento\Framework\Stdlib\DateTime as StdDateTime;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Clean
{

    /**
     *
     * @var \Narvar\Connect\Helper\Config\Batch
     */
    private $configBatchHelper;

    /**
     *
     * @var \Narvar\Connect\Helper\Cron\Log
     */
    private $cronLogHelper;
    
    /**
     *
     * @var \Narvar\Connect\Helper\Config\Activation
     */
    private $activationHelper;

    /**
     *
     * @var \Narvar\Connect\Model\ResourceModel\Audit\Log\CollectionFactory
     */
    private $auditLogCollectionFactory;
    
    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * Constructor
     *
     * @param BatchHelper $batchConfigHelper
     * @param CronLogHelper $cronLogHelper
     * @param Activation $activationHelper
     * @param AuditLogCollectionFactory $auditLogCollectionFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        BatchHelper $batchConfigHelper,
        CronLogHelper $cronLogHelper,
        Activation $activationHelper,
        AuditLogCollectionFactory $auditLogCollectionFactory,
        DateTime $dateTime
    ) {
        $this->configBatchHelper = $batchConfigHelper;
        $this->cronLogHelper = $cronLogHelper;
        $this->activationHelper = $activationHelper;
        $this->auditLogCollectionFactory = $auditLogCollectionFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * Method to clean the Audit Log Entries based on configured days interval
     */
    public function process()
    {
        if (! $this->activationHelper->getIsActivated()) {
            return;
        }

        if ($this->canProcess()) {
            $auditLogs = $this->auditLogCollectionFactory->create()->addAuditCleanFilter(
                $this->configBatchHelper->getBatchAuditCleanInterval()
            );
            $where = sprintf('%s IN (%s)', AuditLog::LOG_ID, implode(',', $auditLogs->getAllIds()));
            $auditLogs->deleteRecords($where);

            $this->cronLogHelper->updateAuditClean();
        }
    }

    /**
     * Method to check can process the clean up
     *
     * @return boolean
     */
    private function canProcess()
    {
        $todayDate = new \DateTime($this->dateTime->date(StdDateTime::DATE_PHP_FORMAT));
        $lastExecutedDate = new \DateTime(
            $this->dateTime->date(
                StdDateTime::DATE_PHP_FORMAT,
                $this->cronLogHelper->getAuditClean()
            )
        );
        $todayDateTime = new \DateTime($this->dateTime->date());

        if ($this->configBatchHelper->getBatchFirstPushTime() != $todayDateTime->format('H')) {
            return false;
        }

        $diffDate = $todayDate->diff($lastExecutedDate);

        return ($diffDate->d >= $this->configBatchHelper->getBatchAuditCleanInterval());
    }
}
