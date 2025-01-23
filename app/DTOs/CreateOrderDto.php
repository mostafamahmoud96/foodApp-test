<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateOrderDto extends Data
{
    /**
     * @param array $products
     */
    public function __construct(
        public  array $products,
    ) {}
}
