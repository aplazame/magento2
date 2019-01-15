<?php

namespace Aplazame\Payment\Model\Api\Controller;

use Aplazame\Payment\Controller\Api\Index as ApiController;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Serializer\Decimal;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

final class Confirm
{
    /**
     * @var \Aplazame\Payment\Gateway\Config\Config
     */
    private $aplazameConfig;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    private static function ok()
    {
        return ApiController::success(
            [
                'status' => 'ok',
            ]
        );
    }

    private static function ko($reason)
    {
        return ApiController::success(
            [
                'status' => 'ko',
                'reason' => $reason,
            ]
        );
    }

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Aplazame\Payment\Gateway\Config\Config $aplazameConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->aplazameConfig = $aplazameConfig;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function confirm(array $payload)
    {
        if (!isset($payload['sandbox']) || $payload['sandbox'] !== $this->aplazameConfig->isSandbox()) {
            return ApiController::client_error('"sandbox" not provided');
        }

        if (!isset($payload['mid'])) {
            return ApiController::client_error('"mid" not provided');
        }
        $checkoutToken = $payload['mid'];

        switch ($payload['status']) {
            case 'pending':
                switch ($payload['status_reason']) {
                    case 'challenge_required':
                        $order = $this->createOrder($checkoutToken);
                        /** @var \Magento\Sales\Model\Order\Payment $payment */
                        $payment = $order->getPayment();

                        if ($payment->getMethod() !== Aplazame::PAYMENT_METHOD_CODE) {
                            return self::ko('Aplazame is not the payment method (at challenge)');
                        }

                        if ($this->isFraud($payload, $order)) {
                            $payment->setIsFraudDetected(true);
                        }

                        $this->orderRepository->save($order);

                        if ($payment->getIsFraudDetected()) {
                            return self::ko('Fraud detected (at challenge)');
                        }
                        break;
                    case 'confirmation_required':
                        $order = $this->createOrder($checkoutToken);
                        /** @var \Magento\Sales\Model\Order\Payment $payment */
                        $payment = $order->getPayment();

                        if ($payment->getMethod() !== Aplazame::PAYMENT_METHOD_CODE) {
                            return self::ko('Aplazame is not the payment method (at confirmation)');
                        }

                        if ($this->isFraud($payload, $order)) {
                            $payment->setIsFraudDetected(true);
                        }

                        $payment->accept();
                        $this->orderRepository->save($order);

                        if ($payment->getIsFraudDetected()) {
                            return self::ko('Fraud detected (at confirmation)');
                        }
                        break;
                }
                break;
            case 'ko':
                try {
                    $order = $this->findOneOrderByQuote($checkoutToken);
                } catch (NoSuchEntityException $e) {
                    return self::ok();
                }

                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $payment = $order->getPayment();

                if ($payment->getMethod() !== Aplazame::PAYMENT_METHOD_CODE) {
                    return self::ko('Aplazame is not the payment method');
                }

                if ($this->isFraud($payload, $order)) {
                    $payment->setIsFraudDetected(true);
                }

                $payment->deny(true);
                $this->orderRepository->save($order);

                if ($payment->getIsFraudDetected()) {
                    return self::ko('Fraud detected');
                }
                break;
        }

        return self::ok();
    }

    /**
     * @param string $quoteId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws NoSuchEntityException
     */
    private function findOneOrderByQuote($quoteId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder->addFilter(OrderInterface::QUOTE_ID, $quoteId, 'eq')->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        if (empty($orders)) {
            throw new NoSuchEntityException(__("Requested entity doesn't exist"));
        }

        return array_shift($orders);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createOrder($checkoutMid)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($checkoutMid);

        if (empty($quote->getCustomerId())) {
            $quote->setCheckoutMethod(\Magento\Quote\Api\CartManagementInterface::METHOD_GUEST);
        }

        $orderId = $this->quoteManagement->placeOrder($quote->getId());

        return $this->orderRepository->get($orderId);
    }

    private function isFraud(array $payload, \Magento\Sales\Api\Data\OrderInterface $order)
    {
        return ($payload['total_amount'] !== Decimal::fromFloat($order->getGrandTotal())->jsonSerialize()) ||
               ($payload['currency']['code'] !== $order->getOrderCurrencyCode());
    }
}
