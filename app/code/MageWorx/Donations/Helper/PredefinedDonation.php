<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Helper;

use Magento\Framework\App\Helper\Context;

class PredefinedDonation extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * PredefinedDonation constructor
     *
     * @param Context                        $context
     * @param \Magento\Framework\Math\Random $mathRandom
     *
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Math\Random $mathRandom
    ) {
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     *
     * @return bool
     */
    public function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row) || !array_key_exists('predefined_values_donation', $row)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode FieldValue
     *
     * @param array $value
     *
     * @return array
     */
    public function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $qty) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['predefined_values_donation' => $this->fixQty($qty)];
        }
        return $result;
    }

    /**
     * Decode Field Value
     *
     * @param array $value
     *
     * @return array
     */
    public function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row) || !array_key_exists('predefined_values_donation', $row)) {
                continue;
            }
            $qty = $this->fixQty($row['predefined_values_donation']);
            $result[] = $qty;
        }
        return $result;
    }

    /**
     * Retrieve fixed qty value
     *
     * @param int|float|string|null $qty
     *
     * @return float|null
     */
    public function fixQty($qty)
    {
        return !empty($qty) ? (float)$qty : null;
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     *
     * @return array
     */
    public function unserializeValue($value)
    {
        if (is_numeric($value)) {
            return [$this->fixQty($value)];
        }
        if (is_string($value) && !empty($value)) {
            return unserialize($value);
        }
        return [];
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     *
     * @return string
     */
    public function serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float)$value;
            return (string)$data;
        }
        if (is_array($value) && !empty($value)) {
            $data = [];
            foreach ($value as $qty) {
                $data[] = $this->fixQty($qty);
            }
            return serialize($data);
        }
        return '';
    }

    /**
     * Filter correct data (Delete row with empty field)
     *
     * @param array $value
     *
     * @return array
     */
    public function filterCorrectData(array $value)
    {
        if (!is_array($value)) {
            return [];
        }

        foreach ($value as $key => $row) {
            if (is_array($row) && array_key_exists('predefined_values_donation', $row) && empty($row['predefined_values_donation'])) {
                unset($value[$key]);
            }
        }
        return $value;
    }
}