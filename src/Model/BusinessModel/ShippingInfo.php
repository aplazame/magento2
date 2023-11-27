<?php

namespace Aplazame\Payment\Model\BusinessModel;

use Aplazame\Serializer\Decimal;
use Magento\Quote\Model\Quote;

class ShippingInfo
{
    public static function createFromQuote(Quote $quote)
    {
        $address = $quote->getShippingAddress();

        $shippingInfo = new \stdClass();
        $shippingInfo->first_name = $address->getFirstname();
        $shippingInfo->last_name = $address->getLastname();
        $shippingInfo->street = $address->getStreetLine(1);
        $shippingInfo->city = $address->getCity();
        $shippingInfo->state = $address->getRegion();
        $shippingInfo->country = $address->getCountryId();
        $shippingInfo->postcode = $address->getPostcode();
        $shippingInfo->name = $address->getShippingMethod();
        $shippingInfo->price = Decimal::fromFloat($address->getShippingAmount());
        $shippingInfo->phone = $address->getTelephone();
        $shippingInfo->address_addition = $address->getStreetLine(2);
        if ($address->getShippingAmount() > 0) {
            $shippingInfo->tax_rate = Decimal::fromFloat(100 * $address->getShippingTaxAmount() / $address->getShippingAmount());
        }
        $shippingInfo->discount = Decimal::fromFloat($address->getShippingDiscountAmount());

        return $shippingInfo;
    }
}
