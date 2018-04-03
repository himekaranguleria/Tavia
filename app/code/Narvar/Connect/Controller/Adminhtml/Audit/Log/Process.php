<?php
/**
 * Admin Audit Log Process Controller
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Controller\Adminhtml\Audit\Log;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Narvar\Connect\Model\UploaderFactory;

class Process extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Narvar_Connect::audit_log_process';
    
    /**
     *
     * @var \Narvar\Connect\Model\UploaderFactory
     */
    private $uploader;
    
    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        UploaderFactory $uploader
    ) {
        parent::__construct($context);
        $this->uploader = $uploader;
    }
    
    /**
     * Process the failure logs
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $this->uploader->create()->process();
            $this->messageManager->addSuccess('Successfully processed the failure records');
        } catch (LocalizedException $e) {
            $this->messageManager->addError(__('Unable to process : %1', $e->getMessage()));
        }
        
        return $this->resultRedirectFactory->create()->setPath('narvar_connect/*/');
    }
}
