<?php

namespace Aplazame\Payment\Observer;

use Aplazame\Api\ApiClientException;
use Aplazame\Payment\Model\Api\AplazameClient;
use Aplazame\Payment\Model\AplazamePayLater;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CaptureOrder implements ObserverInterface
{
    /**
     * @var AplazameClient
     */
    private $aplazameClient;

    public function __construct(
        AplazameClient $aplazameClient
    )
    {
        $this->aplazameClient = $aplazameClient;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getShipment()->getOrder();
        $paymentMethod = $order->getPayment()->getMethodInstance();

        if (!($paymentMethod instanceof AplazamePayLater)) {
            // Only capture payments made with Aplazame Pay Later
            return $this;
        }

        $quoteId = $order->getQuoteId();
        $amount = $order->getGrandTotal() - $order->getTotalRefunded();

        try {
            $this->aplazameClient->captureAmount($quoteId, $amount);
        } catch (ApiClientException $e) {
            throw $e;
        }

        return $this;
    }
}
