<?php

namespace Aplazame\Payment\Model\Api\Controller;

use Aplazame\Payment\Controller\Api\Index as ApiController;
use Aplazame\Payment\Model\Api\BusinessModel\HistoricalOrder;
use Magento\Framework\Exception\NoSuchEntityException;

class Order
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function history(array $params)
    {
        if (!isset($params['order_id'])) {
            return ApiController::not_found();
        }
        $mid = $params['order_id'];

        try {
            $quote = $this->quoteRepository->get($mid);
        } catch (NoSuchEntityException $e) {
            return ApiController::not_found();
        }

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('customer_id', $quote->getCustomer()->getId(), 'eq')
            ->create();

        /** @var \Magento\Sales\Model\Order[] $orders */
        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        $historyOrders = array();
        foreach ($orders as $order) {
            try {
                $quote = $this->quoteRepository->get($order->getQuoteId());
                $historyOrders[] = HistoricalOrder::createFromQuotes($quote, $order->getState());
            } catch (NoSuchEntityException $e) {
                // do nothing
            }
        }

        return ApiController::success($historyOrders);
    }
}
