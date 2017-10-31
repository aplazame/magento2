<?php

namespace Aplazame\Payment\Model\Api\Controller;

use Aplazame\Payment\Controller\Api\Index as ApiController;
use Magento\Framework\Exception\NoSuchEntityException;

final class Order
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function history(array $params, array $queryArguments)
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

        $page = (isset($queryArguments['page'])) ? $queryArguments['page'] : 1;
        $page_size = (isset($queryArguments['page_size'])) ? $queryArguments['page_size'] : 10;

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('customer_id', $quote->getCustomer()->getId(), 'eq')
            ->setCurrentPage($page)
            ->setPageSize($page_size)
            ->create()
        ;

        /** @var \Magento\Quote\Model\Quote[] $quotes */
        $quotes = $this->quoteRepository->getList($searchCriteria)->getItems();

        $historyOrders = array_map(['Aplazame\Payment\Model\Api\BusinessModel\HistoricalOrder', 'createFromQuotes'], $quotes);

        return ApiController::collection($page, $page_size, $historyOrders);
    }
}
