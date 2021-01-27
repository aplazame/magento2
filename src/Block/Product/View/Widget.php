<?php

namespace Aplazame\Payment\Block\Product\View;

use Aplazame\Payment\Gateway\Config\Config;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Directory\Model\Currency;
use Magento\Framework\Pricing\PriceCurrencyInterface;

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

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Block\Product\Context $context,
        Config $config,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
        $this->config = $config;
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

    public function getShowLegalAdvice()
    {
        return $this->config->isProductWidgetLegalAdviceEnabled() ? 'true' : 'false';
    }

    public function getDefaultInstalments()
    {
        return $this->config->getProductDefaultInstalments();
    }

    public function isAplazameActive()
    {
        return $this->config->isActive();
    }
}
