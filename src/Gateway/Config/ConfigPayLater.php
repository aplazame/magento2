<?php

namespace Aplazame\Payment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigPayLater extends \Magento\Payment\Gateway\Config\Config
{
    /**
     * @var string
     */
    private $apiBaseUri;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);

        $this->apiBaseUri = getenv('APLAZAME_API_BASE_URI') ? getenv('APLAZAME_API_BASE_URI') : 'https://api.aplazame.com';
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue('active');
    }

    /**
     * @return bool
     */
    public function isSandbox()
    {
        return (bool) $this->getValue('sandbox');
    }

    /**
     * @return string
     */
    public function getApiBaseUri()
    {
        return $this->apiBaseUri;
    }

    /**
     * @return string
     */
    public function getPrivateApiKey()
    {
        return $this->getValue('private_api_key');
    }

    /**
     * @return string
     */
    public function getPublicApiKey()
    {
        return $this->getValue('public_api_key');
    }

    /**
     * @return bool
     */
    public function getCartWidgetIsEnabled()
    {
        return (bool) $this->getValue('cart_widget_enabled');
    }

    /**
     * @return string
     */
    public function getPaymentButton()
    {
        return $this->getValue('payment_button');
    }

    /**
     * @return bool
     */
    public function shouldAutoInvoice()
    {
        return (bool) $this->getValue('autoinvoice');
    }
}
