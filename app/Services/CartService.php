<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    public function items(): Collection
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return collect();
        }

        $products = Product::with('category')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function (array $item, int|string $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                if ($product->stock < 1) {
                    return null;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $quantity = min($quantity, (int) $product->stock);
                $unitPrice = (float) $product->price;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function add(Product $product, int $quantity = 1): void
    {
        if ($product->stock < 1) {
            return;
        }

        $cart = session()->get('cart', []);
        $currentQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);
        $quantity = max(1, $quantity);
        $cart[$product->id] = [
            'quantity' => min($currentQuantity + $quantity, (int) $product->stock),
        ];

        session()->put('cart', $cart);
    }

    public function update(array $quantities): void
    {
        $cart = session()->get('cart', []);
        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        foreach ($quantities as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (! $product) {
                unset($cart[$productId]);
                continue;
            }

            $quantity = (int) $quantity;

            if ($quantity < 1 || $product->stock < 1) {
                unset($cart[$productId]);
                continue;
            }

            $cart[$productId] = [
                'quantity' => min($quantity, max(1, (int) $product->stock)),
            ];
        }

        session()->put('cart', $cart);
    }

    public function remove(Product $product): void
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }
}
