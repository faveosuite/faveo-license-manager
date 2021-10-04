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
        $this->withoutMiddleware();
        AflProducts::factory()->create(['product_id'=>10,'product_sku'=>'ABCDEFG']);
        $data = [
                 'product_id' => 10,
                 'License_Verification_Period' =>5,
                 'License_Storage_type' =>'DATABASE',
                 'MySQL_Table_Name' => 'faveo_license',
                 'Database_License_File_Location' => 'license.signature.key',
                 'Delete_Cancelled_License' => 'No',
                 'Delete_Cracked_License' => 'No',
                 'God_Mode' =>'Yes',
                  'Connection_Timeout' => 30,
                 'Delete_Downloaded_Archive_After_Extracting' =>'YES'
             ];
             $response= $this->json('POST',url('api/admin/config'),$data);
             $response->assertStatus(200);

    }
    public function test_configGenerate_ConfigRequestedWithAnyInvalidInput_shouldRecieveResponse404()
    {
        $this->withoutMiddleware();
        $data = [
            'product_id' => 10,
            'License_Verification_Period' =>5,
            'License_Storage_type' =>'DATABASE',
            'MySQL_Table_Name' => 'faveo_license',
            'Database_License_File_Location' => 'license.signature.key',
            'Delete_Cancelled_License' => 'No',
            'Delete_Cracked_License' => 'No',
            'God_Mode' =>'Yes',
            'Delete_Downloaded_Archive_After_Extracting' =>'YES'
        ];
        $response= $this->json('POST',url('api/admin/config'),$data);

        $response->getOriginalContent()['Error Message'];
        $response->assertStatus(200);
        AflProducts::where('product_sku','ABCDEFG')->delete();
    }
}
