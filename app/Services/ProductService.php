<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(public ProductRepository $productRepository) {}

    /**
     * Get product by id
     * @param int $id
     * @return mixed
     */
    public function getProductById(int $id): mixed
    {
        return $this->productRepository->getProductById($id);
    }

    /**
     * Get products with lock
     * @param array $products
     * @return mixed
     */
    public function getProductsWithLock(array $products): mixed
    {
        return $this->productRepository->getProductsWithLock($products);
    }
}
