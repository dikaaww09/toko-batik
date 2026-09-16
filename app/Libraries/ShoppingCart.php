<?php

namespace App\Libraries;

use App\Models\ProductModel;

class ShoppingCart
{
    private const SESSION_KEY = 'shopping_cart';

    public function quantities(): array
    {
        $cart = session()->get(self::SESSION_KEY);
        return is_array($cart) ? $cart : [];
    }

    public function set(int $productId, int $quantity): void
    {
        $cart = $this->quantities();
        if ($quantity < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min($quantity, 99);
        }
        session()->set(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    public function count(): int
    {
        return array_sum(array_column($this->details()['items'], 'qty'));
    }

    public function details(): array
    {
        $cart = $this->quantities();
        if ($cart === []) {
            return ['items' => [], 'total' => 0];
        }
        $products = (new ProductModel())->whereIn('id', array_keys($cart))->findAll();
        $items = [];
        $total = 0;
        foreach ($products as $product) {
            $quantity = (int) ($cart[$product['id']] ?? 0);
            if ($quantity < 1 || $product['status_ketersediaan'] !== 'tersedia') {
                continue;
            }
            $subtotal = (int) $product['harga'] * $quantity;
            $items[] = ['product' => $product, 'qty' => $quantity, 'subtotal' => $subtotal];
            $total += $subtotal;
        }
        return ['items' => $items, 'total' => $total];
    }
}
