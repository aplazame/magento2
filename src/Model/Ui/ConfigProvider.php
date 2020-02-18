<?php

namespace Aplazame\Payment\Model\Ui;

use Aplazame\Payment\Gateway\Config\Config;
use Aplazame\Payment\Gateway\Config\ConfigPayLater;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Payment\Model\AplazamePayLater;
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

    /**
     * @var ConfigPayLater
     */
    private $configPayLater;

    public function __construct(
        Session $checkoutSession,
        Config $config,
        ConfigPayLater $configPayLater
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->config = $config;
        $this->configPayLater = $configPayLater;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                Aplazame::PAYMENT_METHOD_CODE => [
                    'button' => $this->getButtonConfig($this->quote, $this->config, 'instalments'),
                    'instalments_enabled' => $this->config->isActive(),
                    'cart_widget_enabled' => $this->config->isCartWidgetEnabled(),
                    'cart_legal_advice_enabled' => $this->config->isCartWidgetLegalAdviceEnabled() ? 'true' : 'false',
                ],
                AplazamePayLater::PAYMENT_METHOD_CODE => [
                    'button' => $this->getButtonConfig($this->quote, $this->configPayLater, 'pay_later'),
                ],
            ],
        ];
    }

    /**
     * @param Quote $quote
     * @param Config|ConfigPayLater $config
     * @param String $type
     *
     * @return array
     */
    private function getButtonConfig(Quote $quote, $config, $type)
    {
        return [
            'selector' => $config->getPaymentButton(),
            'amount'   => JsonSerializer::serializeValue(Decimal::fromFloat($quote->getGrandTotal())),
            'currency' => $quote->getQuoteCurrencyCode(),
            'product'  => ['type' => $type]
        ];
    }
}
