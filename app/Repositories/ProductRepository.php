<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductRepository
{
    /**
     * Create a new repository instance.
     * @param Product $model
     */
    public function __construct(public Product $model) {}

    /**
     * Get products with lock
     * @param array $products
     * @return Collection
     */
    public function getProductsWithLock(array $products): Collection
    {
        $products = Product::query()->with(['ingredients' => function (BelongsToMany $query) {
            $query->lockForUpdate();
        }])->lockForUpdate()->whereIn('id', array_column($products, 'product_id'))->get();

        return $products;
    }

    /**
     * Get product by id
     * @param int $id
     * @return Product
     */
    public function getProductById(int $id): Product
    {
        return Product::query()->with('ingredients')->find($id);
    }
}
