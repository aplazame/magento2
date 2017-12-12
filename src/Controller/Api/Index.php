<?php

namespace Aplazame\Payment\Controller\Api;

use Aplazame\Serializer\JsonSerializer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;

class Index extends Action
{
    /**
     * @var \Aplazame\Payment\Gateway\Config\Config
     */
    private $aplazameConfig;

    public static function forbidden()
    {
        return [
            'status_code' => 403,
            'payload' => [
                'status' => 403,
                'type' => 'FORBIDDEN',
            ],
        ];
    }

    public static function not_found()
    {
        return [
            'status_code' => 404,
            'payload' => [
                'status' => 404,
                'type' => 'NOT_FOUND',
            ],
        ];
    }

    public static function client_error($detail)
    {
        return [
            'status_code' => 400,
            'payload' => [
                'status' => 400,
                'type' => 'CLIENT_ERROR',
                'detail' => $detail,
            ],
        ];
    }

    public static function collection($page, $page_size, array $elements)
    {
        return [
            'status_code' => 200,
            'payload' => [
                'query' => [
                    'page' => $page,
                    'page_size' => $page_size,
                ],
                'elements' => $elements,
            ],
        ];
    }

    public function __construct(
        Context $context,
        \Aplazame\Payment\Gateway\Config\Config $aplazameConfig
    ) {
        parent::__construct($context);

        $this->aplazameConfig = $aplazameConfig;
    }

    /**
     * @return HttpRequest
     */
    public function getRequest()
    {
        $request = parent::getRequest();
        if (!($request instanceof HttpRequest)) {
            throw new \Exception('Unexpected request type. ' . get_class($request));
        }
        return $request;
    }

    /**
     * @return HttpResponse
     */
    public function getResponse()
    {
        $response = parent::getResponse();
        if (!($response instanceof HttpResponse)) {
            throw new \Exception('Unexpected response type. ' . get_class($response));
        }
        return $response;
    }

    public function execute()
    {
        $request = $this->getRequest();
        $path = $request->getParam('path', '');
        $pathArguments = json_decode($request->getParam('path_arguments', '[]'), true);
        $queryArguments = json_decode($request->getParam('query_arguments', '[]'), true);
        $payload = json_decode($request->getContent(), true);

        $result = $this->route($path, $pathArguments, $queryArguments, $payload);

        $response = $this->getResponse();
        $response->setHttpResponseCode($result['status_code']);
        $response->setHeader('Content-type', 'application/json');
        $response->setBody(json_encode(JsonSerializer::serializeValue($result['payload'])));
    }

    /**
     * @param string $path
     * @param array $pathArguments
     * @param array $queryArguments
     * @param null|array $payload
     *
     * @return array
     */
    public function route($path, array $pathArguments, array $queryArguments, $payload)
    {
        if (!$this->verifyAuthentication($this->getRequest(), $this->aplazameConfig->getPrivateApiKey())) {
            return self::forbidden();
        }

        switch ($path) {
            case '/article/':
                /** @var \Aplazame\Payment\Model\Api\Controller\Article $controller */
                $controller = $this->_objectManager->get('Aplazame\Payment\Model\Api\Controller\Article');

                return $controller->articles($queryArguments);
            case '/confirm/':
                /** @var \Aplazame\Payment\Model\Api\Controller\Confirm $controller */
                $controller = $this->_objectManager->get('Aplazame\Payment\Model\Api\Controller\Confirm');

                return $controller->confirm($payload);
            case '/order/{order_id}/history/':
                /** @var \Aplazame\Payment\Model\Api\Controller\Order $controller */
                $controller = $this->_objectManager->get('Aplazame\Payment\Model\Api\Controller\Order');

                return $controller->history($pathArguments, $queryArguments);
            default:
                return self::not_found();
        }
    }

    private function verifyAuthentication(HttpRequest $request, $privateKey)
    {
        $authorization = $this->getAuthorizationFromRequest($request);
        if (!$authorization || empty($privateKey)) {
            return false;
        }
        return ($authorization === $privateKey);
    }

    private function getAuthorizationFromRequest(HttpRequest $request)
    {
        $token = $request->getParam('access_token');
        if ($token) {
            return $token;
        }

        $header = $request->getHeader('authorization');
        if (!$header) {
            return false;
        }
        return trim(str_replace('Bearer', '', $header));
    }
}
