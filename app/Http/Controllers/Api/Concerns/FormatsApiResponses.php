<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Category;
use App\Models\Product;

trait FormatsApiResponses
{
    protected function formatCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'type' => $category->type,
            'description' => $category->description,
            'image' => $category->image,
            'image_url' => $category->image
                ? asset('images/categorias/' . $category->image)
                : asset('images/categorias/sin-imagen.jpg'),
            'products_count' => $category->products_count ?? null,
        ];
    }

    protected function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'producer' => $product->producer,
            'description' => $product->description,
            'image' => $product->image,
            'image_url' => $product->image
                ? asset('images/productos/' . $product->image)
                : asset('images/productos/sin-imagen.jpg'),
            'price' => (float) $product->price,
            'wine_type' => $product->wine_type,
            'grape' => $product->grape,
            'country' => $product->country,
            'region' => $product->region,
            'appellation' => $product->appellation,
            'vintage_year' => $product->vintage_year,
            'volume_ml' => $product->volume_ml,
            'alcohol_percentage' => (float) $product->alcohol_percentage,
            'stock' => $product->stock,
            'condition' => $product->condition,
            'rating_source' => $product->rating_source,
            'rating_score' => $product->rating_score !== null ? (float) $product->rating_score : null,
            'is_featured' => (bool) $product->is_featured,
            'category' => $product->relationLoaded('category') && $product->category
                ? $this->formatCategory($product->category)
                : null,
        ];
    }

    protected function formatProductPaginator($paginator): array
    {
        return [
            'data' => $paginator->getCollection()
                ->map(fn (Product $product) => $this->formatProduct($product))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}