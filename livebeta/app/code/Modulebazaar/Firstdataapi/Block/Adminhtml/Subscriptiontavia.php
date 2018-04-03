<?php
namespace Modulebazaar\Firstdataapi\Block\Adminhtml;

class Subscriptiontavia extends \Magento\Backend\Block\Widget\Grid\Container
{

	protected function _construct()
	{
		$this->_controller = 'adminhtml_subscriptiontavia';
		$this->_blockGroup = 'modulebazaar_firstdataapi';
		$this->_headerText = __('Subscription');
		$this->removeButton('add');
		parent::_construct();
	}
}

