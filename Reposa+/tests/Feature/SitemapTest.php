<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_endpoint_returns_successful_xml_response(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('xml', strtolower((string) $response->headers->get('Content-Type')));
    }

    public function test_sitemap_contains_well_formed_xml_and_standard_namespaces(): void
    {
        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $xml = simplexml_load_string($content);

        $this->assertNotFalse($xml, 'The sitemap response must be a valid, well-formed XML document.');
        $namespaces = $xml->getDocNamespaces();
        $this->assertContains('http://www.sitemaps.org/schemas/sitemap/0.9', $namespaces);
    }

    public function test_sitemap_includes_core_pages_and_products(): void
    {
        $product = Product::factory()->create([
            'name' => ['es' => 'Almohada Ortopédica Test', 'en' => 'Orthopedic Test Pillow'],
            'price' => 49.99,
            'stock' => 10,
        ]);

        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        $xml = simplexml_load_string($content);

        $locs = [];
        foreach ($xml->url as $urlElement) {
            $locs[] = (string) $urlElement->loc;
        }

        $this->assertContains(url('/'), $locs, 'Sitemap must contain the homepage URL.');
        $this->assertContains(route('catalog'), $locs, 'Sitemap must contain the catalog URL.');
        $this->assertContains(route('products.show', $product), $locs, 'Sitemap must contain each product detail URL.');

        // Verify product metadata
        $productEntry = null;
        foreach ($xml->url as $urlElement) {
            if ((string) $urlElement->loc === route('products.show', $product)) {
                $productEntry = $urlElement;
                break;
            }
        }

        $this->assertNotNull($productEntry);
        $this->assertNotEmpty((string) $productEntry->lastmod);
        $this->assertEquals('weekly', (string) $productEntry->changefreq);
        $this->assertEquals('0.8', (string) $productEntry->priority);
    }

    public function test_sitemap_forces_https_when_forwarded_proto_is_secure(): void
    {
        Product::factory()->create([
            'name' => ['es' => 'Almohada Pro HTTPS', 'en' => 'Pro HTTPS Pillow'],
            'price' => 59.99,
            'stock' => 5,
        ]);

        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
        ])->get('/sitemap.xml');

        $response->assertStatus(200);

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);

        foreach ($xml->url as $urlElement) {
            $this->assertStringStartsWith('https://', (string) $urlElement->loc, 'All URLs in sitemap must use HTTPS when forwarded with HTTPS.');
        }
    }

    public function test_robots_txt_contains_sitemap_reference(): void
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('sitemap.xml', strtolower($content));
    }
}
