<?php
namespace Tests\Unit;
use App\Libraries\ProductImages;
use CodeIgniter\Test\CIUnitTestCase;
final class ShopSecurityTest extends CIUnitTestCase
{
    public function testWhatsappMessageUsesProductAndEncodedQuery(): void
    {
        helper('shop');
        $shop = config('Shop');
        $old = $shop->whatsapp;
        try {
            $shop->whatsapp = '6280000000000'; // Test fixture, never a live store contact.
            $url = whatsapp_url(['nama_produk' => 'Kawung & Sekar', 'harga' => 185000]);
            $this->assertStringStartsWith('https://wa.me/6280000000000?text=', $url);
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            $this->assertSame('Halo, saya tertarik dengan produk Kawung & Sekar seharga Rp185.000. Apakah produk ini masih tersedia?', $query['text']);
            $shop->whatsapp = 'javascript:alert(1)';
            $this->assertNull(whatsapp_url());
            $shop->whatsapp = '';
            $this->assertNull(whatsapp_url());
        } finally { $shop->whatsapp = $old; }
    }
    public function testImagePathsCannotTraverseOrUseArbitrarySvg(): void
    {
        helper('shop');
        $fallback = base_url('assets/images/fallback.svg');
        $this->assertSame($fallback, product_image('../../.env'));
        $this->assertSame($fallback, product_image('evil.svg'));
        $this->assertSame($fallback, product_image('missing.jpg'));
        $this->assertSame(base_url('assets/images/sample-1.svg'), product_image('sample-1.svg'));
    }
    public function testDeletionCannotRemoveStaticSamplesOrOutsideFile(): void
    {
        $file = WRITEPATH . 'delete-guard-test.txt';
        file_put_contents($file, 'guard');
        try {
            $images = new ProductImages();
            $images->delete('../../../writable/delete-guard-test.txt');
            $images->delete('sample-1.svg');
            $this->assertFileExists($file);
            $this->assertFileExists(FCPATH . 'assets/images/sample-1.svg');
        } finally { unlink($file); }
    }
}
