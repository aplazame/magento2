<?php

namespace Aplazame\Payment\Model\Ui;

use Aplazame\Payment\Gateway\Config\Config;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Serializer\Decimal;
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
                    'cart_downpayment_info_enabled' => $this->config->isCartWidgetDownpaymentInfoEnabled() ? 'true' : 'false',
                    'cart_legal_advice_enabled' => $this->config->isCartWidgetLegalAdviceEnabled() ? 'true' : 'false',
                    'cart_pay_in_4_enabled' => $this->config->isCartWidgetPayIn4Enabled(),
                    'cart_default_instalments' => $this->config->getCartDefaultInstalments(),
                    'widget_out_of_limits' => $this->config->getWidgetOutOfLimits(),
                    'cart_max_desired_enabled' => $this->config->isCartWidgetMaxDesiredEnabled()  ? 'true' : 'false',
                    'cart_widget_layout' => $this->config->getCartLayout(),
                    'cart_widget_align' => $this->config->getCartAlign(),
                    'cart_widget_primary_color' => $this->config->getCartPrimaryColor(),
                    'cart_widget_ver' => $this->config->getCartWidgetVer(),
                    'cart_slider_enabled' => $this->config->isCartSliderEnabled()  ? 'true' : 'false',
                    'cart_small_size_enabled' => $this->config->isCartSmallSizeEnabled()  ? 'true' : 'false',
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
            'amount' => Decimal::fromFloat($quote->getGrandTotal()),
            'currency' => $quote->getQuoteCurrencyCode(),
        ];
    }
}
