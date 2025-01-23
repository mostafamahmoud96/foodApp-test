<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            [
                'name'  => 'Beef',
                'stock' => '20000',
                'level' => '20000',
            ],
            [
                'name'  => 'Cheese',
                'stock' => '5000',
                'level' => '5000',
            ],
            [
                'name'  => 'Onion',
                'stock' => '1000',
                'level' => '1000',
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate($ingredient);
        }
    }
}
