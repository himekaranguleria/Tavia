<?php

namespace Modulebazaar\Firstdataapi\Model\Source;
use Magento\Framework\DataObject;

class Customername implements \Magento\Framework\Option\ArrayInterface
{
	protected $customerFactory;
	
    protected $options;
	
	public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->customerFactory = $customerFactory;
    }
	
    public function toOptionArray()
    {
		$email = array();		
        $customers = $this->customerFactory->create()->getCollection();	
		
        if ($this->options === null) {

            foreach ($customers as $customer) {
                $email = $customer->getEmail();
                $this->options[] = [
                    'value' => $customer->getId(),
                    'label' => $email
                ];
            }
        }
        return $this->options;
    }
}
