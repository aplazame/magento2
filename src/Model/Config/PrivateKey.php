<?php

namespace Aplazame\Payment\Model\Config;

use Aplazame\Api\ApiClientException;
use Aplazame\Api\Client;

class PrivateKey extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Aplazame\Payment\Gateway\Config\Config
     */
    private $aplazameConfig;

    /**
     * @var string
     */
    private $publicApiKey;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    private $configValueFactory;

    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    public function __construct(
        \Aplazame\Payment\Gateway\Config\Config $aplazameConfig,
        \Magento\Framework\Url $urlBuilder,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection);

        $this->aplazameConfig = $aplazameConfig;
        $this->configValueFactory = $configValueFactory;
        $this->urlBuilder = $urlBuilder;
    }

    public function beforeSave()
    {
        if (empty($this->getValue())) {
            return parent::beforeSave();
        }

        $label = $this->getData('field_config/label');

        $client = new Client(
            $this->aplazameConfig->getApiBaseUri(),
            ($this->aplazameConfig->isSandbox() ? Client::ENVIRONMENT_SANDBOX : Client::ENVIRONMENT_PRODUCTION),
            $this->getValue()
        );

        try {
            $response = $client->patch('/me', [
                'confirmation_url' => $this->urlBuilder->getUrl(
                    'aplazame/api/index',
                    [
                        '_query' => [
                            'path' => '/confirm/',
                        ],
                        '_nosid' => true,
                        '_secure' => true,
                    ]
                ),
            ]);
        } catch (ApiClientException $apiClientException) {
            throw new \Magento\Framework\Exception\ValidatorException(__($label . ' ' . $apiClientException->getMessage()));
        }

        $this->publicApiKey = $response['public_api_key'];

        return parent::beforeSave();
    }

    public function afterSave()
    {
        $path = 'payment/aplazame_payment/public_api_key';
        $this->configValueFactory->create()->load($path, 'path')
            ->setValue($this->publicApiKey)
            ->setPath($path)
            ->save();

        return parent::afterSave();
    }
}
