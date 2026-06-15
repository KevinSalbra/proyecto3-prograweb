<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(CartService $cart): RedirectResponse|View
    {
        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Su carrito está vacío. Agregue productos antes de continuar.');
        }

        return view('checkout.index', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'total' => $cart->subtotal(),
        ]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Su carrito está vacío. Agregue productos antes de finalizar la compra.');
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
        ], [
            'full_name.required' => 'Escriba el nombre completo para continuar.',
            'email.required' => 'Indique un correo electrónico válido.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'phone.required' => 'Indique un teléfono de contacto.',
            'province.required' => 'Seleccione o escriba una provincia.',
            'address.required' => 'Indique la dirección exacta de entrega.',
            'payment_method.required' => 'Seleccione un método de pago.',
            'card_number.required' => 'Ingrese un número de tarjeta simulado.',
            'card_number.digits_between' => 'El número de tarjeta debe tener entre 13 y 19 dígitos.',
            'expiry_date.required' => 'Indique la fecha de expiración.',
            'expiry_date.regex' => 'La fecha debe tener el formato MM/AA.',
            'cvv.required' => 'Ingrese el CVV simulado.',
            'cvv.digits_between' => 'El CVV debe tener entre 3 y 4 dígitos.',
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
            'confirmation' => 'SA-'.strtoupper(Str::random(8)),
            'customer' => $validated,
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'total' => $cart->subtotal(),
            'placed_at' => now()->format('d/m/Y H:i'),
        ];

        session()->put('checkout_order', $order);
        $cart->clear();

        return redirect()
            ->route('checkout.success')
            ->with('success', 'Su compra fue registrada correctamente.');
    }

    public function success(): RedirectResponse|View
    {
        $order = session('checkout_order');

        if (! $order) {
            return redirect()->route('cart.index');
        }

        return view('checkout.success', compact('order'));
    }
}
