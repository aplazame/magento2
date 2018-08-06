<?php

namespace Aplazame\Payment\Controller\Payment;

use Magento\Framework\App\Action\Action;

class Success extends Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    private $objectManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);

        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute()
    {
        $quoteId = $this->checkoutSession->getQuoteId();

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('quote_id', $quoteId, 'eq')
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = array_shift($orders);
        if ($order) {
            $this->checkoutSession->setLastQuoteId($quoteId);
            $this->checkoutSession->setLastSuccessQuoteId($quoteId);
            $this->checkoutSession->setLastOrderId($order->getId());
            $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
            $this->checkoutSession->setLastOrderStatus($order->getStatus());
        }

        $this->_redirect('checkout/onepage/success');
    }
}
