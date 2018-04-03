<?php
namespace Modulebazaar\Firstdataapi\Block\Adminhtml\Subscription;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\PriceCurrencyInterface;


class Amount extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;
	
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        PriceCurrencyInterface $priceFormatter
    ) {
        $this->priceFormatter = $priceFormatter;
    }
 
    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
		$price = $this->priceFormatter->format(
					$row->getOrderAmount(),
                    false,
                    null,
                    null,
                    'USD'
                );
				
        return $price;
    }
}