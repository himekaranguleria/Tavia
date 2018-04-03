<?php
/**
 * Data Transformer Interface
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Data\Transformer;

use Narvar\Connect\Model\Data\DTO;

interface TransformerInterface
{

    /**
     * Method to transform Order and Shipment Data into
     * required Narvar API Format
     *
     * @param DTO $dto
     */
    public function transform(DTO $dto);
}
