<?php

namespace Aplazame\Payment\Model\BusinessModel;

use Aplazame\Serializer\Date;
use DateTime;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;

class Customer
{
    public static function createFromQuote(Quote $quote)
    {
        if (empty($quote->getCustomerId())) {
            return self::createGuessCustomerFromQuote($quote);
        }

        return self::createFromSignedCustomer($quote->getCustomer());
    }

    public static function createFromSignedCustomer(CustomerInterface $customer)
    {
        switch ($customer->getGender()) {
            case '1':
                $gender = 1;
                break;
            case '2':
                $gender = 2;
                break;
            default:
                $gender = 0;
        }

        $aCustomer = new self();
        $aCustomer->email = $customer->getEmail();
        $aCustomer->type = 'e';
        $aCustomer->gender = $gender;
        $aCustomer->id = $customer->getId();
        $aCustomer->first_name = $customer->getFirstname();
        $aCustomer->last_name = $customer->getLastname();
        if (($birthday = $customer->getDob()) !== null) {
            $aCustomer->birthday = $birthday;
        }
        // TODO
//        $aCustomer->date_joined = Date::fromDateTime(new DateTime('@' . $customer->getCreatedAtTimestamp()));
        $logCustomer = self::getCustomerLogger()->get($customer->getId());
        if (($lastLogin = $logCustomer->getLastLoginAt()) != null) {
            $aCustomer->last_login = Date::fromDateTime(new DateTime($lastLogin));
        }

        return $aCustomer;
    }

    public static function createGuessCustomerFromQuote(Quote $quote)
    {
        $aCustomer = new self();
        $aCustomer->type = 'g';
        $aCustomer->gender = 0;

        $billingAddress = $quote->getBillingAddress();
        if (!$billingAddress) {
            return $aCustomer;
        }

        $aCustomer->email = $billingAddress->getEmail();
        $aCustomer->first_name = $billingAddress->getFirstname();
        $aCustomer->last_name = $billingAddress->getLastname();

        return $aCustomer;
    }

    /**
     * @return \Magento\Customer\Model\Logger
     */
    private static function getCustomerLogger()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var \Magento\Customer\Model\Logger $productMetadata */
        return $objectManager->get('Magento\Customer\Model\Logger');
    }
}
