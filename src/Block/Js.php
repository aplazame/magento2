<?php

namespace Aplazame\Payment\Block;

use Aplazame\Payment\Gateway\Config\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Js extends Template
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
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
            return '';
        }

        return parent::_toHtml();
    }
}
