<?php

namespace Aplazame\Payment\Http;

use Aplazame\Http\ClientInterface;
use Aplazame\Http\RequestInterface;
use Aplazame\Http\Response;
use Exception;
use RuntimeException;

class ZendClient implements ClientInterface
{
    public function send(RequestInterface $request)
    {
        $rawHeaders = array();
        foreach ($request->getHeaders() as $header => $value) {
            $rawHeaders[] = sprintf('%s:%s', $header, implode(', ', $value));
        }

        $client = new Zend_Http_Client($request->getUri());
        $client->setHeaders($rawHeaders);
        $client->setMethod($request->getMethod());

        $body = $request->getBody();
        if (!empty($body)) {
            $client->setRawData($body);
        }

        try {
            $zendResponse = $client->request();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $responseBody = $zendResponse->getBody();

        $response = new Response(
            $zendResponse->getStatus(),
            $responseBody
        );

        return $response;
    }
}
