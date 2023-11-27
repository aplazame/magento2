<?php

namespace Aplazame\Payment\Model\BusinessModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

class Meta
{
    public static function create()
    {
        $aMeta = new \stdClass();
        $aMeta->module = [
            'name' => 'aplazame:magento',
            'version' => self::getModuleVersion(),
        ];
        $aMeta->version = self::getMagentoVersion();

        return $aMeta;
    }

    /**
     * @return string
     */
    private static function getMagentoVersion()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');

        return $productMetadata->getVersion();
    }

    /**
     * @return string
     */
    private static function getModuleVersion()
    {
        $objectManager = ObjectManager::getInstance();
        $moduleInfo = $objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Aplazame_Payment');

        return $moduleInfo['setup_version'];
    }
}
