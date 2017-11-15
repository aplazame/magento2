<?php

namespace Aplazame\Payment\Model\Api\Controller;

use Aplazame\Payment\Controller\Api\Index as ApiController;
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
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    private $transactionBuilder;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    private static function ok()
    {
        return [
            'status_code' => 200,
            'payload' => [
                'status' => 'ok',
            ],
        ];
    }

    private static function ko()
    {
        return [
            'status_code' => 200,
            'payload' => [
                'status' => 'ko',
            ],
        ];
    }

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Aplazame\Payment\Gateway\Config\Config $aplazameConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->aplazameConfig = $aplazameConfig;
        $this->transactionBuilder = $transactionBuilder;
    }

    public function confirm($payload)
    {
        if (!$payload) {
            return ApiController::client_error('Payload is malformed');
        }

        if (!isset($payload['sandbox']) || $payload['sandbox'] !== $this->aplazameConfig->isSandbox()) {
            return ApiController::client_error('"sandbox" not provided');
        }

        if (!isset($payload['mid'])) {
            return ApiController::client_error('"mid" not provided');
        }
        $checkoutToken = $payload['mid'];

        try {
            $order = $this->findOneOrderByQuote($checkoutToken);
        } catch (NoSuchEntityException $e) {
            return ApiController::not_found();
        }

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();
        $amount = $order->getGrandTotal();

        if ($payload['total_amount'] !== Decimal::fromFloat($amount)->jsonSerialize() ||
            $payload['currency']['code'] !== $order->getOrderCurrencyCode()
        ) {
            $payment->setIsFraudDetected(true);
        }

        switch ($payload['status']) {
            case 'ok':
                $payment->accept();
                $this->orderRepository->save($order);

                if ($payment->getIsFraudDetected()) {
                    return self::ko();
                }

                return self::ok();
            case 'ko':
                $payment->deny(true);
                $this->orderRepository->save($order);

                if ($payment->getIsFraudDetected()) {
                    return self::ko();
                }

                return self::ok();
            default:
                return ApiController::client_error('Unknown "status" ' . $payload['status']);
        }
    }

    /**
     * @param string $quoteId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws NoSuchEntityException
     */
    private function findOneOrderByQuote($quoteId)
    {
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder->addFilter(OrderInterface::QUOTE_ID, $quoteId, 'eq')->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        if (empty($orders)) {
            throw new NoSuchEntityException(__("Requested entity doesn't exist"));
        }

        return array_shift($orders);
    }
}
