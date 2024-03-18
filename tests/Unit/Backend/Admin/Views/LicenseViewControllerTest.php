<?php
namespace Tests\Unit\Backend\Admin\Views;

use App\Models\AflCallbacks;
use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LicenseViewControllerTest extends TestCase{
    use DatabaseTransactions;

    protected $license_id;
    protected $callbacks;
    protected $product;
    protected $client;
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createInstallationDetails();
    }
    protected function createInstallationDetails()
    {
        $this->client = AflClients::factory()->create();
        $this->product = AflProducts::factory()->create();
        $license = AflLicenses::factory()->create(['client_id' => $this->client->client_id, 'product_id' => $this->product->product_id, 'license_code' => null]);
        AflInstallations::factory()->create(['client_id' => $this->client->client_id, 'product_id' => $this->product->product_id, 'license_code' => '']);
        $this->callbacks = AflCallbacks::factory()->create(['client_id' => $this->client->client_id, 'product_id' => $this->product->product_id, 'license_code' => '']);
        $this->license_id = $license->license_id;
    }
    public function test_license_details()
    {
        $response = $this->get('api/admin/licenseView/' . $this->license_id);
        $response->assertStatus(200);
        $license = json_decode($response->content())->data;
        $this->assertNotNull($license);
        $this->assertEquals($license->latest_call_backs, $this->callbacks->callback_date_time);
        $this->assertEquals($license->installation_counts, 1);
        $this->assertEquals($license->call_backs_count, 1);
        $this->assertEquals($license->product->product_title, $this->product->product_title);
    }
    public function test_license_installations()
    {
        $response = $this->get('api/admin/licenseInstallation/' . $this->license_id);
        $response->assertStatus(200);
        $installations = json_decode($response->content())->data->data;
        $this->assertNotNull($installations);
        $this->assertEquals(count($installations), 1);
        $this->assertEquals($installations[0]->product_id, $this->product->product_id);
        $this->assertEquals($installations[0]->client_id, $this->client->client_id);
    }
}
