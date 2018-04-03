<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Donations\Block\Adminhtml\Order\Create\Form;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\Donations\Block\Adminhtml\Order\Create\Form\AbstractForm;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use MageWorx\Donations\Model\CharityRepository;
use MageWorx\Donations\Model\ResourceModel\Charity\Collection as CharityCollection;

/**
 * Create order donation form
 *
 */
class Donation extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \MageWorx\Donations\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\Donations\Helper\Donation
     */
    protected $helperDonation;

    /**
     * @var \MageWorx\Donations\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \MageWorx\Donations\Helper\Image
     */
    protected $helperImage;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * Data Form object
     *
     * @var \Magento\Framework\Data\Form
     */
    protected $form;

    /**
     * Form factory
     *
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var CharityRepository
     */
    protected $charityRepository;

    /**
     * @var CharityCollection
     */
    protected $charityCollection;


    /**
     * Donation constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote    $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create  $orderCreate
     * @param PriceCurrencyInterface                  $priceCurrency
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \MageWorx\Donations\Helper\Data         $helperData
     * @param \MageWorx\Donations\Helper\Donation     $helperDonation
     * @param \MageWorx\Donations\Helper\Price        $helperPrice
     * @param \MageWorx\Donations\Helper\Image        $helperImage
     * @param CharityRepository                       $charityRepository
     * @param LocaleFormat                            $localeFormat
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Data\FormFactory $formFactory,
        \MageWorx\Donations\Helper\Data $helperData,
        \MageWorx\Donations\Helper\Donation $helperDonation,
        \MageWorx\Donations\Helper\Price $helperPrice,
        \MageWorx\Donations\Helper\Image $helperImage,
        CharityRepository $charityRepository,
        LocaleFormat $localeFormat,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->helperDonation = $helperDonation;
        $this->helperPrice = $helperPrice;
        $this->helperImage = $helperImage;
        $this->charityRepository = $charityRepository;
        $this->localeFormat = $localeFormat;
        $this->formFactory = $formFactory;
        $this->charityCollection = $this->charityRepository->getListCharity();

        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-donation';
    }

    /**
     * Return header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Donation');
    }

    /**
     * Check added donation in session
     *
     * @return bool
     */
    public function isAddedDonation()
    {
        $donationData = $this->helperDonation->getDonation();

        if (!empty($donationData)) {
            return true;
        }
        return false;
    }

    /**
     * Get value donation (format price)
     *
     * @return float
     */
    public function getValueDonation()
    {
        $donationDetails = $this->helperDonation->getCurrentSession()->getMageworxDonationDetails();
        $formatPrice = $this->helperPrice->getFormatPrice($donationDetails['donation'], 2);

        return $formatPrice;
    }

    /**
     * Return turn on or turn off setting (Display Donations in Admin)
     *
     * @return bool
     */
    public function getIsEnableDonation()
    {
        if ($this->helperData->isShowDonationAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * Get price format
     *
     * @return array
     */
    public function getBasePriceFormat()
    {
        return $this->localeFormat->getPriceFormat(null, $this->helperDonation->getQuote()->getQuoteCurrencyCode());
    }


    /**
     * Return Form object
     *
     * @return \Magento\Framework\Data\Form
     */
    public function getForm()
    {
        if ($this->form === null) {
            $this->form = $this->formFactory->create();
            $this->prepareForm();
        }
        return $this->form;
    }

    /**
     * @return string
     */
    public function getSelectFormHtml()
    {
        $rawFormHtml = str_replace('admin__control-radio', 'admin__control-radio charity_field', $this->getForm()->getHtml());
        return str_replace('admin__control-checkbox', 'admin__control-checkbox charity_field', $rawFormHtml);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function prepareForm()
    {
        if ($this->charityCollection->count() > 0) {
            $attribute = [];

            $attribute['attribute_code'] = 'donation_charity';
            $attribute['name'] = 'charity_id';
            $attribute['frontend_input'] = 'select';
            $attribute['store_label'] = 'charity';
            $attribute['visible'] = true;
            $attribute['frontend_class'] = 'mageworx_donations_charity';

            $selectedOption = null;
            $options = [];

            foreach ($this->charityCollection as $charity) {
                $options[] = [
                    'value' => $charity->getId(),
                    'label' => $charity->getName()
                ];
            }

            $attribute['options'] = $options;

            $element = $this->form->addField(
                $attribute['attribute_code'],
                'select',
                []
            );

            $element->setEntityAttribute($attribute);
            $options = isset($attribute['options']) ? $attribute['options'] : [];
            $element->setValues($options);
        }


        return $this;
    }

    /**
     * Get Charity Data from collection charity
     *
     * @return array
     */
    public function getCharityData()
    {
        $charityData = [];

        foreach ($this->charityCollection as $charity) {
            $data = [];
            $data['description'] = $charity->getDescription();
            $data['path'] = $this->helperImage->getImageUrl($charity->getFilename(), 'IMAGE_TYPE_THUMBNAIL');

            $charityData[$charity->getId()] = $data;
        }
        return $charityData;
    }
}
