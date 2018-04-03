<?php
/**
 * Config Custom Attributes Boolean Source Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\System\Config\Source\Attributes;

class Boolean extends \Narvar\Connect\Model\System\Config\Source\Attributes
{

    /**
     * Method to return Customer, Order, Address and Product Attributes
     * Which have boolean as input as Select Options
     *
     * @param boolean $isMultiSelect
     * @param array $filter
     * @return multitype
     */
    public function toOptionArray($isMultiSelect, $filter = [])
    {
        return parent::toOptionArray(
            $isMultiSelect,
            $filter = [
                'boolean_flag' => true
            ]
        );
    }
}
