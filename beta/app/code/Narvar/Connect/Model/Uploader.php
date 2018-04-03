<?php
/**
 * Narvar File Uploader Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model;

use Narvar\Connect\Model\Audit\Log as AuditLog;
use Narvar\Connect\Exception\ConnectorException;
use Magento\Framework\Exception\LocalizedException;
use Narvar\Connect\Helper\ConnectorFactory;
use Narvar\Connect\Model\ResourceModel\Audit\Log\CollectionFactory as AuditLogCollectionFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Filesystem\Io\File as FileHandler;
use Magento\Framework\App\Filesystem\DirectoryList;
use Narvar\Connect\Helper\Audit\Status as AuditStatusHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Uploader extends \Magento\Framework\DataObject
{

    /**
     * Slug value for file upload api
     */
    const SLUG = '/orders/upload/';

    /**
     *
     * @var \Narvar\Connect\Model\ResourceModel\Audit\Log\CollectionFactory
     */
    private $auditLogsCollectionFactory;

    /**
     *
     * @var \Narvar\Connect\Model\ResourceModel\Audit\Log\Collection
     */
    private $logs;

    /**
     *
     * @var File Upload Temporary file Path
     */
    private $filePath;

    /**
     *
     * @var \Narvar\Connect\Helper\Audit\Status
     */
    private $auditStatusHelper;

    /**
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $fileHandler;

    /**
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     *
     * @var \Narvar\Connect\Helper\Connector
     */
    private $connector;
    
    /**
     * Where condition for update process
     */
    private $whereCondition = null;
    
    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime;
     */
    private $dateTime;

    /**
     * Constructor
     *
     * @param AuditStatusHelper $auditStatusHelper
     * @param AuditLogCollectionFactory $auditLogCollectionFactory
     * @param JsonHelper $jsonHelper
     * @param FileHandler $fileHandler
     * @param DirectoryList $directoryList
     * @param ConnectorFactory $connector
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        AuditStatusHelper $auditStatusHelper,
        AuditLogCollectionFactory $auditLogCollectionFactory,
        JsonHelper $jsonHelper,
        FileHandler $fileHandler,
        DirectoryList $directoryList,
        ConnectorFactory $connector,
        DateTime $dateTime,
        array $data = []
    ) {
        $this->auditLogsCollectionFactory = $auditLogCollectionFactory;
        $this->setLogs($data);
        $this->dateTime = $dateTime;
        $this->auditStatusHelper = $auditStatusHelper;
        $this->directoryList = $directoryList;
        $this->fileHandler = $fileHandler;
        $this->setFilePath();
        $this->jsonHelper = $jsonHelper;
        $this->connector = $connector;
    }

    /**
     * Method to set the File Folder Path
     */
    private function setFilePath()
    {
        $this->filePath = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'narvar' . DIRECTORY_SEPARATOR .
             'import' . DIRECTORY_SEPARATOR . $this->dateTime->date('Y') . DIRECTORY_SEPARATOR .
             $this->dateTime->date('m') . DIRECTORY_SEPARATOR . $this->dateTime->date('d') .DIRECTORY_SEPARATOR;
    }

    /**
     * Method to set the logs collection
     */
    private function setLogs($data)
    {
        $logs = $this->auditLogsCollectionFactory->create()->addFailureFilter();
        
        if (isset($data['from_time']) && isset($data['to_time'])) {
            $logs->addBulkDateFilter($data['from_time'], $data['to_time']);
        }
        
        $this->logs = $logs;
        $this->whereCondition = sprintf('%s IN (%s)', AuditLog::LOG_ID, implode(',', $this->logs->getAllIds()));
    }

    /**
     * Method to generate the Order information upload file
     *
     * @return multitype:string boolean
     */
    private function generateFile()
    {
        $fileName = 'narvar_order_data_' . $this->dateTime->date('H') . $this->dateTime->date('i') .
            $this->dateTime->date('s');
        $this->fileHandler->setAllowCreateFolders(true);
        $this->fileHandler->open([
            'path' => $this->filePath
        ]);
        
        $content = '';
        foreach ($this->logs as $log) {
            $data = '';
            $data = $log->getRequestData();
            $content .= "$data\n";
        }
        $this->fileHandler->write($fileName, $content, 'w+');
        $this->fileHandler->close();
        
        $this->logs->updateRecords(
            [
                AuditLog::STATUS => $this->auditStatusHelper->getOnHold(),
                AuditLog::FINISH_TIME => $this->dateTime->date()
            ],
            $this->whereCondition
        );
        
        return [
            'type' => 'filename',
            'value' => $fileName,
            'rm' => true
        ];
    }

    /**
     * Method to process the file generation and file upload to narvar
     */
    public function process()
    {
        if ($this->logs && count($this->logs->getData()) <= 0) {
            throw new LocalizedException(__('Failure Records Not Found'));
        }
        
        $file = $this->generateFile();
        
        if (is_array($file) && isset($file['value'])) {
            $fileName = $file['value'];
            $uploadFile = [
                "filedata" => "@$this->filePath",
                "filename" => $fileName
            ];
            
            try {
                $responseMsg = $this->connector->create()->upload(
                    self::SLUG,
                    $this->jsonHelper->jsonEncode($uploadFile)
                );
                
                $this->logs->updateRecords(
                    [
                        AuditLog::STATUS => $this->auditStatusHelper->getSuccess(),
                        AuditLog::RESPONSE => $responseMsg,
                        AuditLog::FINISH_TIME => $this->dateTime->date()
                    ],
                    $this->whereCondition
                );
                
                return true;
            } catch (ConnectorException $e) {
                $this->logs->updateRecords(
                    [
                        AuditLog::STATUS => $this->auditStatusHelper->getFailure(),
                        AuditLog::FINISH_TIME => $this->dateTime->date()
                    ],
                    $this->whereCondition
                );
                
                throw new LocalizedException(__('%1', $e->getMessage()));
            }
        }
        
        throw new LocalizedException(__('Unable to generate file'));
    }
}
