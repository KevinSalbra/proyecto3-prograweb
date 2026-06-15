<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeApiController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $search = trim($request->get('buscar', ''));

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', "%{$search}%")
                        ->orWhere('producer', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('grape', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('vintage_year', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $featuredProducts = Product::with('category')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get()
            ->map(fn (Product $product) => $this->formatProduct($product))
            ->values();

        $categories = Category::withCount('products')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => $this->formatCategory($category))
            ->values();

        return response()->json([
            'search' => $search,
            'products' => $this->formatProductPaginator($products),
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}