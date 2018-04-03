<?php
namespace MageWorx\Donations\Block;

class Charity extends \Magento\Framework\View\Element\Template
{
    protected $_charityFactory;

    /**
     * Charity constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\Donations\Model\CharityFactory         $charityFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorx\Donations\Model\CharityFactory $charityFactory
    ) {
        $this->_charityFactory = $charityFactory;
        parent::__construct($context);
    }
}