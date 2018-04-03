<?php
namespace Modulebazaar\Firstdataapi\Block;

class Info extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var bool
     */
    protected $isEcheck = false;
    
    protected $helper;
   
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Payment\Model\Config $paymentConfig, array $data = [])
    {
        $this->scopeConfig = $context->getScopeConfig();

        parent::__construct($context, $paymentConfig, $data);
    }
    /**
     * Prepare credit card related payment info
     *
     * @param  \Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = \Magento\Payment\Block\Info::_prepareSpecificInformation($transport);
        $data = [];
        $info = $this->getInfo();
        $ccType = $this->getCcTypeName();
        if (!empty($ccType) && $ccType != 'N/A') {
            $data[(string)__('Credit Card Type') ] = $ccType;
        }
        if ($info->getCcLast4()) {
            $data[(string)__('Credit Card Number') ] = sprintf('XXXX-%s', $info->getCcLast4());
        }
        if ($info->getCcExpMonth()) {
            $data[(string)__('Expiry Month') ] = $info->getCcExpMonth() . ' / ' . $info->getCcExpYear();
        }
         $types = $this->scopeConfig->getValue('payment/firstdataapi/transaction_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($types == '00') {
            $type = "Authorize and Capture";
        } elseif ($types == '01') {
            $type = "Authorize Only";
        }
        if ($info->getCcOwner()) {
            $data[(string)__('Name') ] = $info->getCcOwner();
        }
        $data[(string)__('Type') ] = $type;
        $transport->setData(array_merge($data, $transport->getData()));
        return $transport;
    }
}
