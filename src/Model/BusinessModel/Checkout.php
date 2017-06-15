<?php

namespace Aplazame\Payment\Model\BusinessModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;

class Checkout
{
    public static function createFromQuote(Quote $quote)
    {
        $checkout = new self();
        $checkout->toc = true;
        $checkout->merchant = self::getMerchant();
        $checkout->order = Order::crateFromQuote($quote);
        $checkout->customer = Customer::createFromQuote($quote);
        $checkout->billing = Address::createFromAddress($quote->getBillingAddress());
        $checkout->shipping = ShippingInfo::createFromQuote($quote);
        $checkout->meta = [
            'module' => [
                'name' => 'aplazame:magento',
                'version' => self::getMagentoVersion(),
            ],
            'version' => self::getModuleVersion(),
        ];

        return $checkout;
    }

    /**
     * @return string
     */
    private static function getMagentoVersion()
    {
        $objectManager   = ObjectManager::getInstance();
        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');

        return $productMetadata->getVersion();
    }

    /**
     * @return string
     */
    private static function getModuleVersion()
    {
        $objectManager   = ObjectManager::getInstance();
        $moduleInfo =  $objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Aplazame_Payment');

        return $moduleInfo['setup_version'];
    }

    private static function getMerchant()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var UrlInterface $urlBuilder */
        $urlBuilder = $objectManager->get('Magento\Framework\UrlInterface');

        $merchant = new \stdClass();
        $merchant->confirmation_url = $urlBuilder->getUrl('aplazame/payment/confirm', ['_secure' => true]);

        return $merchant;
    }
}
