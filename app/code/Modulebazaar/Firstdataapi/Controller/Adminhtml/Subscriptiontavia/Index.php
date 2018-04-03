<?php 

namespace Modulebazaar\Firstdataapi\Controller\Adminhtml\Subscriptiontavia;

class Index extends \Magento\Framework\App\Action\Action { 

	protected $resultPageFactory = false;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Subscription')));

		return $resultPage;
	}
  
} 