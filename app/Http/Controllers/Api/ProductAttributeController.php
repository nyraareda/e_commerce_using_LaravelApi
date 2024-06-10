<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Http\Resources\ProductAttributeResource;
use App\Trait\ApiResponse;

class ProductAttributeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $attributes = ProductAttribute::with('product')->get();
        return ProductAttributeResource::collection($attributes);
    }

    public function show($id)
    {
        $attribute = ProductAttribute::find($id);

        if (!$attribute) {
            return $this->errorResponse('Attribute not found', 404);
        }

        $attribute->load('product');
        return new ProductAttributeResource($attribute);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'attribute_name' => 'required|string|max:255',
            'attribute_value' => 'required|string|max:255',
        ]);

        $attribute = ProductAttribute::create([
            'product_id' => $request->product_id,
            'attribute_name' => $request->attribute_name,
            'attribute_value' => $request->attribute_value,
        ]);

        return $this->successResponse(new ProductAttributeResource($attribute), 'Attribute added successfully');
    }

    public function update(Request $request, $id)
    {
        $attribute = ProductAttribute::find($id);

        if (!$attribute) {
            return $this->errorResponse('Attribute not found', 404);
        }

        $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'attribute_name' => 'sometimes|required|string|max:255',
            'attribute_value' => 'sometimes|required|string|max:255',
        ]);

        if ($request->has('product_id')) {
            $attribute->product_id = $request->product_id;
        }
        if ($request->has('attribute_name')) {
            $attribute->attribute_name = $request->attribute_name;
        }
        if ($request->has('attribute_value')) {
            $attribute->attribute_value = $request->attribute_value;
        }

        $attribute->save();

        return $this->successResponse(new ProductAttributeResource($attribute), 'Attribute updated successfully');
    }

    public function destroy($id)
    {
        $attribute = ProductAttribute::find($id);

        if (!$attribute) {
            return $this->errorResponse('Attribute not found', 404);
        }

        $attribute->delete();

        return $this->successResponse(null, 'Attribute deleted successfully');
    }

    //Search about the productname
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return $this->errorResponse('Query parameter is required', 400);
        }

        $products = Product::where('title', 'LIKE', '%' . $query . '%')
            ->with(['category','attributes'])
            ->get();

        return ProductWithCategoryResource::collection($products);
    }
}
