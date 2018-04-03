<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Donations\Block\Adminhtml\Charity\Edit\Button;

class Back extends Generic
{
    /**
     * @param int $sortOrder
     *
     * @return array
     */
    public function getButtonData($sortOrder = 10)
    {
        $label = __('Back');
        $onClick = sprintf("location.href = '%s';", $this->getUrl('*/*/'));
        return [
            'label' => $label,
            'on_click' => $onClick,
            'class' => 'back',
            'sort_order' => $sortOrder
        ];
    }
}
