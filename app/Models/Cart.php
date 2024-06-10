<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    /**
     * Define the relationship with products in the cart.
     */
    public function product()
    {
        return $this->belongsToMany(Product::class, 'cart_products')->withPivot('quantity')->withTimestamps();
    }

    
}
