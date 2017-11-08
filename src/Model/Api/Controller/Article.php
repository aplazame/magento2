<?php

namespace Aplazame\Payment\Model\Api\Controller;

use Aplazame\Payment\Controller\Api\Index as ApiController;

final class Article
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function articles(array $queryArguments)
    {
        $page = (isset($queryArguments['page'])) ? $queryArguments['page'] : 1;
        $page_size = (isset($queryArguments['page_size'])) ? $queryArguments['page_size'] : 10;

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
        $searchCriteria = $searchCriteriaBuilder
            ->setCurrentPage($page)
            ->setPageSize($page_size)
            ->create()
        ;

        /** @var \Magento\Catalog\Model\Product[] $products */
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        $articles = array_map(['Aplazame\Payment\Model\Api\BusinessModel\Article', 'createFromProduct'], $products);

        return ApiController::collection($page, $page_size, $articles);
    }
}
