<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Cart;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Framework\Escaper;
use Psr\Log\LoggerInterface;
use MageWorx\Donations\Helper\Donation as HelperDonation;
use MageWorx\Donations\Helper\Image as HelperImage;
use MageWorx\Donations\Helper\Data as HelperData;
use MageWorx\Donations\Helper\Price as HelperPrice;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use MageWorx\Donations\Model\CharityRepository;
use MageWorx\Donations\Model\ResourceModel\Charity\Collection;
use MageWorx\Donations\Helper\PredefinedDonation as HelperPredefined;


class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var HelperDonation
     */
    protected $helperDonation;

    /**
     * @var HelperImage
     */
    protected $helperImage;

    /**
     * @var HelperData
     */
    protected $helperData;
    /**
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * @var HelperPredefined
     */
    protected $helperPredefinedDonation;

    /**
     * @var CharityRepository
     */
    protected $charityRepository;

    /**
     * LayoutProcessor constructor.
     *
     * @param AttributeMerger   $merger
     * @param Escaper           $escaper
     * @param LoggerInterface   $logger
     * @param HelperDonation    $helperDonation
     * @param HelperImage       $helperImage
     * @param HelperData        $helperData
     * @param HelperPrice       $helperPrice
     * @param CharityRepository $charityRepository
     * @param HelperPredefined  $helperPredefinedDonation
     */
    public function __construct(
        AttributeMerger $merger,
        Escaper $escaper,
        LoggerInterface $logger,
        HelperDonation $helperDonation,
        HelperImage $helperImage,
        HelperData $helperData,
        HelperPrice $helperPrice,
        CharityRepository $charityRepository,
        HelperPredefined $helperPredefinedDonation
    ) {
        $this->merger = $merger;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->helperDonation = $helperDonation;
        $this->helperImage = $helperImage;
        $this->helperData = $helperData;
        $this->helperPrice = $helperPrice;
        $this->helperPredefinedDonation = $helperPredefinedDonation;

        $this->charityRepository = $charityRepository;

    }

    /**
     * Add components on frontend
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        if (isset($jsLayout['components']['mageworx-donation-form-container']['children']
            ['mageworx-donation-form-fieldset']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['mageworx-donation-form-container']['children']
            ['mageworx-donation-form-fieldset']['children'];

            foreach ($this->getFormComponents() as $component) {
                $fieldSetPointer[] = $component;
            }
        }

        if (isset($jsLayout['components']['checkout']['children']['sidebar']['children']
            ['summary']['children']['itemsBefore']['children']
            ['mageworx-donation-form-container']['children']['mageworx-donation-form-fieldset']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['checkout']['children']['sidebar']['children']
            ['summary']['children']['itemsBefore']['children']['mageworx-donation-form-container']
            ['children']['mageworx-donation-form-fieldset']['children'];

            foreach ($this->getFormComponents() as $component) {
                $fieldSetPointer[] = $component;
            }
        }

        return $jsLayout;
    }

    /**
     * Get form components
     *
     * @return array
     */
    public function getFormComponents()
    {
        /** @var \MageWorx\Donations\Model\ResourceModel\Charity\Collection $charityCollection */
        $charityCollection = $this->charityRepository->getListCharity();

        if ($charityCollection->count() > 0) {
            $components[] = $this->getCharitySelectComponent($charityCollection);
        }

        if ($this->helperData->isShowPredefinedValues()) {
            $components[] = $this->getPredefinedDonationSelectComponent();
        }

        $components[] = $this->getInputComponent();

        return $components;
    }

    /**
     * Get charity Component
     *
     * @return array
     */
    protected function getCharitySelectComponent(Collection $charityCollection)
    {
        $component = [];
        $component['component'] = 'MageWorx_Donations/js/form/element/select';
        $component['config'] = [
            'customScope' => 'mageworxDonationForm',
            'template' => 'MageWorx_Donations/form/field',
            'elementTmpl' => 'ui/form/element/select'
        ];
        $component['dataScope'] = 'charity';
        $component['provider'] = 'checkoutProvider';
        $component['visible'] = true;
        $component['validation'] = [];
        $component['sortOrder'] = 0;

        $options = [];

        foreach ($charityCollection as $charity) {
            $options[] =
                [
                    'label' => $charity->getName(),
                    'value' => $charity->getId(),
                    'notice' => $charity->getDescription(),
                    'path' => $this->helperImage->getImageUrl($charity->getImage(), 'IMAGE_TYPE_THUMBNAIL')
                ];
        }

        $component['options'] = $options;

        return $component;
    }

    /**
     * Get predefine donation Component
     *
     * @return array
     */
    protected function getPredefinedDonationSelectComponent()
    {
        $predefinedValues = $this->helperData->getPredefinedValuesDonation();
        if (!empty($predefinedValues)) {
            $component = [];
            $component['component'] = 'MageWorx_Donations/js/form/element/select';
            $component['config'] = [
                'customScope' => 'mageworxDonationForm',
                'template' => 'MageWorx_Donations/form/field',
                'elementTmpl' => 'ui/form/element/select'
            ];
            $component['dataScope'] = 'predefinedDonation';
            $component['provider'] = 'checkoutProvider';
            $component['visible'] = true;
            $component['validation'] = [];
            $component['sortOrder'] = 5;


            $options = [];

            foreach ($predefinedValues as $value) {
                $options[] =
                    [
                        'label' => $price = $this->helperPrice->getFormatPrice($value, 2),
                        'value' => $value,
                    ];
            }
            /* add custom value */
            $options[] =
                [
                    'label' => __('Custom Amount'),
                    'value' => 'custom_donation'
                ];

            $component['options'] = $options;

            return $component;
        }
    }

    /**
     * Get Input Component
     *
     * @return array
     */
    protected function getInputComponent()
    {
        $donationData = $this->helperDonation->getDonation();

        $component = [];
        $component['component'] = 'Magento_Ui/js/form/element/abstract';
        $component['config'] = [
            'customScope' => 'mageworxDonationForm',
            'template' => 'MageWorx_Donations/form/input',
            'elementTmpl' => 'ui/form/element/input',
        ];

        $component['dataScope'] = 'mageworxDonationForm';
        $component['provider'] = 'checkoutProvider';
        $component['visible'] = true;

        if ($this->helperData->isShowPredefinedValues()) {
            $component['visible'] = false;
        }

        $component['validation'] = [];
        $component['sortOrder'] = 10;
        $component['placeholder'] = $this->helperData->getAmountPlaceholder();

        if (!empty($donationData['donation'])) {
            $component['value'] = $this->escaper->escapeHtml($donationData['donation']);
        }

        return $component;
    }
}
