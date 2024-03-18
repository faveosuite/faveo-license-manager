<?php

namespace Tests\Unit\Backend\Admin\Views;

use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductsViewControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $productId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->productId = $this->createProductInstallationsAndLicense();
    }

    private function createProductInstallationsAndLicense()
    {
        $product = AflProducts::factory()->create();
        $client = AflClients::factory()->create();
        AflInstallations::factory()->create(['client_id' => $client->client_id, 'product_id' => $product->product_id,'license_code' => '']);
        AflLicenses::factory()->create(['client_id' => $client->client_id, 'product_id' => $product->product_id, 'license_code' => null]);
        return $product->product_id;
    }

    public function test_productView_allDetailsAreFetch()
    {
        $response = $this->get('api/admin/productView/' . $this->productId);
        $response->assertStatus(200);

        $product = json_decode($response->content())->data;

        $this->assertNotNull($product);
        $this->assertEquals($this->productId, $product->product_id);
        $this->assertTrue(property_exists($product, 'product_title'));
        $this->assertTrue(property_exists($product, 'product_url_download'));
    }

    public function test_productInstallations_allInstallationsAreOnParticularProduct_andHaveLicenseCode()
    {
        $response = $this->get('api/admin/productInstallations/' . $this->productId);
        $response->assertStatus(200);

        $installations = json_decode($response->content())->data->data;

        $this->assertNotEmpty($installations);
        foreach ($installations as $installation) {
            $this->assertEquals($this->productId, $installation->product_id);
            $this->assertTrue(!is_null($installation->client_id) || !is_null($installation->license_code));
        }
    }

    public function test_productLicenses_allLicenseAreOnParticularProduct_andHaveLicenseCode()
    {
        $response = $this->get('api/admin/productLicenses/' . $this->productId);
        $response->assertStatus(200);

        $licenses = json_decode($response->content())->data->data;

        $this->assertNotEmpty($licenses);
        foreach ($licenses as $license) {
            $this->assertEquals($this->productId, $license->product_id);
            $this->assertTrue(!is_null($license->client_id) || !is_null($license->license_code));
        }
    }
}
