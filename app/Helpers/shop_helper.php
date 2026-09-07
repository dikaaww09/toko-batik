<?php
function rupiah($amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}
function product_image(?string $filename): string
{
    if ($filename && preg_match('/^sample-[1-6]\.svg$/D', $filename)) {
        return base_url('assets/images/' . $filename);
    }
    if ($filename && preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/D', $filename)
        && is_file(FCPATH . 'uploads/products/' . $filename)) {
        return base_url('uploads/products/' . $filename);
    }
    return base_url('assets/images/fallback.svg');
}
function whatsapp_url(?array $product = null): ?string
{
    $number = config('Shop')->whatsapp;
    if (! preg_match('/^[1-9][0-9]{7,14}$/D', $number)) {
        return null;
    }
    $message = $product
        ? 'Halo, saya tertarik dengan produk ' . $product['nama_produk'] . ' seharga ' . rupiah($product['harga']) . '. Apakah produk ini masih tersedia?'
        : 'Halo, saya ingin bertanya tentang koleksi Batik Pusaka.';
    return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
}
