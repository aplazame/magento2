<?php

namespace Aplazame\Payment\Model\Config\Source;

class Align implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'left', 'label' => __('Left')],
            ['value' => 'center', 'label' => __('Center')],
            ['value' => 'right', 'label' => __('Right')]
        ];
    }
}
