<?php

namespace Aplazame\Payment\Model\Config\Source;

class Layout implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'horizontal', 'label' => __('Horizontal')],
            ['value' => 'vertical', 'label' => __('Vertical')]
        ];
    }
}