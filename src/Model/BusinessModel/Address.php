<?php

namespace Aplazame\Payment\Model\BusinessModel;

class Address
{
    public static function createFromAddress(\Magento\Quote\Model\Quote\Address $address)
    {
        $aAddress = new self();
        $aAddress->first_name = $address->getFirstname();
        $aAddress->last_name = $address->getLastname();
        $aAddress->street = $address->getStreetLine(1);
        $aAddress->city = $address->getCity();
        $aAddress->state = $address->getRegion();
        $aAddress->country = $address->getCountryId();
        $aAddress->postcode = $address->getPostcode();
        $aAddress->phone = $address->getTelephone();
        $aAddress->address_addition = $address->getStreetLine(2);

        return $aAddress;
    }
}
