<?php

namespace Aplazame\Payment\Block\Adminhtml\Product\Edit;

use Aplazame\Payment\Model\Api\BusinessModel\Article;

class Aplazame extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->registry->registry('product'));
        }
        return $this->getData('product');
    }

    public function getArticles()
    {
        $product = $this->getProduct();

        $articles = [
            Article::createFromProduct($product)
        ];

        return $articles;
    }
}
