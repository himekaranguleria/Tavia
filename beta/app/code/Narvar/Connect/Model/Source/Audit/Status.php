<?php
/**
 * Audit Status Source Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Source\Audit;

use Narvar\Connect\Model\ResourceModel\Audit\Status\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{

    /**
     *
     * @var Narvar\Connect\Model\ResourceModel\Audit\Status\CollectionFactory
     */
    private $auditStatusCollection;

    /**
     * Constructor
     *
     * @param CollectionFactory $auditStatusCollection
     */
    public function __construct(CollectionFactory $auditStatusCollection)
    {
        $this->auditStatusCollection = $auditStatusCollection;
    }

    /**
     * Method to get the Audit Log Status as Options
     *
     * @return multitype:multitype:NULL
     */
    public function toOptionArray()
    {
        $statuses = $this->auditStatusCollection->create();

        $options = [];
        foreach ($statuses as $status) {
            $options[] = [
                'label' => ucwords($status->getStatusLabel()),
                'value' => $status->getId()
            ];
        }

        return $options;
    }
}
