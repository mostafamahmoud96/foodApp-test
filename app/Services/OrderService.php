<?php

namespace App\Services;

use App\DTOs\CreateOrderDto;
use Illuminate\Support\Facades\DB;
use App\Repositories\OrderRepository;
use App\Exceptions\InsufficientIngredientAmount;

class OrderService
{
    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     * @param ProductService $productService
     * @param IngredientService $ingredientService
     */
    public function __construct(public OrderRepository $orderRepository, public ProductService $productService, public IngredientService $ingredientService) {}

    /**
     * Create order
     * @param CreateOrderDto $createOrderDto
     * @return Order $order
     */
    public function createOrder(CreateOrderDto $createOrderDto)
    {
        return DB::transaction(function ()  use ($createOrderDto) {

            $products = $this->productService->getProductsWithLock($createOrderDto->products);

            $inSufficientProducts = collect();
            foreach ($products as $key => $product) {
                $inSufficientIngredients = app(IngredientService::class)->checkInSufficentStock($product->id, $createOrderDto->products[$key]['quantity']);
                if ($inSufficientIngredients->isNotEmpty()) {
                    $inSufficientProducts->push($inSufficientIngredients);
                }
            }

            if ($inSufficientProducts->isNotEmpty()) {
                throw new InsufficientIngredientAmount($inSufficientProducts);
            }

            $order = $this->storeOrderWithProducts($createOrderDto->products);

            foreach ($products as $key => $product) {
                $this->ingredientService->updateStock($product->id, $createOrderDto->products[$key]['quantity']); //TODO move to ingredient service
            }

            return $order;
        });
    }


    /**
     * Store order with products
     *  @param array $products
     * @return Order $order
     */
    public function storeOrderWithProducts(array $products)
    {
        return $this->orderRepository->storeOrderWithProducts($products);
    }
}
