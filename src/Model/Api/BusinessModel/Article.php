<?php

namespace Aplazame\Payment\Model\Api\BusinessModel;

use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product;

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
        ];

        $imagePath = $product->getImage();
        if (!empty($imagePath)) {
            $article['image_url'] = $product->getMediaConfig()->getMediaUrl($imagePath);
        }

        return $article;
    }
}
