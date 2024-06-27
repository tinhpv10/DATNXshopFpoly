<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function viewCart()
    {
        $cartItems = Session::get('cartItems', []);

        return view('layouts.cart', [
            'cartItems' => $cartItems,
        ]);
    }

    public function updateCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $cartItems = Session::get('cartItems', []);

        foreach ($cartItems as &$cartItem) {
            if ($cartItem['id'] == $productId) {
                $cartItem['quantity'] = $quantity;
                break;
            }
        }

        Session::put('cartItems', $cartItems);
        return response()->json(['success' => true]);
    }

    public function removeFromCart($productId)
    {
        $cartItems = Session::get('cartItems', []);
        foreach ($cartItems as $key => $cartItem) {
            if ($cartItem['id'] == $productId) {
                unset($cartItems[$key]);
                break;
            }
        }
        Session::put('cartItems', $cartItems);
        if (empty($cartItems)) {
            return redirect()->route('cart.view')->with('success', 'Your cart is now empty.');
        } else {
            return redirect()->route('cart.view')->with('success', 'Item removed from cart.');
        }

    }

}
