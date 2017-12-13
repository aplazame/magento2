<?php

namespace Aplazame\Payment\Model\Api\BusinessModel;

use Magento\Catalog\Model\Product;
use Aplazame\Payment\Model\BusinessModel\Article as BMArticle;

class Article
{
    /**
     * @return array
     */
    public static function createFromProduct(Product $product)
    {
        $article = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => substr($product->getDescription(), 0, 255),
            'url' => $product->getProductUrl(),
            'image_url' => BMArticle::getImageProduct($product),
        ];

        return $article;
    }
}
