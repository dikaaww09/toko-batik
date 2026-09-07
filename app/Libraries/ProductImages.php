<?php
namespace App\Libraries;
use CodeIgniter\HTTP\Files\UploadedFile;
class ProductImages
{
    public function store(UploadedFile $file): string
    {
        $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$file->getMimeType()] ?? null;
        if (! $file->isValid() || ! $extension) { throw new \RuntimeException('Gambar tidak valid.'); }
        // Re-encode pixels to discard metadata and embedded non-image payloads.
        $bytes = file_get_contents($file->getTempName());
        $image = @imagecreatefromstring($bytes);
        if (! $image) { throw new \RuntimeException('Gambar tidak dapat dibaca.'); }
        $name = bin2hex(random_bytes(16)) . '.' . $extension;
        $path = FCPATH . 'uploads/products/' . $name;
        try {
            imagesavealpha($image, true);
            $saved = match ($extension) {
                'jpg' => imagejpeg($image, $path, 85),
                'png' => imagepng($image, $path, 7),
                'webp' => imagewebp($image, $path, 85),
            };
            if (! $saved) { throw new \RuntimeException('Gambar gagal disimpan.'); }
        } finally { imagedestroy($image); }
        return $name;
    }
    public function delete(?string $name): void
    {
        if ($name && preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/D', $name)) {
            $path = FCPATH . 'uploads/products/' . $name;
            if (is_file($path) && ! unlink($path)) { log_message('error', 'Gagal membersihkan gambar produk: {name}', ['name' => $name]); }
        }
    }
}
