<?php

namespace App\Repositories;

use App\Models\Order;


class OrderRepository
{
    /**
     * @var model
     */
    protected $model;


    public function __construct(Order $model)
    {
        $this->model = $model;
    }



    /**
     * Store order with products
     *  @param array $products
     * @return Order $order
     */
    public function storeOrderWithProducts(array $products): Order
    {

        $dataToSync = [];
        foreach ($products as $product) {
            $dataToSync[$product['product_id']] = ['quantity' => $product['quantity']];
        }
        
        $order = Order::query()->create();
        $order->products()->sync($dataToSync);
        return $order;
    }
}
