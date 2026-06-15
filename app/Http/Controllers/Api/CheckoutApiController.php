<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutApiController extends Controller
{
    public function index(CartService $cart): JsonResponse
    {
        if ($cart->isEmpty()) {
            return response()->json([
                'message' => 'Su carrito está vacío.',
                'items' => [],
                'subtotal' => 0,
                'total' => 0,
            ], 422);
        }

        return response()->json([
            'items' => $cart->items()->values(),
            'subtotal' => $cart->subtotal(),
            'total' => $cart->subtotal(),
        ]);
    }

    public function store(Request $request, CartService $cart): JsonResponse
    {
        if ($cart->isEmpty()) {
            return response()->json([
                'message' => 'Su carrito está vacío. Agregue productos antes de finalizar la compra.',
            ], 422);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'in:Tarjeta de crédito,Tarjeta de débito,Transferencia bancaria'],
            'card_number' => ['required', 'digits_between:13,19'],
            'expiry_date' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv' => ['required', 'digits_between:3,4'],
        ]);

        $items = $cart->items()->map(function (array $item) {
            return [
                'name' => $item['product']->name,
                'image' => $item['product']->image,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
            ];
        })->all();

        $order = [
            'confirmation' => 'SA-' . strtoupper(Str::random(8)),
            'customer' => $validated,
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'total' => $cart->subtotal(),
            'placed_at' => now()->format('d/m/Y H:i'),
        ];

        session()->put('checkout_order', $order);
        $cart->clear();

        return response()->json([
            'message' => 'Su compra fue registrada correctamente.',
            'order' => $order,
        ]);
    }

    public function success(): JsonResponse
    {
        $order = session('checkout_order');

        if (! $order) {
            return response()->json([
                'message' => 'No hay una orden registrada.',
                'order' => null,
            ], 404);
        }

        return response()->json([
            'order' => $order,
        ]);
    }
}