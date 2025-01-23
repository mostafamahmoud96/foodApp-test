<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class ProductIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productBurger = Product::query()->where('name', 'Burger')->first();
        $productBurgerIngredients = [
            Ingredient::query()->where('name', 'Beef')->value('id')   => ['amount' => '150'],
            Ingredient::query()->where('name', 'Cheese')->value('id') => ['amount' => '30'],
            Ingredient::query()->where('name', 'Onion')->value('id')  => ['amount' => '20'],
        ];

        $productBurger->ingredients()->sync($productBurgerIngredients);
    }
}
