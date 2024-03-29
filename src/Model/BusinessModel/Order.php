<?php

namespace Aplazame\Payment\Model\BusinessModel;

use Aplazame\Serializer\Date;
use Aplazame\Serializer\Decimal;
use DateTime;
use Magento\Quote\Model\Quote;

class Order
{
    public static function createFromQuote(Quote $quote, $quote_date = null)
    {
        $aOrder = new \stdClass();
        $aOrder->id = $quote->getId();
        $aOrder->currency = $quote->getQuoteCurrencyCode();
        $aOrder->total_amount = Decimal::fromFloat($quote->getGrandTotal());
        $aOrder->articles = array_map(['Aplazame\Payment\Model\BusinessModel\Article', 'crateFromQuoteItem'], $quote->getAllVisibleItems());

        // TODO
//        if (($discounts = $quote->getDiscountAmount()) !== null) {
//            $aOrder->discount = Decimal::fromFloat(-$discounts);
//        }

        if ($quote_date) {
            $aOrder->created = Date::fromDateTime(new DateTime($quote_date));
        }

        return $aOrder;
    }
}
