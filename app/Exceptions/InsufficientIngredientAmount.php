<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;


class InsufficientIngredientAmount extends Exception
{
    public function __construct(private Collection $inSufficientProducts) {}

    public function render()
    {
        $errors = $this->inSufficientProducts->map(function ($product) {
            return $product->map(function ($ingredient) {
                return "Ingredient {$ingredient['ingredient']} requires more amount which is " . abs($ingredient['diff']) . " GM for {$ingredient['quantity']} {$ingredient['product']}";
            });
        })->all();

        return response()->json([
            'success' => false,
            'message' => 'Insufficient ingredients amount to complete the order',
            'errors'  => $errors,
        ], Response::HTTP_OK);
    }
}
