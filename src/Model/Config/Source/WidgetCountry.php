<?php

namespace Aplazame\Payment\Model\Config\Source;

class WidgetCountry implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'auto', 'label' => __('Auto')],
            ['value' => 'es', 'label' => __('ES (Spain)')],
            ['value' => 'pt', 'label' => __('PT (Portugal)')]
        ];
    }
}
