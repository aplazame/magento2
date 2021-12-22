<?php

namespace Aplazame\Payment\Model\Config\Source;

class Outoflimits implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'show', 'label' => __('Show')],
            ['value' => 'hide', 'label' => __('Hide')]
        ];
    }
}
