<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    use FormatsApiResponses;

    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => $this->formatCategory($category))
            ->values();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function byType(Request $request, string $type): JsonResponse
    {
        $mappedType = match ($type) {
            'vino' => 'Vino',
            'champan', 'champagne' => 'Champán',
            default => abort(404),
        };

        $title = $mappedType === 'Vino'
            ? 'Catálogo de vinos'
            : 'Catálogo de champanes';

        return $this->listByType($request, $mappedType, $title);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        $search = trim($request->get('buscar', ''));
        $sort = $request->get('orden', 'recientes');

        $products = Product::with('category')
            ->where('category_id', $category->id)
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
            });

        $products = $this->applySorting($products, $sort)
            ->paginate(8)
            ->withQueryString();

        $categories = Category::withCount('products')
            ->where('type', $category->type)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $item) => $this->formatCategory($item))
            ->values();

        return response()->json([
            'category' => $this->formatCategory($category),
            'title' => $category->name,
            'search' => $search,
            'sort' => $sort,
            'categories' => $categories,
            'products' => $this->formatProductPaginator($products),
        ]);
    }

    private function listByType(Request $request, string $type, string $title): JsonResponse
    {
        $search = trim($request->get('buscar', ''));
        $sort = $request->get('orden', 'recientes');
        $categoryId = $request->get('categoria');

        $categories = Category::withCount('products')
            ->where('type', $type)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => $this->formatCategory($category))
            ->values();

        $products = Product::with('category')
            ->whereHas('category', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
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
            });

        $products = $this->applySorting($products, $sort)
            ->paginate(8)
            ->withQueryString();

        return response()->json([
            'category' => null,
            'title' => $title,
            'search' => $search,
            'sort' => $sort,
            'categoryId' => $categoryId,
            'categories' => $categories,
            'products' => $this->formatProductPaginator($products),
        ]);
    }

    private function applySorting($query, string $sort)
    {
        return match ($sort) {
            'precio_asc' => $query->orderBy('price', 'asc'),
            'precio_desc' => $query->orderBy('price', 'desc'),
            'nombre' => $query->orderBy('name', 'asc'),
            'anio_desc' => $query->orderBy('vintage_year', 'desc'),
            default => $query->latest(),
        };
    }
}