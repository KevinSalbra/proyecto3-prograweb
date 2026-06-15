<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $search = trim($request->get('buscar', ''));
        $categoryId = $request->get('categoria');
        $sort = $request->get('orden', 'recientes');

        $categories = Category::withCount('products')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => $this->formatCategory($category))
            ->values();

        $products = Product::with('category')
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
            'search' => $search,
            'categoryId' => $categoryId,
            'sort' => $sort,
            'categories' => $categories,
            'products' => $this->formatProductPaginator($products),
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadProductImage($request, $data['name']);
        } else {
            $data['image'] = null;
        }

        $product = Product::create($data);
        $product->load('category');

        return response()->json([
            'message' => 'El producto fue creado correctamente.',
            'product' => $this->formatProduct($product),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get()
            ->map(fn (Product $item) => $this->formatProduct($item))
            ->values();

        return response()->json([
            'product' => $this->formatProduct($product),
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        if ($product->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadProductImage($request, $data['name']);
        } else {
            unset($data['image']);
        }

        $product->update($data);
        $product->load('category');

        return response()->json([
            'message' => 'El producto fue actualizado correctamente.',
            'product' => $this->formatProduct($product),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'El producto fue eliminado correctamente.',
        ]);
    }

    private function uploadProductImage(ProductRequest $request, string $productName): string
    {
        $file = $request->file('image');
        $directory = public_path('images/productos');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = Str::slug($productName)
            . '-'
            . uniqid()
            . '.'
            . $file->getClientOriginalExtension();

        $file->move($directory, $filename);

        return $filename;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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