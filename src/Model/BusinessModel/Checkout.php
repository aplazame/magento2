<?php

namespace Aplazame\Payment\Model\BusinessModel;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;

class Checkout
{
    public static function createFromQuote(Quote $quote)
    {
        $checkout = new self();
        $checkout->toc = true;
        $checkout->merchant = [
            'notification_url' => self::getUrlBuilder()->getUrl(
                'aplazame/api/index',
                [
                    '_query' => [
                        'path' => '/confirm/',
                        'quote_id' => $quote->getId(),
                    ],
                    '_nosid' => true,
                    '_secure' => true,
                ]
            ),
        ];
        $checkout->order = Order::createFromQuote($quote);
        $checkout->customer = Customer::createFromQuote($quote);
        $checkout->billing = Address::createFromAddress($quote->getBillingAddress());

        if (!$quote->isVirtual()) {
            $checkout->shipping = ShippingInfo::createFromQuote($quote);
        }

        $checkout->meta = Meta::create();

        return $checkout;
    }

    /**
     * @return \Magento\Framework\Url
     */
    private static function getUrlBuilder()
    {
        $objectManager = ObjectManager::getInstance();
        return $objectManager->get('Magento\Framework\Url');
    }
}
