<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\ProductsCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Trait\ApiResponse;
use App\Http\Requests\CreateCategoryRequest;
use Illuminate\Support\Facades\Validator;
class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
{
    $categories = Category::with('products')->get();
    return CategoryResource::collection($categories);
}

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        $category->load('products'); 

        return $this->formatCategoryResponse($category);

    }

    public function store(CreateCategoryRequest $request)
{
    
    $category = new Category;
    $category->name = $request->name;
    $category->description = $request->description;

    $category->save();

    if ($request->product_ids) {
        foreach ($request->product_ids as $product_id) {
            // if (ProductsCategory::where('product_id', $product_id)->exists()) {
            //     return $this->errorResponse("Product ID $product_id is already assigned to another category.", 422);
            // }
            ProductsCategory::create([
                'product_id' => $product_id,
                'category_id' => $category->id,
            ]);
        }
    }
        $category->load('products');

        return $this->formatCategoryResponse($category, "Category added successfully");
}

public function update($id, CreateCategoryRequest $request)
{
    $category = Category::find($id);

    if (!$category) {
        return $this->errorResponse('Category not found', 404);
    }

    if ($request->has('name')) {
        $category->name = $request->name;
    }
    if ($request->has('description')) {
        $category->description = $request->description;
    }
    $category->save();

    if ($request->product_ids) {
        ProductsCategory::where('category_id', $category->id)->delete();
        foreach ($request->product_ids as $product_id) {
            // if (ProductsCategory::where('product_id', $product_id)->exists()) {
            //     return $this->errorResponse("Product ID $product_id is already assigned to another category.", 422);
            // }
            ProductsCategory::create([
                'product_id' => $product_id,
                'category_id' => $category->id,
            ]);
        }
    }

    $category->load('products');
    return $this->formatCategoryResponse($category, "Category updated successfully");
}


private function formatCategoryResponse($category, $message)
{
    $formattedProducts = $category->products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name, 
            'description' => $product->description,
            'price' => $product->price,
        ];
    });

    $responseData = [
        'id' => $category->id,
        'name' => $category->name,
        'description' => $category->description,
        'created_at' => $category->created_at,
        'updated_at' => $category->updated_at,
        'products' => $formattedProducts,
    ];

    return $this->successResponse($responseData, $message);
}
    public function destroy(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }

        ProductCategory::where('category_id', $category->id)->delete();

        $category->delete();

        return $this->successResponse($category, 'Category deleted successfully');
    }
}
