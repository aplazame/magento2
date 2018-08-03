<?php

namespace Aplazame\Payment\Model\Api\BusinessModel;

use Aplazame\Payment\Model\BusinessModel\Address;
use Aplazame\Payment\Model\BusinessModel\ShippingInfo;
use Aplazame\Serializer\Decimal;
use Magento\Quote\Model\Quote;

class HistoricalOrder
{
    /**
     * @return array
     */
    public static function createFromQuotes(Quote $quote)
    {
        $serialized = [
            'id' => $quote->getId(),
            'amount' => Decimal::fromFloat($quote->getGrandTotal()),
            'due' => Decimal::fromFloat($quote->getTotalDue()),
            'status' => $quote->getStatus(),
            'type' => $quote->getPayment()->getMethodInstance()->getCode(),
            'order_date' => date(DATE_ISO8601, strtotime($quote->getCreatedAt())),
            'currency' => $quote->getQuoteCurrencyCode(),
            'billing' => Address::createFromAddress($quote->getBillingAddress()),
        ];

        if (!$quote->isVirtual()) {
            $serialized['shipping'] = ShippingInfo::createFromQuote($quote);
        }

        return $serialized;
    }
}
