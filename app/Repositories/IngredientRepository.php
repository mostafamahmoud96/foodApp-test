<?php

namespace App\Repositories;

use App\Models\Ingredient;


class IngredientRepository
{
    public function __construct(public Ingredient $model) {}
}
