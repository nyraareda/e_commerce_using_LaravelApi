<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsCategory extends Model
{
    protected $table = 'products_category';
    protected $fillable = ['product_id', 'category_id'];

    public $timestamps = false;


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productsCategory()
    {
        return $this->hasOne(ProductsCategory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
