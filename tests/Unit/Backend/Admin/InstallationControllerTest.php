<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Tests\TestCase;
use App\Models\AflApiKeys;


class InstallationControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_installUpdate_whenInstallationDetailsUpdated_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $product = AflProducts::factory()->create(['product_sku' => \Str::upper(\Str::random(10))]);
        $license = AflLicenses::factory()->create(['product_id' => $product->product_id]);
        $installation = AflInstallations::factory()->create(['product_id' => $product->product_id, 'license_code' => $license->license_code]);
        $data = [

            'api_key_secret' => AflApiKeys::first()->api_key_secret,
            'installation_id' => $installation->installation_id,
            'installation_ip' => '127.0.0.1',
            'installation_status' => 1,
            'installation_disable_ip' => 1,
        ];
        $response = $this->json('POST', url('api/admin/installations/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install = $content['page_message'];
        $this->assertEquals($install, "Helpdesk Product 2 installation on {$installation->installation_domain} (127.0.0.1) updated.");
    }

    public function test_installUpdate_whenInstallationDetailsUpdatedWithoutInstallationPresent_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflInstallations::where('installation_id', 6)->delete();
        $data = [

            'api_key_secret' => AflApiKeys::first()->api_key_secret,
            'installation_id' => 6,
            'installation_ip' => '127.0.0.1',
            'installation_status' => 1,
            'installation_disable_ip' => 1,
        ];
        $response = $this->json('POST', url('api/admin/installations/edit'), $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Installation could not be updated because of this reason: Invalid record details, duplicated data, or database error.']);
    }

    public function test_deleteInstallation_whenInstallationDetailsDeletedWhichareNotPresent_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflInstallations::where('installation_id', 6)->delete();
        $data = [

            'api_key_secret' => AflApiKeys::first()->api_key_secret,
            'installation_id' => 6,
            'delete_record' => 1,
        ];
        $response = $this->json('POST', url('api/admin/installations/edit'), $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Installation could not be updated because of this reason: Invalid record or database error.Invalid IP address or status.']);
    }

    public function test_deleteInstallation_whenInstallationDetailsDeleted_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $installation = AflInstallations::factory()->create();
        $data = [

            'api_key_secret' => AflApiKeys::first()->api_key_secret,
            'installation_id' => $installation->installation_id,
            'delete_record' => 1,
        ];
        $response = $this->json('POST', url('api/admin/installations/edit'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('afl_installations', ['license_code' => 'AKO094GD9NCK0DHJ']);
        AflProducts::where('product_sku', 'INSTALL-UPDATE')->forceDelete();
    }
}
