<?php

namespace Aplazame\Payment\Model\Config\Backend;

class Links extends \Magento\Framework\App\Config\Value
{
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->cleanType(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }
}
