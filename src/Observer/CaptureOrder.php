<?php

namespace Aplazame\Payment\Observer;

use Aplazame\Api\ApiClientException;
use Aplazame\Payment\Model\Api\AplazameClient;
use Aplazame\Payment\Model\Aplazame;
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

        if (!($paymentMethod instanceof Aplazame)) {
            return $this;
        }

        $quoteId = $order->getQuoteId();

        try {
            $payload = $this->aplazameClient->getOrderCapture($quoteId);
        } catch (ApiClientException $e) {
            throw $e;
        }

        if ($payload['remaining_capture_amount'] != 0) {
            try {
                $this->aplazameClient->captureAmount($quoteId, $payload['remaining_capture_amount']);
            } catch (ApiClientException $e) {
                throw $e;
            }
        }

        return $this;
    }
}
