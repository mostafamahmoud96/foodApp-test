<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stock',
        'level'
    ];

    /**
     * The products that belong to the ingredient.
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ingredient_product')->withPivot('quantity')->withTimestamps();
    }

    /**
     * The alerts that belong to the ingredient.
     * @return BelongsToMany
     */
    public function alerted(): HasOne
    {
        return $this->hasOne(IngredientAlert::class, 'ingredient_id', 'id');
    }
}
