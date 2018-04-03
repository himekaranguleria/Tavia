<?php

/**
 * Coustom Test Block Catalog Product View Type Bundle
 *
 * @category    Coustom
 * @package     Coustom_Test
 * @author      Swap
 *
 */

namespace Coustom\Testing\Block;

class Option extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle {

    public function getSelectionTitlePrice($selection, $includeContainer = true) {
        return "sad";
// Do your stuff here
    }

}
