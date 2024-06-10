<?php

namespace App\Models;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image','quantity'];

    public function productsCategory()
    {
        return $this->hasOne(ProductsCategory::class);
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'products_category', 'product_id', 'category_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'products_category');
    }
    


}
