<?php

namespace Tests\Unit\Backend\Admin;

//use PHPUnit\Framework\TestCase;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_productAdd_whenProductIsAdded_shouldRecieveResponseTrue()
    {
        $this->withoutMiddleware();

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_title' => 'Helpdesk Product',
            'product_sku' => 'FAVEO-HDFRR',
            'product_status' => 1,
            'product_description' => 'This is a test product for license manager',
            'product_url_homepage' => null,
            'product_url_download' => null,
            'product_key' => 'wiicncniuiuci',
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
        $response = $this->json('POST', url('api/admin/products/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Product and the Product's details has been added"]);
        $response->assertJson(['data' => 1]);
    }

    public function test_productUpdate_whenProductIsUpdated_shouldRecieveResponseTrue()
    {
        $this->withoutMiddleware();
        $product_id = AflProducts::where('product_sku', 'FAVEO-HDFRR')->value('product_id');
        $data =
           [
               'api_key_secret' => '5hDuaXuTh9gTLfPL',
               'product_id' => $product_id,
               'product_title' => 'Helpdesk Updated',
               'product_sku' => 'FAVEO-TESTHDFE',
               'product_key' => 'wiicncniuiuci',
               'product_status' => 1,

           ];
        $response = $this->json('POST', url('api/admin/products/edit'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Product Deatils updated Successfully']);
        $response->assertJson(['data' => 1]);
    }

    public function test_productDelete_whenProductIsDeleted_shouldRecieveResponseTrue()
    {
        $this->withoutMiddleware();
        $product_id = AflProducts::where('product_sku', 'FAVEO-TESTHDFE')->value('product_id');
        AflLicenses::factory()->create(['product_id' => $product_id, 'license_code' => 'KIOSXH890DH678DK']);
        AflInstallations::factory()->create(['product_id' => $product_id, 'license_code' => 'KIOSXH890DH678DK']);
        AflCallbacks::factory()->create(['product_id' => $product_id, 'license_code' => 'KIOSXH890DH678DK']);
        $data =
           [

               'api_key_secret' => '5hDuaXuTh9gTLfPL',
               'product_id' => $product_id,
           ];
        $response = $this->json('POST', url('api/admin/products/delete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'The record you have selected has been deleted from the Auto Faveo License Manager Database']);
        $response->assertJson(['data' => 1]);
    }

    public function test_productAdd_whenProductIsAddedWithInvalidProductUrlHomepage_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_title' => 'Helpdesk Product',
            'product_sku' => 'FAVEO-HDFRR',
            'product_status' => 1,
            'product_description' => 'This is a test product for license manager',
            'product_key' => 'ckjdjdfjfds',
            'product_url_homepage' => 'sandesh',
            'product_url_download' => null,
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
        $response = $this->json('POST', url('api/admin/products/add'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.error_producturl']);
    }

    public function test_productAdd_whenProductIsAddedWithFloatEnvatoIdValue_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_title' => 'Helpdesk Product',
            'product_sku' => 'FAVEO-HDFRR',
            'product_status' => 1,
            'product_key' => 'skjnsdfjsfd',
            'product_description' => 'This is a test product for license manager',
            'product_version' => '4.6.2',
            'product_envato_id' => 1.999999,
        ];
        $response = $this->json('POST', url('api/admin/products/add'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.error_product_envato']);
    }

    public function test_productAdd_whenProductIsAddedWithoutTitleOrSku_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_description' => 'This is a test product for license manager',
            'product_key' => 'skjnsdfjsfd',
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
        $response = $this->json('POST', url('api/admin/products/add'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }

    public function test_productAdd_whenProductIsAddedWithoutApiKeySecret_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();

        $data = [

            'api_key_secret' => '555555555555555555555555555555555555hDuaXuTh9gTLfPL',
            'product_description' => 'This is a test product for license manager',
            'product_key' => 'skjnsdfjsfd',
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
        $response = $this->json('POST', url('api/admin/products/add'), $data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.invalid_api_key']);
    }

    public function test_productUpdate_whenProductIsUpdatedWithInvalidProductUrlHomepage_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();

        AflProducts::factory()->create(['product_id' => 100]);
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 100,
            'product_title' => 'Helpdesk Product',
            'product_sku' => 'FAVEO-HDFRR',
            'product_status' => 1,
            'product_key' => 'vjdsnvkjdscndkjs',
            'product_description' => 'This is a test product for license manager',
            'product_url_homepage' => 'sandesh',
            'product_url_download' => null,
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
        $response = $this->json('POST', url('api/admin/products/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.url_error']);
    }

    public function test_productUpdated_whenProductIsUpdatedWithFloatEnvatoIdValue_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 100,
            'product_title' => 'Helpdesk Product',
            'product_sku' => 'FAVEO-HDFRR',
            'product_status' => 1,
            'product_key' => 'hdsbvjndskj',
            'product_description' => 'This is a test product for license manager',
            'product_version' => '4.6.2',
            'product_envato_id' => 1.999999,
        ];
        $response = $this->json('POST', url('api/admin/products/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.envato_error']);
    }

    public function test_productUpdated_whenProductIsUpdatedWithoutTitleOrSku_shouldRecieveResponseFalse()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 100,
            'product_description' => 'This is a test product for license manager',
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
        $response = $this->json('POST', url('api/admin/products/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
        AflProducts::where('product_id', 100)->delete();
    }
}
