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

        if (!empty($product->getData('image'))) {
            $objectManager = ObjectManager::getInstance();
            /** @var \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder */
            $imageBuilder = $objectManager->get('Magento\Catalog\Block\Product\ImageBuilder');
            $image = $imageBuilder->setProduct($product)
                ->setImageId('category_page_list')
                ->create()
            ;

            $article['image_url'] = $image->getImageUrl();
        }

        return $article;
    }
}
