<?php

namespace Aplazame\Payment\Model;

use Aplazame\Payment\Model\Api\AplazameClient;
use Aplazame\Serializer\Decimal;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;

class Aplazame extends AbstractMethod
{
    const PAYMENT_METHOD_CODE = 'aplazame_payment';

    protected $_code = self::PAYMENT_METHOD_CODE;
    protected $_canCancelInvoice = true;
    protected $_canOrder = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;

    /**
     * @var AplazameClient
     */
    private $aplazameClient;

    /**
     * @var Payment\Transaction\BuilderInterface
     */
    private $transactionBuilder;

    public function __construct(
        AplazameClient $aplazameClient,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
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
        $this->transactionBuilder = $transactionBuilder;
    }

    public function order(InfoInterface $payment, $amount)
    {
        /** @var Payment $payment */

        $order = $payment->getOrder();
        $checkoutToken = $order->getQuoteId();

        $aOrder = $this->aplazameClient->fetchOrder($checkoutToken);
        if ($aOrder['total_amount'] !== Decimal::fromFloat($amount)->jsonSerialize() ||
            $aOrder['currency']['code'] !== $order->getOrderCurrencyCode()
        ) {
            throw new LocalizedException(__(
                'Aplazame authorized amount of ' . $aOrder['total_amount'] .
                ' does not match requested amount of: ' . $amount
            ));
        }

        $this->aplazameClient->authorize($checkoutToken);

        $message = __('Ordered amount of %1', $amount);
        $payment
            ->setTransactionId($checkoutToken)
            ->setIsTransactionClosed(false)
            ->setIsTransactionPending(true)
            ->setIsTransactionApproved(true);
        $transaction = $this->transactionBuilder
            ->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payment->getTransactionId())
            ->build(Transaction::TYPE_ORDER);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        $payment->setSkipOrderProcessing(true);

        return $this;
    }

    public function void(InfoInterface $payment)
    {
        return $this->cancel($payment);
    }

    public function cancel(InfoInterface $payment)
    {
        /** @var Payment $payment */
        $order = $payment->getOrder();

        $this->aplazameClient->cancelOrder($order->getQuoteId());

        return $this;
    }

    public function refund(InfoInterface $payment, $amount)
    {
        /** @var Payment $payment */
        $order = $payment->getOrder();

        $this->aplazameClient->refundAmount($order->getQuoteId(), $amount);

        return $this;
    }
}
