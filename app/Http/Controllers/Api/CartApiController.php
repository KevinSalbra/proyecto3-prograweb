<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    use FormatsApiResponses;

    public function index(CartService $cart): JsonResponse
    {
        return response()->json($this->formatCart($cart));
    }

    public function store(Request $request, Product $product, CartService $cart): JsonResponse
    {
        if ($product->stock < 1) {
            return response()->json([
                'message' => 'Este producto está agotado temporalmente.',
            ], 422);
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:' . $product->stock],
        ]);

        $cart->add($product, (int) ($validated['quantity'] ?? 1));

        return response()->json([
            'message' => "{$product->name} se agregó al carrito.",
            'cart' => $this->formatCart($cart),
        ]);
    }

    public function update(Request $request, CartService $cart): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.quantity' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $quantities = collect($validated['items'])
            ->mapWithKeys(function (array $item, string $productId) {
                return [$productId => $item['quantity']];
            })
            ->all();

        $cart->update($quantities);

        return response()->json([
            'message' => 'El carrito se actualizó correctamente.',
            'cart' => $this->formatCart($cart),
        ]);
    }

    public function destroy(Product $product, CartService $cart): JsonResponse
    {
        $cart->remove($product);

        return response()->json([
            'message' => "{$product->name} se eliminó del carrito.",
            'cart' => $this->formatCart($cart),
        ]);
    }

    public function clear(CartService $cart): JsonResponse
    {
        $cart->clear();

        return response()->json([
            'message' => 'El carrito fue vaciado correctamente.',
            'cart' => $this->formatCart($cart),
        ]);
    }

    private function formatCart(CartService $cart): array
    {
        $items = $cart->items()->map(function (array $item) {
            return [
                'product' => $this->formatProduct($item['product']),
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
            ];
        })->values();

        return [
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'total' => $cart->subtotal(),
            'count' => $cart->count(),
        ];
    }
}