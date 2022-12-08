<?php

namespace Tests\Unit\Backend\Update;

use App\Models\AflProducts;
use App\Models\AfuInstallations;
use App\Models\AfuVersions;
use Tests\TestCase;

class UpdateInstallationsControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_updateInstallationEdit_whenInstallationDetailsUpdated_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflProducts::factory()->create(['product_id' => 6, 'product_sku' => 'INSTALL-UPDATEE', 'product_key' => 'sndndhhdsh']);
        AfuVersions::factory()->create(['version_id' => 8, 'product_id' => 6]);
        AfuInstallations::factory()->create(['installation_id' => 1, 'version_id' => 8, 'product_id' => 6, 'installation_ip' => '109.0.0.2']);
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'installation_id' => 1,
            'installation_ip' => '127.0.0.1',
            'installation_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/updatedInstallation/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install = $content['page_message'];
        $this->assertEquals($install, 'Helpdesk Product 2 installation on 127.0.0.1 updated.');
    }

    public function test_updateInstallationEdit_whenInstallationDetailsUpdatedWithoutInstallationPresent_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'installation_id' => 6,
            'installation_ip' => '127.0.0.1',
            'installation_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/updatedInstallation/edit'), $data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }

    public function test_updateInstallationEdit_whenInstallationDetailsUpdatedWithoutProperFormat_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'installation_id' => 1,
            'installation_ip' => '12sdssdds',
            'installation_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/updatedInstallation/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install = $content['page_message'];
        $this->assertEquals($install, 'Installation could not be updated because of this reason: Invalid IP address or status.');
    }

    public function test_deleteInstallationUpdate_whenInstallationDetailsDeletedWhichareNotPresent_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'installation_id' => 1,
            'delete_record' => 1,
        ];
        $response = $this->json('POST', url('api/admin/updatedInstallation/edit'), $data);
        $response->assertStatus(200);
        $this->assertEquals($response->content(), 'Deleted 1 installation(s).');
    }

    public function test_deleteInstallationUpdate_whenInstallationDetailsDeleted_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'installation_id' => 1,
            'delete_record' => 1,
        ];
        $response = $this->json('POST', url('api/admin/updatedInstallation/edit'), $data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
        AflProducts::where('product_sku', 'INSTALL-UPDATEE')->delete();
        AfuVersions::where('version_id', 8)->delete();
    }
}
