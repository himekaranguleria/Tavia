<?php
/**
 * Batch Bulk Process Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Batch\Audit;

use Narvar\Connect\Helper\Config\Batch as BatchHelper;
use Narvar\Connect\Helper\Cron\Log as CronLogHelper;
use Narvar\Connect\Helper\Config\Activation;
use Narvar\Connect\Model\UploaderFactory;
use Magento\Framework\Stdlib\DateTime as StdDateTime;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Bulk
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
     * @var \Narvar\Connect\Model\UploaderFactory
     */
    private $uploader;
    
    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
    
    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * Constructor
     *
     * @param BatchHelper $batchConfigHelper
     * @param CronLogHelper $cronLogHelper
     * @param Activation $activationHelper
     * @param Uploader $uploader
     * @param DateTime $dateTime
     */
    public function __construct(
        BatchHelper $batchConfigHelper,
        CronLogHelper $cronLogHelper,
        Activation $activationHelper,
        UploaderFactory $uploader,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->configBatchHelper = $batchConfigHelper;
        $this->cronLogHelper = $cronLogHelper;
        $this->activationHelper = $activationHelper;
        $this->uploader = $uploader;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Method to process the failure records based on configuration
     */
    public function process()
    {
        if (! $this->activationHelper->getIsActivated()) {
            return;
        }

        if ($this->canProcess()) {
            $data = [];
            $data['from_time'] = $this->cronLogHelper->getBulkPush();
            $data['to_time'] = $this->dateTime->date();
            $this->cronLogHelper->updateBulkPush();
            
            try {
                $this->uploader->create(['data' => $data])->process();
            } catch (LocalizedException $e) {
                $this->logger->error(__('Narvar Bulk Process - Unable to process : %1', $e->getMessage()));
            }
        }
    }

    /**
     * Method to check can process the bulk upload
     *
     * @return boolean
     */
    private function canProcess()
    {
        $lastExeBulkPush = $this->cronLogHelper->getBulkPush();
        $todayDate = new \DateTime($this->dateTime->date(StdDateTime::DATE_PHP_FORMAT));
        $lastExecutedDate = new \DateTime(
            $this->dateTime->date(
                StdDateTime::DATE_PHP_FORMAT,
                $lastExeBulkPush
            )
        );
        $diffDate = $todayDate->diff($lastExecutedDate);

        $todayDateTime = new \DateTime($this->dateTime->date());
        $lastExecutedDateTime = new \DateTime($lastExeBulkPush);
        $diffDateTime = $todayDateTime->diff($lastExecutedDateTime);

        if ($diffDate->d > 0 && $todayDateTime->format('H') == $this->configBatchHelper->getBatchFirstPushTime()) {
            return true;
        }

        if ($diffDate->d == 0 && $this->configBatchHelper->getBatchBulkPushFreq() == $diffDateTime->h) {
            return true;
        }

        return false;
    }
}
