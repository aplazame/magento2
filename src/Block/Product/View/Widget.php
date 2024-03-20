<?php

namespace Aplazame\Payment\Block\Product\View;

use Aplazame\Payment\Gateway\Config\Config;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Directory\Model\Currency;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Widget extends AbstractProduct
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Block\Product\Context $context,
        Config $config,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
    }

    public function isHiddenInCurrentLanguage()
{
    // Obtiene el código de localización actual
    $currentLocale = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE);
    // Obtiene la configuración de los idiomas seleccionados para ocultar el widget
    $hiddenLanguages = $this->scopeConfig->getValue('payment/aplazame_payment/aplazame_widget/hide_in_languages', ScopeInterface::SCOPE_STORE) ?? '';

    if (in_array($currentLocale, explode(',', $hiddenLanguages))) {
        return true;
    }

    return false;
}

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        $product = $this->getProduct();

        return $product->getFinalPrice();
    }

    public function getCurrencyCode()
    {
        /** @var Currency $currencyModel */
        $currencyModel = $this->priceCurrency->getCurrency();

        return $currencyModel->getCurrencyCode();
    }

    public function getShowDownpaymentInfo()
    {
        return $this->config->isProductWidgetDownpaymentInfoEnabled() ? 'true' : 'false';
    }

    public function getShowLegalAdvice()
    {
        return $this->config->isProductWidgetLegalAdviceEnabled() ? 'true' : 'false';
    }

    public function getShowPayIn4()
    {
        return $this->config->isProductWidgetPayIn4Enabled();
    }

    public function getDefaultInstalments()
    {
        return $this->config->getProductDefaultInstalments();
    }

    public function getPriceSelector()
    {
        return $this->config->getProductCSS();
    }

    public function getOptionOutOfLimits()
    {
        return $this->config->getWidgetOutOfLimits();
    }

    public function isWidgetLegacyEnabled()
    {
        return $this->config->isWidgetLegacyEnabled();
    }

    public function getShowBorder()
    {
        return $this->config->isProductWidgetBorderEnabled() ? 'true' : 'false';
    }

    public function getShowMaxDesired()
    {
        return $this->config->isProductWidgetMaxDesiredEnabled() ? 'true' : 'false';
    }

    public function getPrimaryColor()
    {
        return $this->config->getProductPrimaryColor();
    }

    public function getWidgetLayout()
    {
        return $this->config->getProductLayout();
    }

    public function getWidgetAlign()
    {
        return $this->config->getProductAlign();
    }

    public function isAplazameActive()
    {
        return $this->config->isActive();
    }
}
