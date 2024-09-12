<?php

namespace Aplazame\Payment\Model\Config\Source;

class WidgetVersion implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'v3', 'label' => __('v3')],
            ['value' => 'v4', 'label' => __('v4')],
            ['value' => 'v5', 'label' => __('v5')]
        ];
    }
}
