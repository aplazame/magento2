<?php

namespace Aplazame\Payment\Controller\Payment;

use Aplazame\Payment\Model\Api\AplazameClient;
use Aplazame\Payment\Model\Aplazame;
use Aplazame\Serializer\Decimal;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\OrderFactory;

class Confirm extends Action
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var AplazameClient
     */
    private $aplazameClient;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        QuoteManagement $quoteManagement,
        AplazameClient $aplazameClient
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->aplazameClient = $aplazameClient;
        $this->quoteManagement = $quoteManagement;

        parent::__construct($context);
    }

    public function execute()
    {
        $checkoutToken = $this->getRequest()->getParam('order_id');
        if (!$checkoutToken) {
            throw new LocalizedException(__('Confirm has no checkout token.'));
        }

        $quote = $this->checkoutSession->getQuote();
        if ($quote->getId() !== $checkoutToken) {
            throw new LocalizedException(__('Invalid token or session has expired.'));
        }

        $this->quoteManagement->placeOrder($quote->getId());

        $order = $this->checkoutSession->getLastRealOrder();

        $this->invoice($order, $checkoutToken);
    }

    private function invoice(Order $order, $checkoutToken)
    {
        $invoice = $order->prepareInvoice();

        $invoice
            ->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::NOT_CAPTURE)
            ->setTransactionId($checkoutToken)
            ->register();

        $this->_objectManager->create('Magento\Framework\DB\Transaction')
             ->addObject($invoice)
             ->addObject($order)
             ->save();

        $order->addRelatedObject($invoice);
        $payment = $order->getPayment();
        $payment->setCreatedInvoice($invoice);

        return $invoice;
    }
}
