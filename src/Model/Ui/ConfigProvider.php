<?php

namespace Aplazame\Payment\Model\Ui;

use Aplazame\Payment\Gateway\Config\Config;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Serializer\Decimal;
use Aplazame\Serializer\JsonSerializer;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Session $checkoutSession,
        Config $config
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->config = $config;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                Aplazame::PAYMENT_METHOD_CODE => [
                    'button' => $this->getButtonConfig($this->quote),
                    'instalments_enabled' => $this->config->isActive(),
                    'cart_widget_enabled' => $this->config->isCartWidgetEnabled(),
                    'cart_legal_advice_enabled' => $this->config->isCartWidgetLegalAdviceEnabled() ? 'true' : 'false',
                    'cart_default_instalments' => $this->config->getCartDefaultInstalments(),
                    'widget_legacy_enabled' => $this->config->isWidgetLegacyEnabled(),
                    'cart_widget_primary_color' => $this->config->isWidgetLegacyEnabled() ? '' : $this->config->getCartPrimaryColor(),
                    'cart_widget_layout' => $this->config->isWidgetLegacyEnabled() ? '' : $this->config->getCartLayout(),
                    'cart_widget_align' => $this->config->isWidgetLegacyEnabled() ? '' : $this->config->getCartAlign(),
                ],
            ],
        ];
    }

    /**
     * @param Quote $quote
     *
     * @return array
     */
    private function getButtonConfig(Quote $quote)
    {
        return [
            'selector' => $this->config->getPaymentButton(),
            'amount' => JsonSerializer::serializeValue(Decimal::fromFloat($quote->getGrandTotal())),
            'currency' => $quote->getQuoteCurrencyCode(),
        ];
    }
}
