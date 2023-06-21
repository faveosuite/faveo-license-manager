<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflBannedHosts;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\AflReports;
use App\Models\AfuCallbacks;
use App\Models\AfuInstallations;
use App\Models\AfuVersions;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_search_searchForBanpnedHostsUsingIp_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflBannedHosts::factory()->create(['banned_host_id' => 1]);
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'banned_host',
            'search_keyword' => '109.89.89.22',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $banned_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('banned_host_ip', $banned_array);
    }

    public function test_search_searchForBannedHostsUsingComments_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'banned_host',
            'search_keyword' => 'This is a search for a banned host',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $banned_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('banned_host_comments', $banned_array);
        AflBannedHosts::where('banned_host_ip', '109.89.89.22')->delete();
    }

    public function test_search_searchForCallbackUsingLicenseCode_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $product = AflCallbacks::factory()->create(['callback_id' => rand(100000, 999999), 'product_id' => rand(100000, 999999), 'license_code' => 'QWRT125SKOD87C6H', 'callback_domain' => 'faveotest.com']);
        AflProducts::factory()->create(['product_id' => $product->product_id, 'product_sku' => \Str::random(10)]);
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' => 'QWRT125SKOD87C6H',
            'isLicenseSearchApi' => 1,
        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $callback_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('license_code', $callback_array);
    }

    public function test_search_searchForCallbackUsingCallbackDomain_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' => 'faveotest.com',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $callback_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('callback_domain', $callback_array);
    }

    public function test_search_searchForCallbackUsingCallbackIp_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' => '106.51.140.178',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $callback_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('callback_ip', $callback_array);
        AflProducts::where('product_id', 14)->delete();
        AflCallbacks::where('callback_id', 1)->delete();
    }

    public function test_search_searchForReportUsingReportText_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflReports::factory()->create();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'report',
            'search_keyword' => 'The configuration file could not be generated because of this reason: Invalid product, license verification period, license storage type, license file location or MySQL table name.',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $report_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('report_text', $report_array);
    }

    public function test_search_searchForReportUsingLicenseCode_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflReports::factory()->create();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'report',
            'search_keyword' => 'AK12BJSI9OP3BDJ8',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $report_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('license_code', $report_array);
    }

    public function test_search_searchForInstallationUsingLicenseCode_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflProducts::factory()->create(['product_id' => 15, 'product_sku' => \Str::random(10)]);
        AflInstallations::factory()->create(['installation_id' => 3, 'product_id' => 15, 'license_code' => 'QWYDKO0D6NCLO5HN']);
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => 'QWYDKO0D6NCLO5HN',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('license_code', $install_array);
    }

    public function test_search_searchForInstallationUsingInstallationDomain_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => 'sandesh.com',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('installation_domain', $install_array);
    }

    public function test_search_searchForInstallationUsingInstallationIp_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => '106.51.140.178',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('installation_ip', $install_array);
        AflProducts::where('product_id', 15)->delete();
        AflInstallations::where('installation_id', 3)->delete();
    }

    // public function test_search_searchForLicensesUsingLicenseCode_shouldRespondWith200()
    // {
    //     $this->withoutMiddleware();
    //     $product = AflProducts::factory()->create(['product_id' => rand(1000,9999), 'product_sku' => \Str::random(10)]);
    //     AflLicenses::factory()->create(['license_id' => rand(1000,9999), 'license_code' => 'ANKOSYU987NCKLO3', 'product_id' => $product->product_id, 'license_comments' => 'This is a license']);
    //     AflInstallations::factory()->create(['installation_id' => 4, 'product_id' => $product->product_id, 'license_code' => 'ANKOSYU987NCKLO3']);
    //     AflCallbacks::factory()->create(['product_id' => $product->product_id, 'license_code' => 'ANKOSYU987NCKLO3']);
    //     $data = [
    //         'api_key_secret' => '5hDuaXuTh9gTLfPL',
    //         'search_type' => 'license',
    //         'search_keyword' => 'ANKOSYU987NCKLO3',
    //         'isLicenseSearchApi' => 1,
    //     ];
    //     $response = $this->json('POST', url('api/admin/search'), $data);
    //     $response->assertStatus(200);
    //     $content = (array) json_decode($response->content());
    //     $license_array = (array) $content['page_message']['0'];
    //     $this->assertArrayHasKey('license_code', $license_array);
    // }

    // public function test_search_searchForLicensesUsingLicenseComments_shouldRespondWith200()
    // {
    //     $this->withoutMiddleware();
    //     $data = [

    //         'api_key_secret' => '5hDuaXuTh9gTLfPL',
    //         'search_type' => 'license',
    //         'search_keyword' => 'This is a license',
    //         'isLicenseSearchApi' => 1,

    //     ];
    //     $response = $this->json('POST', url('api/admin/search'), $data);
    //     $response->assertStatus(200);
    //     $content = (array) json_decode($response->content());
    //     $license_array = (array) $content['page_message']['0'];
    //     $this->assertArrayHasKey('license_comments', $license_array);
    //     AflProducts::where('product_id', 16)->delete();
    //     AflLicenses::where('product_id', 16)->delete();
    //     AflInstallations::where('product_id', 16)->delete();
    //     AflCallbacks::where('product_id', 16)->delete();
    // }

    public function test_search_searchForProductsUsingProductTitle_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        AflProducts::factory()->create(['product_id' => 17, 'product_sku' => 'SEARCH-PRO', 'product_title' => 'Faveo Test Product']);
        AflLicenses::factory()->create(['license_id' => 11, 'license_code' => 'KODJKSOPIC6789EH', 'product_id' => 17]);
        AflInstallations::factory()->create(['installation_id' => 5, 'product_id' => 17, 'license_code' => 'KODJKSOPIC6789EH']);
        AflCallbacks::factory()->create(['product_id' => 17, 'license_code' => 'KODJKSOPIC6789EH']);
        AflReports::factory()->create(['product_id' => 17, 'license_code' => 'KODJKSOPIC6789EH']);
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'product',
            'search_keyword' => 'Faveo Test Product',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $product_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('product_title', $product_array);
    }

    public function test_search_searchForProductsUsingProductSku_shouldRespondWith200()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'product',
            'search_keyword' => 'SEARCH-PRO',
            'isLicenseSearchApi' => 1,
        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $product_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('product_sku', $product_array);
        AflProducts::where('product_id', 17)->delete();
        AflLicenses::where('product_id', 17)->delete();
        AflInstallations::where('product_id', 17)->delete();
        AflCallbacks::where('product_id', 17)->delete();
        AflReports::where('product_id', 17)->delete();
    }

    public function test_search_searchForInvalidDetails_shouldRespondWith200WithErrorMessage()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'license',
            'search_keyword' => 'This is a license that is not present',
            'isLicenseSearchApi' => 1,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $error = $content['page_message'];
        $this->assertEquals($error, 'There was an error searching for the particular detail in license manager');
    }

    public function test_search_searchUsingCallbackIp_shouldReturnResponseWithCallbackIp()
    {
        $this->withoutMiddleware();
        $callback = AfuCallbacks::factory()->create(['callback_id' => rand(100000, 999999), 'product_id' => rand(100000, 999999), 'callback_ip' => '127.0.0.1']);
        AflProducts::factory()->create(['product_id' => $callback->product_id, 'product_sku' => \Str::random(10)]);
        AfuVersions::factory()->create(['product_id' => $callback->product_id, 'version_id' => 1]);
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' => '127.0.0.1',
            'isLicenseSearchApi' => 0,
        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $callback_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('callback_ip', $callback_array);
        AflProducts::where('product_id', 14)->delete();
        AfuCallbacks::where('callback_id', 100)->delete();
        AfuVersions::where('version_id', 1)->delete();
    }

    public function test_search_searchUsingUpdateInstallationProductTitle_shouldReturnResponseWithProductTitle()
    {
        $this->withoutMiddleware();
        AflProducts::factory()->create(['product_id' => 15, 'product_sku' => \Str::random(10)]);
        AfuInstallations::factory()->create(['installation_id' => 1, 'product_id' => 15, 'version_id' => 11]);
        AfuVersions::factory()->create(['version_id' => 11, 'product_id' => 15]);
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => 'Helpdesk Product 2',
            'isLicenseSearchApi' => 0,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('product_title', $install_array);
    }

    public function test_search_searchUsingUpdateInstallationInstallationIp_shouldReturnResponseWithProductTitle()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => '127.0.0.1',
            'isLicenseSearchApi' => 0,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        $content = (array) json_decode($response->content());
        $install_array = (array) $content['page_message']['0'];
        $this->assertArrayHasKey('installation_ip', $install_array);
        AflProducts::where('product_id', 15)->delete();
        AfuInstallations::where('installation_id', 1)->delete();
        AfuVersions::where('version_id', 11)->delete();
    }

    // public function test_search_searchUsingUpdateProductsProductTitle_shouldReturnResponseWithProductSku()
    // {
    //     $this->withoutMiddleware();
    //     $product = AflProducts::factory()->create(['product_id' => rand(1000,9999), 'product_sku' => \Str::random(10)]);
    //     AfuInstallations::factory()->create(['installation_id' => rand(1000,9999), 'product_id' => $product->product_id, 'version_id' => 12]);
    //     AfuCallbacks::factory()->create(['callback_id' => rand(1000,9999), 'product_id' => $product->product_id]);
    //     AflReports::factory()->create(['report_id' => rand(1000,9999), 'product_id' => $product->product_id]);

    //     $data = [
    //         'api_key_secret' => '5hDuaXuTh9gTLfPL',
    //         'search_type' => 'product',
    //         'search_keyword' => 'Helpdesk Product 2',
    //         'isLicenseSearchApi' => 0,

    //     ];
    //     $response = $this->json('POST', url('api/admin/search'), $data);
    //     $response->assertStatus(200);
    // }

    public function test_search_searchUsingUpdateProductsProductSku_shouldReturnResponseWithProductSku()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'product',
            'search_keyword' => 'HSJK-SKSJ',
            'isLicenseSearchApi' => 0,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        AflReports::where('report_id', 99)->delete();
        AfuCallbacks::where('callback_id', 12)->delete();
        AfuInstallations::where('installation_id', 11)->delete();
        AflProducts::where('product_id', 39)->delete();
    }

    // public function test_search_searchUsingUpdateReportsReportText_shouldReturnResponseWithReportText()
    // {
    //     $this->withoutMiddleware();
    //     AflProducts::factory()->create(['product_id'=>91,'product_sku'=>'HDJD-JCJCC','product_key'=>'dhsdsjshfhsd']);
    //     AflReports::factory()->create(['report_id'=>109,'product_id'=>91]);
    //     $data = [
    //         'api_key_secret' => '5hDuaXuTh9gTLfPL',
    //         'search_type' => 'report',
    //         'search_keyword' => 'The configuration file could not be generated because of this reason: Invalid product, license verification period, license storage type, license file location or MySQL table name.',
    //         'isLicenseSearchApi' => 0,

    //     ];
    //     $response = $this->json('POST', url('api/admin/search'), $data);
    //     $response->assertStatus(200);
    //     AflReports::where('report_id', 109)->delete();
    //     AflProducts::where('product_id', 91)->delete();
    // }

    public function test_search_searchUsingUpdateVersionsWithProductTitle_shouldReturnResponseWithVersionDetailsWithThatProductId()
    {
        $this->withoutMiddleware();
        AflProducts::factory()->create(['product_id' => 51, 'product_sku' => 'SEARCH-INST']);
        AfuVersions::factory()->create(['version_id' => 11, 'product_id' => 51]);
        AfuCallbacks::factory()->create(['callback_id' => 12, 'product_id' => 51, 'version_id' => 11]);
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'version',
            'search_keyword' => 'Helpdesk Product 2',
            'isLicenseSearchApi' => 0,

        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
    }

    public function test_search_searchUsingUpdateVersionsWithProductSku_shouldReturnResponseWithVersionDetailsWithThatProductId()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'version',
            'search_keyword' => 'SEARCH-INST',
            'isLicenseSearchApi' => 0,
        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
    }

    public function test_search_searchUsingUpdateVersionsWithVersionNumber_shouldReturnResponseWithVersionDetailsWithThatProductId()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'version',
            'search_keyword' => 'v7.1.1',
            'isLicenseSearchApi' => 0,
        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
    }

    public function test_search_searchUsingUpdateVersionsWithVersionComments_shouldReturnResponseWithVersionDetailsWithThatProductId()
    {
        $this->withoutMiddleware();
        $data = [
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'version',
            'search_keyword' => 'This is a version comment',
            'isLicenseSearchApi' => 0,
        ];
        $response = $this->json('POST', url('api/admin/search'), $data);
        $response->assertStatus(200);
        AflProducts::where('product_id', 51)->delete();
        AfuVersions::where('version_id', 11)->delete();
        AfuCallbacks::where('callback_id', 12)->delete();
    }
}
