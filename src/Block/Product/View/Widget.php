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

    public function isInstalmentsActive()
    {
        return $this->config->isActive();
    }

    /**
     * Solo renderizamos si tenemos producto,
     * y si el modulo esta activo en la tienda actual
     * si no hay producto no renderizamos nada (empty string).
     *
     * @return string
     */
    /*public function _toHtml()
    {
        if (Mage::helper('aplazame')->isEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }*/
}
