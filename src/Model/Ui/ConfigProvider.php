<?php

namespace Aplazame\Payment\Model\Ui;

use Aplazame\Payment\Model\BusinessModel\Checkout;
use Aplazame\Payment\Gateway\Config\Config;
use Aplazame\Payment\Model\Aplazame;
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
                    'checkout' => $this->getCheckoutConfig($this->quote),
                ],
            ],
        ];
    }

    private function getButtonConfig(Quote $quote)
    {
        return [
            'selector' => $this->config->getPaymentButton(),
            'amount'   => $quote->getGrandTotal(),
            'currency' => $quote->getQuoteCurrencyCode(),
        ];
    }

    private function getCheckoutConfig(Quote $quote)
    {
        return JsonSerializer::serializeValue(Checkout::createFromQuote($quote));
    }
}
