<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Modulebazaar\Firstdataapi\Model;


class Subscriptiontavia extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'modulebazaar_firstdataapi_subscriptiontavia';

	protected $_cacheTag = 'modulebazaar_firstdataapi_subscriptiontavia';

	protected $_eventPrefix = 'modulebazaar_firstdataapi_subscriptiontavia';

	protected function _construct()
	{
		$this->_init('Modulebazaar\Firstdataapi\Model\ResourceModel\Subscriptiontavia');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}