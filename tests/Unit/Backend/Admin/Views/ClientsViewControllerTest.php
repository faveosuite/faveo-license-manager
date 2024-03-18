<?php

namespace Tests\Unit\Backend\Admin\Views;

use App\Models\AflClients;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientsViewControllerTest extends TestCase
{
    use DatabaseTransactions;
    protected $client_id;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createClientDetails();
    }

    protected function createClientDetails()
    {
        $client = AflClients::factory()->create();
        $product = AflProducts::factory()->create();
        AflLicenses::factory()->create(['client_id' => $client->client_id, 'product_id' => $product->product_id, 'license_code' => null]);
        AflInstallations::factory()->create(['client_id' => $client->client_id, 'product_id' => $product->product_id, 'license_code' => '']);
        $this->client_id = $client->client_id;
    }

    public function test_get_client_info()
    {
        $response = $this->get('api/admin/clientView/' . $this->client_id);
        $response->assertStatus(200);
        $client = json_decode($response->content())->data;
        $this->assertNotNull($client);
        $this->assertTrue(property_exists($client, 'full_name'));
    }

    public function test_get_client_installations()
    {
        $response = $this->get('api/admin/clientInstallations/' . $this->client_id);
        $response->assertStatus(200);

        $installations = json_decode($response->content())->data->data;
        $this->assertNotEmpty($installations);
        $this->assertEquals($this->client_id, $installations[0]->client_id);
    }

    public function test_client_does_not_have_installations()
    {
        $client = AflClients::factory()->create();
        $response = $this->get('api/admin/clientInstallations/' . $client->client_id);
        $response->assertStatus(200);

        $installations = json_decode($response->content())->data->data;
        $this->assertEmpty($installations);
    }

    public function test_get_client_licenses()
    {
        $response = $this->get('api/admin/clientLicenses/' . $this->client_id);
        $response->assertStatus(200);

        $licenses = json_decode($response->content())->data->data;
        $this->assertNotEmpty($licenses);
        $this->assertEquals($this->client_id, $licenses[0]->client_id);
    }
}
