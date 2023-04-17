<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflLicenses;
use App\Models\AflProducts;
use Tests\TestCase;

class LicenseControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_licenseAdd_whenLicenseIsAdded_shouldReciveResponseTrue200AndDatabaseHasTheCode()
    {
        $this->withoutMiddleware();
        $product = AflProducts::factory()->create(['product_id' => rand(10000, 99999), 'product_sku' => \Str::random(6)]);
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => $product->product_id,
            'license_code' => 'W23EDI98CJKO234M',
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('afl_licenses', ['license_code' => 'W23EDI98CJKO234M']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithoutLicenseCode_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'The License code is manditory']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithInvalidIp_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_ip' => 1234555,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Invalid License IP Address']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithInvalidDoamin_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_domain' => 1234555,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Invalid Domain(s) Address']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithFloatLicenseLimit_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2.789,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.invalid_license_limit']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithInvalidLicenseExpiry_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '12-000-00000',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License Expiration Date']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithInvalidUpdatesExpiry_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '12-2-2222',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License Update Date']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithInvalidSupportExpiry_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '12-2-2222',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License Support Date']);
    }

    public function test_licenseAdd_whenLicenseIsAddedWithInvalidProductId_shouldReciveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13.1,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2020-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/add'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'There are invalid details present in this request']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedwithInvalid_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_id' => $license_id,
            'product_id' => 13,
            'license_code' => 'W23EDI98CJKO234M',
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 5,
            'license_expire_date' => '2090-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case which is updated',
        ];

        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'The license Details has been Updated Successfully']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithoutLicenseId_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 13,
            'license_code' => 'W23EDI98CJKO234M',
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 5,
            'license_expire_date' => '2090-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case which is updated',
        ];

        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License ID']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithoutLicenseCode_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 13,
            'license_id' => $license_id,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 5,
            'license_expire_date' => '2090-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case which is updated',
        ];

        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'The License code is manditory']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithInvalidIp_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 13,
            'license_id' => $license_id,
            'license_require_domain' => 1,
            'license_code' => 'W23EDI98CJKO234M',
            'license_ip' => 12335555,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 5,
            'license_expire_date' => '2090-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case which is updated',
        ];

        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Invalid License IP Address']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithInvalidDomain_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'product_id' => 13,
            'license_id' => $license_id,
            'license_require_domain' => 1,
            'license_code' => 'W23EDI98CJKO234M',
            'license_domain' => 12335555,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 5,
            'license_expire_date' => '2090-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case which is updated',
        ];
        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Invalid Domain(s) Address']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithInvalidLicenseLimit_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_id' => $license_id,
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2.5,
            'license_expire_date' => '2022-09-29',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'lang.invalid_license_limit']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithInvalidExpiryDate_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_id' => $license_id,
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '12-000-00000',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/edit'), $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License Expiration Date']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithUpdatesDate_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_code' => 'W23EDI98CJKO234M',
            'license_id' => $license_id,
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '12-2-2222',
            'license_support_date' => '2022-09-29',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License Update Date']);
    }

    public function test_licenseUpdate_whenLicenseIsUpdatedWithInvalidSupportDate_shouldRecieveResponse400()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');

        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_id' => $license_id,
            'license_code' => 'W23EDI98CJKO234M',
            'product_id' => 13,
            'license_require_domain' => 1,
            'license_status' => 1,
            'license_order_number' => 8494872,
            'license_limit' => 2,
            'license_expire_date' => '2022-09-12',
            'license_updates_date' => '2022-09-29',
            'license_support_date' => '12-2-2222',
            'license_comments' => 'This is license for the test case',
        ];
        $response = $this->json('POST', url('api/admin/license/edit'), $data);
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => 'Please update the License Support Date']);
    }

    public function test_deleteLicense_whenLicenseIsDeleted_shouldRecieveResponse200()
    {
        $this->withoutMiddleware();
        $license_id = AflLicenses::where('license_code', 'W23EDI98CJKO234M')->value('license_id');
        $data = [

            'api_key_secret' => '5hDuaXuTh9gTLfPL',
            'license_id' => $license_id,
        ];
        $response = $this->json('POST', url('api/admin/license/delete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'The record you have selected has been deleted from the Auto Faveo License Manager Database']);
        $response->assertJson(['data' => 1]);
        AflProducts::where('product_id', 13)->delete();
    }
}
