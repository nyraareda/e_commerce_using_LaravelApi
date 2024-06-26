<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Http\Resources\ProductAttributeResource;
use App\Trait\ApiResponse;
use App\Http\Requests\AttributeRequest;

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

    public function store(AttributeRequest $request)
    {
        $validated = $request->validated();

        $attribute = ProductAttribute::create($validated);

        return $this->successResponse(new ProductAttributeResource($attribute), 'Attribute added successfully');
    }

    public function update(AttributeRequest $request, $id)
    {
        $validated = $request->validated();
        $attribute = ProductAttribute::find($id);

        if (!$attribute) {
            return $this->errorResponse('Attribute not found', 404);
        }

        $attribute->fill($validated);
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

        return $this->successResponse($attribute, 'Attribute deleted successfully');
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
