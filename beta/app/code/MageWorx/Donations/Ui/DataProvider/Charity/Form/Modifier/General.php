<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Ui\DataProvider\Charity\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\Donations\Controller\Adminhtml\Charity;
use MageWorx\Donations\Model\CharityFactory;

/**
 * Class General
 */
class General extends AbstractModifier
{
    const FIELD_CHARITY_NAME      = 'charity';
    const FIELD_NAME_NAME         = 'name';
    const FIELD_TITLE_DESCRIPTION = 'description';
    const FIELD_TITLE_NAME        = 'name';
    const FIELD_SORT_ORDER        = 'sort_order';
    const FIELD_IS_ACTIVE_NAME    = 'is_active';
    const FIELD_IMAGE             = 'filename';
    const KEY_SUBMIT_URL          = 'submit_url';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param ArrayManager          $arrayManager
     * @param UrlInterface          $urlBuilder
     * @param CharityFactory        $charityFactory
     * @param Registry              $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface      $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        CharityFactory $charityFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        RequestInterface $request
    ) {
        parent::__construct($arrayManager, $urlBuilder, $coreRegistry, $storeManager, $charityFactory);
        $this->request = $request;
    }


    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {

        // Add submit (save) url to the config
        $actionParameters = [];
        $submitUrl = $this->urlBuilder->getUrl('mageworx_donations/charity/save', $actionParameters);
        $data = array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ]
            ]
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        return $this->meta;
    }
}

