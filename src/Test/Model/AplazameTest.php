<?php

namespace Aplazame\Payment\Model\Test;

use Aplazame\Payment\Model\Aplazame;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Block\Info\Instructions;
use Magento\Payment\Helper\Data;
use PHPUnit_Framework_TestCase as TestCase;

class AplazameTest extends TestCase
{
    /**
     * @var Aplazame
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    protected function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $eventManager = $this->getMock(ManagerInterface::class, [], [], '', false);
        $paymentDataMock = $this->getMock(Data::class, [], [], '', false);

        $this->scopeConfig = $this->getMock(ScopeConfigInterface::class, [], [], '', false);
        $this->object = $helper->getObject(
            Aplazame::class,
            [
                'eventManager' => $eventManager,
                'paymentData' => $paymentDataMock,
                'scopeConfig' => $this->scopeConfig,
            ]
        );
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals(Instructions::class, $this->object->getInfoBlockType());
    }
}
