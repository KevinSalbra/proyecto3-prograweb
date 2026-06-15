<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        return view('cart.index', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'total' => $cart->subtotal(),
        ]);
    }

    public function store(Request $request, Product $product, CartService $cart): RedirectResponse
    {
        if ($product->stock < 1) {
            return back()->with('warning', 'Este producto está agotado temporalmente.');
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.$product->stock],
        ]);

        $cart->add($product, (int) ($validated['quantity'] ?? 1));

        return back()
            ->with('success', "{$product->name} se agregó al carrito.");
    }

    public function update(Request $request, CartService $cart): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.quantity' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $quantities = collect($validated['items'])
            ->mapWithKeys(fn (array $item, string $productId) => [$productId => $item['quantity']]);

        $cart->update($quantities->all());

        if ($request->expectsJson()) {
            $items = $cart->items();

            return response()->json([
                'subtotal' => $cart->subtotal(),
                'total' => $cart->subtotal(),
                'count' => $cart->count(),
                'items' => $items->map(fn (array $item) => [
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ])->values(),
            ]);
        }

        return back()->with('success', 'El carrito se actualizó correctamente.');
    }

    public function destroy(Product $product, CartService $cart): RedirectResponse
    {
        $cart->remove($product);

        return back()->with('success', "{$product->name} se eliminó del carrito.");
    }
}
