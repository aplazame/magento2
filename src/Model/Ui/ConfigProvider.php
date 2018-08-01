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
                    'cart_widget_enabled' => $this->config->getCartWidgetIsEnabled(),
                ],
            ],
        ];
    }

    private function getButtonConfig(Quote $quote)
    {
        return [
            'selector' => $this->config->getPaymentButton(),
            'amount'   => JsonSerializer::serializeValue(Decimal::fromFloat($quote->getGrandTotal())),
            'currency' => $quote->getQuoteCurrencyCode(),
        ];
    }
}
