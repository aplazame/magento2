<?php

namespace Aplazame\Payment\Model\Api\Controller;

use Aplazame\Payment\Controller\Api\Index as ApiController;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Serializer\Decimal;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Sales\Api\Data\OrderInterface;

class Confirm
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

    private static function ok(array $extra = null)
    {
        $response = array(
            'status' => 'ok',
        );

        if ($extra) {
            $response += $extra;
        }

        return ApiController::success($response);
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
    )
    {
        $this->orderRepository = $orderRepository;
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->aplazameConfig = $aplazameConfig;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function confirm(array $payload,HttpRequest $request)
    {
        if (!isset($payload['sandbox']) || $payload['sandbox'] !== $this->aplazameConfig->isSandbox()) {
            return ApiController::client_error('"sandbox" not provided');
        }

        if (!isset($payload['mid'])) {
            return ApiController::client_error('"mid" not provided');
        }

        $quote_param = $request->getParam('quote_id');
        if ($quote_param != null) {
            $isQuoteIdQueryParamSet = true;
            $checkoutToken = $quote_param;
        } else {
            $isQuoteIdQueryParamSet = false;
            $checkoutToken = $payload['mid'];
        }

        switch ($payload['status']) {
            case 'pending':
                try {
                    $order = $this->findOneOrderByQuote($checkoutToken);
                } catch (NoSuchEntityException $e) {
                    $order = $this->createOrder($checkoutToken);
                }

                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $payment = $order->getPayment();

                if ($payment->getMethod() !== Aplazame::PAYMENT_METHOD_CODE) {
                    return self::ko('Aplazame is not the payment method');
                }

                if ($this->isFraud($payload, $order)) {
                    $payment->setIsFraudDetected(true);
                }

                $this->orderRepository->save($order);

                if ($payment->getIsFraudDetected()) {
                    return self::ko('Fraud detected');
                }

                if ($this->aplazameConfig->isChangeToOrderIdEnabled()) {
                    return self::ok($this->buildMid($isQuoteIdQueryParamSet, $order));
                }
                break;

            case 'ok':
                try {
                    $order = $this->findOneOrderByQuote($checkoutToken);
                } catch (NoSuchEntityException $e) {
                    $order = $this->createOrder($checkoutToken);
                }

                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $payment = $order->getPayment();
                $payment->accept();
                $this->orderRepository->save($order);

                if ($this->aplazameConfig->isChangeToOrderIdEnabled()) {
                    return self::ok($this->buildMid($isQuoteIdQueryParamSet, $order));
                }
                break;

            case 'ko':
                try {
                    $order = $this->findOneOrderByQuote($checkoutToken);
                } catch (NoSuchEntityException $e) {
                    $this->deleteCart($checkoutToken);
                    return self::ok();
                }

                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $payment = $order->getPayment();

                if ($payment->getMethod() !== Aplazame::PAYMENT_METHOD_CODE) {
                    return self::ko('Aplazame is not the payment method');
                }

                $payment->deny(true);
                $this->orderRepository->save($order);
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

    /**
     * @throws NoSuchEntityException
     */
    private function deleteCart($checkoutMid)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($checkoutMid);
        $this->quoteRepository->delete($quote);
    }

    private function buildMid($isCartIdQueryParamSet, $order)
    {
        if (!$isCartIdQueryParamSet) {
            return null;
        }

        return array('order_id' => "order_" . $order->getRealOrderId());
    }
}
