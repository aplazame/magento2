<?php

namespace Aplazame\Payment\Setup;

use Aplazame\Api\Client;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Aplazame\Payment\Gateway\Config\Config
     */
    private $aplazameConfig;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    private $configValueFactory;

    public function __construct(
        \Aplazame\Payment\Gateway\Config\Config $aplazameConfig,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory
    ) {
        $this->aplazameConfig = $aplazameConfig;
        $this->configValueFactory = $configValueFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (!empty($this->aplazameConfig->getPrivateApiKey())) {
            $response = $this->setupConfirmationUrl();

            $this->setPublicKey($response['public_api_key']);
        }
    }

    private function setupConfirmationUrl()
    {
        $apiClient = new Client(
            $this->aplazameConfig->getApiBaseUri(),
            ($this->aplazameConfig->isSandbox() ? Client::ENVIRONMENT_SANDBOX : Client::ENVIRONMENT_PRODUCTION),
            $this->aplazameConfig->getPrivateApiKey()
        );

        $response = $apiClient->patch('/me', [
            'confirmation_url' => '',
        ]);

        return $response;
    }

    private function setPublicKey($publicKey)
    {
        $path = 'payment/aplazame_payment/public_api_key';
        $this->configValueFactory->create()
             ->load($path, 'path')
             ->setValue($publicKey)
             ->setPath($path)
             ->save();
    }
}
