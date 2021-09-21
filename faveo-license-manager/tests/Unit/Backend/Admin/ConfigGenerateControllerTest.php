<?php

namespace Tests\Unit\Backend\Admin;

use Tests\TestCase;
use App\Models\AflProducts;

class ConfigGenerateControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_configGenerate_configurationsIsRequested_shouldRecieveResponse200()
    {
        AflProducts::factory()->create(['product_id'=>10,'product_sku'=>'ABCDEFG']);
        $data = ['token' => env('LICENSE_KEY'),
                 'product_id' => 10,
                 'License_Verification_Period' =>5,
                 'License_Storage_type' =>'DATABASE',
                 'MySQL_Table_Name' => 'faveo_license',
                 'Database_License_File_Location' => 'license.signature.key',
                 'Delete_Cancelled_License' => 'No',
                 'Delete_Cracked_License' => 'No',
                 'God_Mode' =>'Yes',
             ];
             $response= $this->json('POST',url('api/admin/config'),$data);
             $response->assertStatus(200);


    }
    public function test_configGenerate_ConfigRequestedWithInvalidStorage_shouldRecieveResponse404()
    {

        $data = [ 'token' => env('LICENSE_KEY'),
                 'product_id' => 10,
                 'License_Verification_Period' =>5,
                 'License_Storage_type' =>'DATA',
                 'MySQL_Table_Name' => 'faveo_license',
                 'Database_License_File_Location' => 'license.signature.key',
                 'Delete_Cancelled_License' => 'No',
                 'Delete_Cracked_License' => 'No',
                 'God_Mode' =>'Yes',
             ];
             $response= $this->json('POST',url('api/admin/config'),$data);
             $response->assertStatus(500);




    }
    public function test_configGenerate_ConfigRequestedWithInvalidProduct_shouldRecieveResponse404()
    {
        $data = [ 'token' => env('LICENSE_KEY'),
            'product_id' => 100,
            'License_Verification_Period' =>5,
            'License_Storage_type' =>'DATABASE',
            'MySQL_Table_Name' => 'faveo_license',
            'Database_License_File_Location' => 'license.signature.key',
            'Delete_Cancelled_License' => 'No',
            'Delete_Cracked_License' => 'No',
            'God_Mode' =>'Yes',
        ];
        $response= $this->json('POST',url('api/admin/config'),$data);
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
        $response->assertJson(['message' => "There are invalid details present in this request"]);

        AflProducts::where('product_sku','ABCDEFG')->delete();


    }
}
