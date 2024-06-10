<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductWithCategoryResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductsCategory;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;



class ProductController extends Controller
{
    use ApiResponse;

    
    public function index(Request $request)
    {
        $query = Product::query();

    // Filtering by category
    if ($request->has('category_id')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->where('categories.id', $request->category_id);
        });
    }

    // Filtering by price range
    if ($request->has('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->has('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Searching by product name, if search query is provided
    if ($request->has('search')) {
        $searchQuery = $request->search;
        $query->where('name', 'like', "%$searchQuery%");
    }

    // Sorting
    if ($request->has('sort_by')) {
        switch ($request->sort_by) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'highest_price':
                $query->orderBy('price', 'desc');
                break;
            case 'lowest_price':
                $query->orderBy('price', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $products = $query->with('category', 'attributes')->get();

    return ProductWithCategoryResource::collection($products);
}

    public function show($id)
    {
        $product = Product::with('category','attributes')->find($id);

        if (! $product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(new ProductWithCategoryResource($product));
    }

    public function store(ProductRequest $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;

        if ($request->hasFile('image')) {
            $originalFilename = $request->image->getClientOriginalName();
            $request->image->move(public_path('images'), $originalFilename);
            $product->image = $originalFilename;
        } else {
            $product->image = 'default.jpg';
        }

        $product->save();

        // Associate product with a category
        if ($request->category_id) {
            ProductsCategory::create([
                'product_id' => $product->id,
                'category_id' => $request->category_id,
            ]);
        }

        if ($request->attributes) {
            foreach ($request->attributes as $attribute) {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'attribute_name' => $attribute['name'],
                    'attribute_value' => $attribute['value'],
                ]);
            }
        }

        return $this->successResponse(new ProductWithCategoryResource($product), 'Product added successfully');
    }


    public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'description' => 'sometimes|nullable|string',
        'price' => 'sometimes|required|numeric',
        'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'category_ids' => 'array',
        'attributes' => 'array',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $product->fill($validator->validated());

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $product->image = basename($imagePath);
    }

    $product->save();

    if ($request->category_ids) {
        $product->categories()->sync($request->category_ids);
    }

    if ($request->attributes) {
        $product->attributes()->delete();
        foreach ($request->attributes as $attribute) {
            ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_name' => $attribute['name'],
                'attribute_value' => $attribute['value'],
            ]);
        }
    }

    return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
}

    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);

        if (! $product) {
            return $this->errorResponse('Product not found', 404);
        }
        $imageName = $product->image;
        $imagePath = public_path('images').'/'.$imageName;

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Remove associated attribute
        ProductAttribute::where('product_id', $product->id)->delete();

        $product->delete();

        return $this->successResponse(new ProductWithCategoryResource($product), 'Product deleted successfully');
    }

    //Search about the productTitle
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
