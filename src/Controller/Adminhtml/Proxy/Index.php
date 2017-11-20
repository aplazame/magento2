<?php

namespace Aplazame\Payment\Controller\Adminhtml\Proxy;

use Aplazame\Payment\Model\Api\AplazameClient;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @var AplazameClient
     */
    private $aplazameClient;

    public function __construct(
        Context $context,
        AplazameClient $aplazameClient
    ) {
        $this->aplazameClient = $aplazameClient;

        parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest();

        $data = json_decode($request->getParam('data'));

        $response = $this->aplazameClient->apiClient->request(
            $request->getParam('method'),
            $request->getParam('path'),
            $data
        );

        $this->getResponse()
             ->setHeader('Content-Type', 'application/json')
             ->setBody(json_encode($response));
    }
}
