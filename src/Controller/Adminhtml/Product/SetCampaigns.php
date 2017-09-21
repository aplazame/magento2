<?php

namespace Aplazame\Payment\Controller\Adminhtml\Product;

use Aplazame\Payment\Model\Api\BusinessModel\Article;

class SetCampaigns extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $productBuilder);

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Catalog\Model\Product[] $products */
        $products = $this->filter->getCollection($this->collectionFactory->create())->getItems();

        $articles = [];
        foreach ($products as $product) {
            $product->load($product->getId());
            $articles[] = Article::createFromProduct($product);
        }

        $resultPage = $this->resultPageFactory->create();
        $contentBlock = $resultPage->getLayout()->getBlock('aplazame');
        $contentBlock->setAttribute('articles', $articles);

        return $resultPage;
    }
}
