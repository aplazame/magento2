<?php

namespace Aplazame\Payment\Block;

use Aplazame\Payment\Gateway\Config\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Js extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        Config $config,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    protected function _toHtml()
    {
        if (!$this->config->isActive() || $this->isHiddenInCurrentLanguage()) {
            return '';
        }

        return parent::_toHtml();
    }

    public function isHiddenInCurrentLanguage()
    {
        $currentLocale = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE);
        $hiddenLanguages = $this->scopeConfig->getValue('payment/aplazame_payment/aplazame_widget/hide_in_languages', ScopeInterface::SCOPE_STORE);

        if (in_array($currentLocale, explode(',', $hiddenLanguages))) {
            return true;
        }

        return false;
    }
}
