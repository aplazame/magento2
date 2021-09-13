<?php

namespace Aplazame\Payment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends \Magento\Payment\Gateway\Config\Config
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
     * @return bool
     */
    public function isWidgetLegacyEnabled()
    {
        return (bool) $this->getValue('widget_legacy_enabled');
    }

    /**
     * @return bool
     */
    public function isProductWidgetLegalAdviceEnabled()
    {
        return (bool) $this->getValue('product_legal_advice');
    }

    /**
     * @return bool
     */
    public function isProductWidgetPayIn4Enabled()
    {
        return (bool) $this->getValue('product_pay_in_4');
    }

    /**
     * @return bool
     */
    public function isProductWidgetBorderEnabled()
    {
        return (bool) $this->getValue('product_widget_border');
    }

    /**
     * @return int
     */
    public function getProductDefaultInstalments()
    {
        return (int) $this->getValue('product_default_instalments');
    }

    /**
     * @return string
     */
    public function getProductPrimaryColor()
    {
        return (string) $this->getValue('product_widget_primary_color');
    }

    /**
     * @return string
     */
    public function getProductLayout()
    {
        return (string) $this->getValue('product_widget_layout');
    }

    /**
     * @return string
     */
    public function getProductAlign()
    {
        return (string) $this->getValue('product_widget_align');
    }

    /**
     * @return bool
     */
    public function isCartWidgetEnabled()
    {
        return (bool) $this->getValue('cart_widget_enabled');
    }

    /**
     * @return bool
     */
    public function isCartWidgetLegalAdviceEnabled()
    {
        return (bool) $this->getValue('cart_legal_advice');
    }

    /**
     * @return bool
     */
    public function isCartWidgetPayIn4Enabled()
    {
        return (bool) $this->getValue('cart_pay_in_4');
    }

    /**
     * @return int
     */
    public function getCartDefaultInstalments()
    {
        return (int) $this->getValue('cart_default_instalments');
    }

    /**
     * @return string
     */
    public function getCartPrimaryColor()
    {
        return (string) $this->getValue('cart_widget_primary_color');
    }

    /**
     * @return string
     */
    public function getCartLayout()
    {
        return (string) $this->getValue('cart_widget_layout');
    }

    /**
     * @return string
     */
    public function getCartAlign()
    {
        return (string) $this->getValue('cart_widget_align');
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
