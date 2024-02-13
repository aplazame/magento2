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

    // Widget config

    /**
     * @return bool
     */
    public function isWidgetLegacyEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/widget_legacy_enabled');
    }

    /**
     * @return string
     */
    public function getWidgetOutOfLimits()
    {
        return (string) $this->getValue('aplazame_widget/widget_out_of_limits');
    }

    // Product widget

    /**
     * @return bool
     */
    public function isProductWidgetDownpaymentInfoEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_product_widget/product_downpayment_info');
    }

    /**
     * @return bool
     */
    public function isProductWidgetLegalAdviceEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_product_widget/product_legal_advice');
    }

    /**
     * @return bool
     */
    public function isProductWidgetPayIn4Enabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_product_widget/product_pay_in_4');
    }

    /**
     * @return bool
     */
    public function isProductWidgetBorderEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_product_widget/product_widget_border');
    }

    /**
     * @return bool
     */
    public function isProductWidgetMaxDesiredEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_product_widget/product_widget_max_desired');
    }

    /**
     * @return int
     */
    public function getProductDefaultInstalments()
    {
        return (int) $this->getValue('aplazame_widget/aplazame_product_widget/product_default_instalments');
    }

    /**
     * @return string
     */
    public function getProductCSS()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_product_widget/product_css');
    }

    /**
     * @return string
     */
    public function getProductPrimaryColor()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_product_widget/product_widget_primary_color');
    }

    /**
     * @return string
     */
    public function getProductLayout()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_product_widget/product_widget_layout');
    }

    /**
     * @return string
     */
    public function getProductAlign()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_product_widget/product_widget_align');
    }

    // Cart widget

    /**
     * @return bool
     */
    public function isCartWidgetEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_widget_enabled');
    }

    /**
     * @return bool
     */
    public function isCartWidgetDownpaymentInfoEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_downpayment_info');
    }

    /**
     * @return bool
     */
    public function isCartWidgetLegalAdviceEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_legal_advice');
    }

    /**
     * @return bool
     */
    public function isCartWidgetPayIn4Enabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_pay_in_4');
    }

    /**
     * @return int
     */
    public function getCartDefaultInstalments()
    {
        return (int) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_default_instalments');
    }

    /**
     * @return bool
     */
    public function isCartWidgetMaxDesiredEnabled()
    {
        return (bool) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_widget_max_desired');
    }

    /**
     * @return string
     */
    public function getCartPrimaryColor()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_widget_primary_color');
    }

    /**
     * @return string
     */
    public function getCartLayout()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_widget_layout');
    }

    /**
     * @return string
     */
    public function getCartAlign()
    {
        return (string) $this->getValue('aplazame_widget/aplazame_cart_widget/cart_widget_align');
    }

    // API & Payment

    /**
     * @return bool
     */
    public function isChangeToOrderIdEnabled()
    {
        return (bool) $this->getValue('aplazame_misc/change_to_order_id');
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
        return $this->getValue('aplazame_misc/payment_button');
    }

    /**
     * @return bool
     */
    public function shouldAutoInvoice()
    {
        return (bool) $this->getValue('autoinvoice');
    }
}
