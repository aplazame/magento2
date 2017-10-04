<?php

namespace Aplazame\Payment\Model\Api;

use Aplazame\Api\Client;
use Aplazame\Payment\Gateway\Config\Config;
use Aplazame\Serializer\Decimal;

class AplazameClient
{
    /**
     * @var string
     */
    public $apiBaseUri;

    /**
     * @var Client
     */
    public $apiClient;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;

        $this->apiBaseUri = $config->getApiBaseUri();
        $this->apiClient = new Client(
            $this->apiBaseUri,
            (($config->isSandbox()) ? Client::ENVIRONMENT_SANDBOX : Client::ENVIRONMENT_PRODUCTION),
            $config->getPrivateApiKey()
        );
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function authorize($orderId)
    {
        return $this->apiClient->request('POST', '/orders/' . urlencode(urlencode($orderId)) . '/authorize');
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function fetchOrder($orderId)
    {
        $orders = $this->apiClient->request('GET', '/orders?mid=' . urlencode($orderId));

        return $orders['results'][0];
    }

    /**
     * @return array
     */
    public function cancelOrder($quoteId)
    {
        return $this->apiClient->request('POST', $this->getEndpointForOrder($quoteId) . '/cancel');
    }

    /**
     * @param float $amount
     * @return array
     */
    public function refundAmount($quoteId, $amount)
    {
        $data = ['amount' => Decimal::fromFloat($amount)->jsonSerialize()];

        return $this->apiClient->request('POST', $this->getEndpointForOrder($quoteId) . '/refund', $data);
    }

    /**
     * @return string
     */
    protected function getEndpointForOrder($quoteId)
    {
        return '/orders/' . urlencode(urlencode($quoteId));
    }
}
