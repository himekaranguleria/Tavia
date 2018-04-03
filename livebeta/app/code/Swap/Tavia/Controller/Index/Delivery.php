<?php

namespace Swap\Tavia\Controller\Index;

use \Swap\Tavia\Helper\Data;

class Delivery extends \Magento\Framework\App\Action\Action {

    public function __construct(\Swap\Tavia\Helper\Data $helper, \Magento\Framework\App\Action\Context $context) {
        $this->helper = $helper;
        return parent::__construct($context);
    }

    public function execute() {
//          if (@$_POST['name']) {
//            $name = $_POST['name'];
//            $street = $_POST['street1'] . " " . $_POST['street2'];
//            $city = $_POST['city'];
//            $state = $_POST['state'];
//            $postcode = $_POST['postcode'];
//            $telephone = $_POST['telephone'];
//            $email = $_POST['email'];
////            echo "post";
//            $tempOrder = [
//                'currency_id' => 'USD',
//                'email' => $email, //buyer email id
//                'shipping_address' => [
//                    'firstname' => $name, //address Details
//                    'lastname' => $name,
//                    'street' => $street,
//                    'city' => $city,
//                    'country_id' => 'IN',
//                    'region' => $state,
//                    'postcode' => $postcode,
//                    'telephone' => $telephone,
//                    'fax' => '',
//                    'save_in_address_book' => 1
//                ],
//                'items' => [//array of product which order you want to create
//
//                    ['product_id' => '50', 'qty' => 2, 'price' => 20],
//                    ['product_id' => '51', 'qty' => 1, 'price' => 20],
//                    ['product_id' => '52', 'qty' => 1, 'price' => 20],
//                    ['product_id' => '53', 'qty' => 1, 'price' => 20]
//                ]
//            ];
//            $this->helper->createOrder($tempOrder);
//        }
//        $tempOrder = [
//            'currency_id' => 'USD',
//            'email' => 'swap.guleria@gmail.com', //buyer email id
//            'shipping_address' => [
//                'firstname' => 'jhon', //address Details
//                'lastname' => 'Deo',
//                'street' => 'xxxxx',
//                'city' => 'xxxxx',
//                'country_id' => 'IN',
//                'region' => 'xxx',
//                'postcode' => '43244',
//                'telephone' => '52332',
//                'fax' => '32423',
//                'save_in_address_book' => 1
//            ],
//            'items' => [//array of product which order you want to create
//
////                ['product_id' => '50', 'qty' => 2, 'price' => 20],
//                ['product_id' => '51', 'qty' => 2, 'price' => 20],
//                ['product_id' => '51', 'qty' => 1, 'price' => 20],
////                ['product_id' => '53', 'qty' => 1, 'price' => 20]
//            ]
//        ];
//        $this->helper->createOrder($tempOrder);

        $this->_view->loadLayout();

        $this->_view->renderLayout();
    }

}

?>