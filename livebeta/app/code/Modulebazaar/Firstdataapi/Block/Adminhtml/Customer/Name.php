<?php
 
namespace Modulebazaar\Firstdataapi\Block\Adminhtml\Customer;
 
use Magento\Framework\DataObject;
 
class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
    }
 
    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $mageCateId = $row->getCustomerId();
        $storeCat = $this->categoryFactory->create()->load($mageCateId);
        return $storeCat->getEmail();
    }
}