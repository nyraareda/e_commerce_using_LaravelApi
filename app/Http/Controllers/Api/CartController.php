<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Trait\ApiResponse;
use App\Http\Requests\CartRequest;



class CartController extends Controller
{
    use ApiResponse;

    public function index()
{
    $carts = Cart::with('product')->get();
    return CartResource::collection($carts);
}

public function show($id)
{
    try {
        $cart = Cart::with('product')->findOrFail($id);

        return $this->successResponse(new CartResource($cart));
    } catch (\Exception $e) {
        \Log::error('Failed to retrieve cart: ' . $e->getMessage());
        return $this->errorResponse('Failed to retrieve cart', 500);
    }
}
    public function store(CartRequest $request, $productId)
    {
        try {
            $user_id = auth()->id();
            $product = Product::findOrFail($productId);
            $quantityInCart = $request->input('quantity', 1);
            
            $existingCart = Cart::where('user_id', $user_id)
                                ->where('product_id', $productId)
                                ->first();

            \DB::beginTransaction();

            if ($existingCart) {
                $existingCart->quantity += $quantityInCart;
                $existingCart->save();
            } else {
                $cart = new Cart();
                $cart->user_id = $user_id;
                $cart->product_id = $productId;
                $cart->quantity = $quantityInCart;
                $cart->save();
            }

            $product->quantity -= $quantityInCart;
            $product->save();

            \DB::commit();

            return $this->successResponse('Product added to cart successfully');
        } catch (\Exception $e) {
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

    public function update(CartRequest $request, $id)
{
    try {
        $cart = Cart::findOrFail($id);
        $newQuantity = $request->input('quantity', 1);
        $oldQuantity = $cart->quantity;

        if ($newQuantity > $oldQuantity) {
            $availableQuantity = $cart->product->quantity;
            if ($availableQuantity >= ($newQuantity - $oldQuantity)) {
                $cart->quantity = $newQuantity;
                $cart->save();

                // Update product quantity
                $cart->product->quantity -= ($newQuantity - $oldQuantity);
                $cart->product->save();

                return $this->successResponse('Cart updated successfully');
            } else {
                return $this->errorResponse('Not enough quantity available in stock', 400);
            }
        } elseif ($newQuantity < $oldQuantity) {
            $cart->quantity = $newQuantity;
            $cart->save();

            $cart->product->quantity += ($oldQuantity - $newQuantity);
            $cart->product->save();

            return $this->successResponse('Cart updated successfully');
        } else {
            return $this->successResponse('No changes made to cart');
        }
    } catch (\Exception $e) {
        \Log::error('Failed to update cart: ' . $e->getMessage());
        return $this->errorResponse('Failed to update cart', 500);
    }
}

public function destroy($id)
{
    try {
        $cart = Cart::findOrFail($id);

        $product = $cart->product;

        $product->quantity += $cart->quantity;
        $product->save();

        $cart->delete();

        return $this->successResponse('Cart deleted successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to delete cart: ' . $e->getMessage());
        return $this->errorResponse('Failed to delete cart', 500);
    }
}

}
