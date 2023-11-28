<?php

namespace Aplazame\Payment\Controller\Payment;

use Aplazame\Api\ApiClientException;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Payment\Model\BusinessModel\Checkout;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http as HttpResponse;

class Index extends Action
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Aplazame\Payment\Model\Api\AplazameClient
     */
    private $aplazameClient;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Aplazame\Payment\Model\Api\AplazameClient $aplazameClient
    ) {
        parent::__construct($context);

        $this->quote = $checkoutSession->getQuote();
        $this->aplazameClient = $aplazameClient;
    }

    /**
     * @return HttpResponse
     * @throws \Exception
     */
    public function getResponse()
    {
        $response = parent::getResponse();
        if (!($response instanceof HttpResponse)) {
            throw new \Exception('Unexpected response type. ' . get_class($response));
        }
        return $response;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $response = $this->getResponse()->setHeader('Content-Type', 'application/json');

        $payment = $this->quote->getPayment();
        if ($payment->getMethod() !== Aplazame::PAYMENT_METHOD_CODE) {
            throw new \Exception($payment->getMethod() . 'is not an Aplazame payment method');
        }

        $payload = json_decode(json_encode(Checkout::createFromQuote($this->quote)), true);

        try {
            $checkout = $this->aplazameClient->apiClient->request(
                'POST',
                '/checkout',
                $payload
            );
            $response->setBody(json_encode($checkout));
        } catch (ApiClientException $e) {
            $response->setStatusCode(400);
            $response->setBody(json_encode($e->getError()));
        }
    }
}
