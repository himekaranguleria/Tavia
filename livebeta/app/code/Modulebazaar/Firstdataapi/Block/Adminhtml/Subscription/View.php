<?php
namespace Modulebazaar\Firstdataapi\Block\Adminhtml\Subscription;
use Magento\Framework\DataObject;


class View extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $urlBuider;
	
    /**
     * @param \Magento\Framework\UrlInterface $urlBuider
     */
    public function __construct(
		\Magento\Framework\UrlInterface $urlBuilder
    ) {
		$this->urlBuilder = $urlBuilder;
    }
 
    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
		return '<a href="'.$this->urlBuilder->getUrl('sales/order/view', ['order_id' => $row->getOrderId()]).'" >'.__('View').'</a>';
		//return df_url_backend('sales/order/view', ['order_id' => $row->getOrderId()]);
		//return $this->getUrl('sales/order/view', ['order_id' => $row->getOrderId()]);
    }
}