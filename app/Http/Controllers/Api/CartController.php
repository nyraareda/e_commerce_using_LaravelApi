<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Trait\ApiResponse;


class CartController extends Controller
{
    use ApiResponse; // Use the ApiResponse trait

    
    public function store(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            $quantityInCart = $request->input('quantity', 1);

            if ($product->quantity < $quantityInCart) {
                return $this->errorResponse('Insufficient stock', 400);
            }
            
            \DB::beginTransaction();

            $cart = new Cart();
            $cart->user_id = auth()->id();
            $cart->product_id = $productId;
            $cart->quantity = $quantityInCart;
            $cart->save();

            // Update product quantity
            $product->quantity -= $quantityInCart;
            $product->save();

            \DB::commit();

            return $this->successResponse('Product added to cart successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            \DB::rollback();

            \Log::error('Failed to add product to cart: ' . $e->getMessage());

            return $this->errorResponse('Failed to add product to cart', 500);
        }
    }

    // Method to handle unauthorized access
    public function unauthorized()
    {
        return $this->errorResponse('Not authorized', 401);
    }
}
