<?php
namespace Modulebazaar\Firstdataapi\Controller\License;

use Modulebazaar\Firstdataapi\Helper\License;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $license;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Modulebazaar\Firstdataapi\Helper\License $license)
    {
        $this->license  = $license;
        parent::__construct($context);
    }
    public function execute()
    {

        $result_page = $this->license->checklicense();
        return $result_page;
    }
}
