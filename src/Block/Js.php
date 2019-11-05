<?php

namespace Aplazame\Payment\Block;

use Aplazame\Payment\Gateway\Config\Config;
use Aplazame\Payment\Gateway\Config\ConfigPayLater;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Js extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigPayLater
     */
    private $configPayLater;

    public function __construct(
        Context $context,
        Config $config,
        ConfigPayLater $configPayLater,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
        $this->configPayLater = $configPayLater;
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
        if (!$this->config->isActive()) {
            if(!$this->configPayLater->isActive()){
                return '';
            }
        }

        return parent::_toHtml();
    }
}
