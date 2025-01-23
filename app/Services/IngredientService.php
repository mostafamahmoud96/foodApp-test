<?php

namespace App\Services;

use App\Mail\IngredientAlertMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Repositories\IngredientRepository;


class IngredientService
{

    public function __construct(public IngredientRepository $ingredientRepository, public ProductService $productService, public AdminService $adminService) {}

    /**
     * Check if there is enough stock for the product
     * @param int $productId
     * @param int $quantity
     * @return Collection
     */
    public function checkInSufficentStock($productId, $quantity): Collection
    {
        $inSufficientIngredients = collect();
        $product = $this->productService->getProductById($productId);

        foreach ($product->ingredients as $ingredient) {
            $desiredStock = $ingredient->pivot->amount * $quantity;
            $level = $ingredient->level;
            $difference = $level - $desiredStock;

            if ($difference < 0) {
                $inSufficientIngredients->push([
                    'ingredient' => $ingredient->name,
                    'diff'       => $difference,
                    'quantity'   => $quantity,
                    'product'    => $product->name,
                ]);
            }
        }

        return $inSufficientIngredients;
    }

    /**
     * Update stock for the product
     * @param int $productId
     * @param int $quantity
     */
    public function updateStock(int $productId, int $quantity): void
    {
        $alertedIngredients = collect();
        $product = $this->productService->getProductById($productId);

        foreach ($product->ingredients as $ingredient) {
            $levelStock = $ingredient->level - ($ingredient->pivot->amount * $quantity);
            $ingredient->level = $levelStock;
            $ingredient->save();

            if (($ingredient->level / $ingredient->stock * 100) < 50 && ! $ingredient->alerted) {
                $alertedIngredients->push($ingredient);
            }
        }

        $adminEmails = $this->adminService->getAdminsEmails();

        if ($alertedIngredients->isNotEmpty()) {
            Mail::to($adminEmails)
                ->queue(new IngredientAlertMail($alertedIngredients));
        }
    }
}
