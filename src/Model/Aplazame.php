<?php

namespace Aplazame\Payment\Model;

use Aplazame\Payment\Model\Api\AplazameClient;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order\Payment;

class Aplazame extends AbstractMethod
{
    const PAYMENT_METHOD_CODE = 'aplazame_payment';

    protected $_code = self::PAYMENT_METHOD_CODE;
    protected $_canAuthorize = true;
    protected $_canCancelInvoice = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = false;

    /**
     * @var AplazameClient
     */
    private $aplazameClient;

    /**
     * @var \Aplazame\Payment\Gateway\Config\Config
     */
    private $aplazameConfig;

    public function __construct(
        AplazameClient $aplazameClient,
        \Aplazame\Payment\Gateway\Config\Config $aplazameConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->aplazameClient = $aplazameClient;
        $this->aplazameConfig = $aplazameConfig;
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     *
     * @return $this
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        if ($payment->getIsFraudDetected()) {
            return $this;
        }

        $order = $payment->getOrder();
        $mid = $order->getIncrementId();

        $payment->setTransactionId($mid);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     *
     * @return bool
     */
    public function acceptPayment(InfoInterface $payment)
    {
        if ($payment->getIsFraudDetected()) {
            return false;
        }

        if ($this->aplazameConfig->shouldAutoInvoice()) {
            $payment->registerCaptureNotification($payment->getAmountAuthorized());
        }

        $order = $payment->getOrder();
        if (!$order->getEmailSent()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender */
            $orderSender = $objectManager->get('Magento\Sales\Model\Order\Email\Sender\OrderSender');
            $orderSender->send($order);
        }

        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     *
     * @return bool
     */
    public function denyPayment(InfoInterface $payment)
    {
        return true;
    }

    public function cancel(InfoInterface $payment)
    {
        /** @var Payment $payment */
        $order = $payment->getOrder();

        $this->aplazameClient->cancelOrder($order->getQuoteId());

        return $this;
    }

    public function void(InfoInterface $payment)
    {
        return $this->cancel($payment);
    }

    public function refund(InfoInterface $payment, $amount)
    {
        /** @var Payment $payment */
        $order = $payment->getOrder();

        $this->aplazameClient->refundAmount($order->getQuoteId(), $amount);

        return $this;
    }
}
