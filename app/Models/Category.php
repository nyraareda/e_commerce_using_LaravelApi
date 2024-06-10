<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description'];

    public function productsCategory()
    {
        return $this->hasMany(ProductsCategory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_category', 'category_id', 'product_id');
    }
    
}
