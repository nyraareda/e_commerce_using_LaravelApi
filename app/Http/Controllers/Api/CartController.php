<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Trait\ApiResponse;


class CartController extends Controller
{
    use ApiResponse; // Use the ApiResponse trait

    public function index()
{
    $carts = Cart::with('product')->get();
    return CartResource::collection($carts);
}

public function show($id)
{
    try {
        // Find the cart by its ID
        $cart = Cart::with('product')->findOrFail($id);

        // Return the cart with a success response
        return $this->successResponse(new CartResource($cart));
    } catch (\Exception $e) {
        \Log::error('Failed to retrieve cart: ' . $e->getMessage());
        return $this->errorResponse('Failed to retrieve cart', 500);
    }
}
    public function store(Request $request, $productId)
    {
        try {
            $user_id = auth()->id();
            $product = Product::findOrFail($productId);
            $quantityInCart = $request->input('quantity', 1);
            
            // Check if the product already exists in the user's cart
            $existingCart = Cart::where('user_id', $user_id)
                                ->where('product_id', $productId)
                                ->first();

            \DB::beginTransaction();

            if ($existingCart) {
                // If the product already exists in the cart, update the quantity
                $existingCart->quantity += $quantityInCart;
                $existingCart->save();
            } else {
                // If the product doesn't exist in the cart, create a new cart entry
                $cart = new Cart();
                $cart->user_id = $user_id;
                $cart->product_id = $productId;
                $cart->quantity = $quantityInCart;
                $cart->save();
            }

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

    public function update(Request $request, $id)
{
    try {
        $cart = Cart::findOrFail($id);
        $newQuantity = $request->input('quantity', 1);
        $oldQuantity = $cart->quantity;

        // Check if the new quantity is greater than the old quantity
        if ($newQuantity > $oldQuantity) {
            // Check if there is enough quantity in the product
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
            // Update cart quantity
            $cart->quantity = $newQuantity;
            $cart->save();

            // Update product quantity
            $cart->product->quantity += ($oldQuantity - $newQuantity);
            $cart->product->save();

            return $this->successResponse('Cart updated successfully');
        } else {
            // No change in quantity
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
        // Find the cart
        $cart = Cart::findOrFail($id);

        // Get the product associated with the cart
        $product = $cart->product;

        // Update product quantity
        $product->quantity += $cart->quantity;
        $product->save();

        // Delete the cart
        $cart->delete();

        return $this->successResponse('Cart deleted successfully');
    } catch (\Exception $e) {
        \Log::error('Failed to delete cart: ' . $e->getMessage());
        return $this->errorResponse('Failed to delete cart', 500);
    }
}

}
