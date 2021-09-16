<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflBannedHosts;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\AflReports;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_search_searchForBannedHostsUsingIp_shouldRespondWith200()
    {
        AflBannedHosts::factory()->create(['banned_host_id'=>1]);
        $data = [
                  'token' => env('LICENSE_KEY'),
                  'api_key_secret' => '5hDuaXuTh9gTLfPL',
                  'search_type' => 'banned_host',
                  'search_keyword' =>'109.89.89.22'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $banned_array= (array)$content['page_message']['0'];
        $this->assertArrayHasKey('banned_host_ip',$banned_array);
    }
    public function test_search_searchForBannedHostsUsingComments_shouldRespondWith200()
    {
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'banned_host',
            'search_keyword' =>'This is a search for a banned host'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $banned_array= (array)$content['page_message']['0'];
        $this->assertArrayHasKey('banned_host_comments',$banned_array);
        AflBannedHosts::where('banned_host_ip','109.89.89.22')->delete();
    }

    public function test_search_searchForCallbackUsingLicenseCode_shouldRespondWith200(){
        AflCallbacks::factory()->create(['callback_id'=>1,'product_id'=>14,'license_code'=> 'QWRT125SKOD87C6H','callback_domain'=>'faveotest.com']);
        AflProducts::factory()->create(['product_id'=>14,'product_sku'=>'SEARCH-CALLBACK']);
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' =>'QWRT125SKOD87C6H'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $callback_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('license_code',$callback_array);

    }
    public function test_search_searchForCallbackUsingCallbackDomain_shouldRespondWith200(){
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' =>'faveotest.com'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $callback_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('callback_domain',$callback_array);

    }
    public function test_search_searchForCallbackUsingCallbackIp_shouldRespondWith200(){

        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'callback',
            'search_keyword' =>'106.51.140.178'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $callback_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('callback_ip',$callback_array);
        AflProducts::where('product_id',14)->delete();
        AflCallbacks::where('callback_id',1)->delete();

    }
    public function test_search_searchForReportUsingReportText_shouldRespondWith200(){
        AflReports::factory()->create();
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'report',
            'search_keyword' =>'The configuration file could not be generated because of this reason: Invalid product, license verification period, license storage type, license file location or MySQL table name.'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $report_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('report_text',$report_array);

    }
    public function test_search_searchForReportUsingLicenseCode_shouldRespondWith200(){
        AflReports::factory()->create();
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'report',
            'search_keyword' => 'AK12BJSI9OP3BDJ8'
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $report_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('license_code',$report_array);

    }

    public function test_search_searchForInstallationUsingLicenseCode_shouldRespondWith200()
    {
        AflProducts::factory()->create(['product_id'=>15,'product_sku'=> 'SEARCH-INST']);
        AflInstallations::factory()->create(['installation_id'=>3 , 'product_id'=>15, 'license_code'=>'QWYDKO0D6NCLO5HN']);
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => 'QWYDKO0D6NCLO5HN',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $install_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('license_code',$install_array);
    }
    public function test_search_searchForInstallationUsingInstallationDomain_shouldRespondWith200()
    {
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => 'sandesh.com',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $install_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('installation_domain',$install_array);
    }
    public function test_search_searchForInstallationUsingInstallationIp_shouldRespondWith200()
    {
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'installation',
            'search_keyword' => '106.51.140.178',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $install_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('installation_ip',$install_array);
        AflProducts::where('product_id',15)->delete();
        AflInstallations::where('installation_id',3)->delete();
    }
    public function test_search_searchForLicensesUsingLicenseCode_shouldRespondWith200(){

        AflProducts::factory()->create(['product_id'=>16,'product_sku'=>'SEARCH-LIC']);
        AflLicenses::factory()->create(['license_id'=> 10,'license_code'=>'ANKOSYU987NCKLO3','product_id'=>16,'license_comments'=> 'This is a license']);
        AflInstallations::factory()->create(['installation_id'=>4,'product_id'=>16,'license_code'=>'ANKOSYU987NCKLO3']);
        AflCallbacks::factory()->create(['product_id' => 16,'license_code'=>'ANKOSYU987NCKLO3']);
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'license',
            'search_keyword' => 'ANKOSYU987NCKLO3',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $license_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('license_code',$license_array);

    }
    public function test_search_searchForLicensesUsingLicenseComments_shouldRespondWith200(){

        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'license',
            'search_keyword' => 'This is a license',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $license_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('license_comments',$license_array);
        AflProducts::where('product_id',16)->delete();
        AflLicenses::where('product_id',16)->delete();
        AflInstallations::where('product_id',16)->delete();
        AflCallbacks::where('product_id',16)->delete();

    }
    public function test_search_searchForProductsUsingProductTitle_shouldRespondWith200(){

        AflProducts::factory()->create(['product_id'=>17,'product_sku'=>'SEARCH-PRO','product_title'=>'Faveo Test Product']);
        AflLicenses::factory()->create(['license_id'=> 11,'license_code'=>'KODJKSOPIC6789EH','product_id'=>17]);
        AflInstallations::factory()->create(['installation_id'=>5,'product_id'=>17,'license_code'=>'KODJKSOPIC6789EH']);
        AflCallbacks::factory()->create(['product_id' => 17,'license_code'=>'KODJKSOPIC6789EH']);
        AflReports::factory()->create(['product_id'=>17,'license_code'=>'KODJKSOPIC6789EH']);
        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'product',
            'search_keyword' => 'Faveo Test Product',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $product_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('product_title',$product_array);

    }
    public function test_search_searchForProductsUsingProductSku_shouldRespondWith200(){

        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'product',
            'search_keyword' => 'SEARCH-PRO',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $product_array = (array)$content['page_message']['0'];
        $this->assertArrayHasKey('product_sku',$product_array);
        AflProducts::where('product_id',17)->delete();
        AflLicenses::where('product_id',17)->delete();
        AflInstallations::where('product_id',17)->delete();
        AflCallbacks::where('product_id',17)->delete();
        AflReports::where('product_id',17)->delete();

    }
    public function test_search_searchForInvalidDetails_shouldRespondWith200WithErrorMessage(){

        $data = [
            'token' => env('LICENSE_KEY'),
            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'search_type' => 'license',
            'search_keyword' => 'This is a license that is not present',
        ];
        $response = $this->json('POST',url('api/admin/search'),$data);
        $response->assertStatus(200);
        $content=(array)json_decode($response->content());
        $error=$content['page_message'];
        $this->assertEquals($error,'There was an error searching for the particular detail in license manager');
    }

}
