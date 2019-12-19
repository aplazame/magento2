<?php

namespace Aplazame\Payment\Model\Api\BusinessModel;

use Aplazame\Payment\Model\BusinessModel\Address;
use Aplazame\Payment\Model\BusinessModel\Customer;
use Aplazame\Payment\Model\BusinessModel\Meta;
use Aplazame\Payment\Model\BusinessModel\Order;
use Aplazame\Payment\Model\BusinessModel\ShippingInfo;
use Magento\Quote\Model\Quote;

class HistoricalOrder
{
    /**
     * @param Quote $quote
     * @param string $status
     * @return array
     */
    public static function createFromQuotes(Quote $quote, $status)
    {
        switch ($status) {
            case 'canceled':
                $payment_status = 'cancelled';
                $status = 'cancelled';
                break;
            case 'closed':
                $payment_status = 'refunded';
                $status = 'refunded';
                break;
            case 'complete':
            case 'processing':
                $payment_status = 'payed';
                break;
            case 'holded':
            case 'new':
            case 'payment_review':
            case 'pending_payment':
                $payment_status = 'pending';
                $status = 'payment';
                break;
            default:
                $payment_status = 'unknown';
                $status = 'custom_' . $status;
        }

        $serialized = [
            'customer' => Customer::createFromQuote($quote),
            'order' => Order::createFromQuote($quote, $quote->getCreatedAt()),
            'billing' => Address::createFromAddress($quote->getBillingAddress()),
            'meta' => Meta::create(),
            'payment' => array(
                'method' => $quote->getPayment()->getMethodInstance()->getCode(),
                'status' => $payment_status,
            ),
            'status' => $status,

        ];

        if (!$quote->isVirtual()) {
            $serialized['shipping'] = ShippingInfo::createFromQuote($quote);
        }

        return $serialized;
    }
}
