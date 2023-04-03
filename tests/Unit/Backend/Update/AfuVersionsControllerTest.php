<?php

namespace Tests\Unit\Backend\Update;

use App\Models\AflProducts;
use App\Models\AfuVersions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AfuVersionsControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_versionAdd_uploadANewVersionFile_returnResponseSuccess()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        AflProducts::factory()->create(['product_id' => 81, 'product_sku' => 'SHJDK-CKJC']);
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 81,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $path,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(500);
        // $content = (array) json_decode($response->content());
        // $install_array = (array) $content['page_message'];
        // $this->assertIsArray($install_array, 'Helpdesk Product 2 version v7.1.1 added.');
    }

    public function test_versionAdd_uploadAInvalidVersionFile_returnResponseWithAErrorMessage()
    {
        $this->withoutMiddleware();
        $path = UploadedFile::fake()->create('download.zip', 1000, 'application/zip');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 81,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $path,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(500);
    
    }

    public function test_versionAdd_uploadAInvalidInstallLimit_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $rootDirectory = config('test.SCRIPT_ROOT_DIRECTORY');
        $ARCHIVES_DIRECTORY = config('test.ARCHIVES_DIRECTORY');
        $QUERIES_DIRECTORY = config('test.ARCHIVES_DIRECTORY'); 

        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 81,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $path,
            'version_install_limit' => 10.1,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be added because of this reason:Invalid version installatio
ns limit.');
    }

    public function test_versionAdd_uploadAInvalidUpgradeLimit_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $rootDirectory = config('test.SCRIPT_ROOT_DIRECTORY');
        $ARCHIVES_DIRECTORY = config('test.ARCHIVES_DIRECTORY');
        $QUERIES_DIRECTORY = config('test.ARCHIVES_DIRECTORY'); 
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 81,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $path,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10.1,
            'version_date' => now(),
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be added because of this reason:Invalid version upgrades limit.');
    }

    public function test_versionAdd_uploadAInvalidVersionExpireDate_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 81,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $path,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_expire_date' => '0000-0-00',
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be added because of this reason:Invalid version expiration
date.Invalid version expiration date.');
    }

    public function test_versionAdd_uploadWithInvalidApiKey_returnResponseWithErrorMessage()
    {
        $this->withoutMiddleware();
        $file = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $data = [
            'product_id' => 81,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $file,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_expire_date' => '0000-0-00',
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'The action could not be completed because of this reason: Your api key has failed');
    }

    public function test_versionAdd_uploadWithInvalidProduct_returnResponseWithErrorMessage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 8.9,
            'version_number' => 'v7.1.1',
            'version_upgrade_file' => $path,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be added because of this reason:Invalid product, version nu
mber, or status.');
    }

    public function test_versionAdd_uploadWithNoVersionNumber_returnResponseWithErrorMessage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 81,
            'version_upgrade_file' => $path,
            'version_install_limit' => 10,
            'version_upgrade_limit' => 10,
            'version_date' => now(),
            'version_comments' => 'This is a version test',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/add'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be added because of this reason:Invalid product, version nu
mber, or status.');
    }

    public function test_versionUpdate_editDetails_returnResponseWithSuccessMessage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'product_title' => 'Faveo Helpdesk',
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Helpdesk Product 2 version v7.1.1 updated.');
    }

    public function test_versionUpdate_uploadAInvalidVersionFile_returnResponseWithAErrorMessage()
    {
        $this->withoutMiddleware();
        $path = UploadedFile::fake()->create('download.zip', 1000, 'application/zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be updated because of this reason:Invalid upgrade archive for
mat or size (ZIP archive, 100 MB max).');
    }

    public function test_versionUpdate_uploadDupilcateData_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');

        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be updated because of this reason: Invalid record details,
duplicated data, or database error.');
    }

    public function test_versionUpdate_uploadInvalidInstallLimit_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');

        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 1.1,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be updated because of this reason: Invalid version installa
tions limit.');
    }

    public function test_versionUpdate_uploadAInvalidUpgradeLimit_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');

        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 1.1,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be updated because of this reason:Invalid version upgrades limit.');
    }

    public function test_versionUpdate_uploadAInvalidVersionExpireDate_returnReponseWithErrorMesssage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');

        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_expire_date' => '0000-000-00',
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be Updates because of this reason:Invalid version expiration
date.Invalid version expiration date.');
    }

    public function test_versionUpdate_uploadWithInvalidApiKey_returnResponseWithErrorMessage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');

        $data = [
            'version_id' => $version_id,
            'product_id' => 81,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'The action could not be completed because of this reason: Your api key has failed');
    }

    public function test_versionUpdate_uploadWithInvalidProduct_returnResponseWithErrorMessage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');

        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 8.1,
            'version_number' => 'v7.1.2',
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be updated because of this reason:Invalid product, version number, or status.');
    }

    public function test_versionUpdate_uploadWithNoVersionNumber_returnResponseWithErrorMessage()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
            'product_id' => 81,
            'version_upgrade_file' => $path,
            'version_install_limit' => 11,
            'version_upgrade_limit' => 11,
            'version_date' => now(),
            'version_comments' => 'This is a version test updated status',
            'version_status' => 1,
        ];
        $response = $this->json('POST', url('api/admin/versions/edit'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message'];
        $this->assertIsArray($install_array, 'Version could not be updated because of this reason:Invalid product, version number, or status.');
  

    }

    public function test_deleteVersions_returnSuccessResponse()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $version_id = AfuVersions::where('product_id', 81)->value('version_id');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => $version_id,
        ];
        $response = $this->json('POST', url('api/admin/versions/delete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'lang.deleted']);
        AflProducts::where('product_id', 81)->delete();
    }

    public function test_deleteVersionsWithoutDetails_returnSuccessResponse()
    {
        $this->withoutMiddleware();
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'test.zip');
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'version_id' => 8.2,
        ];
        $response = $this->json('POST', url('api/admin/versions/delete'), $data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.not_found']);
    }
}
