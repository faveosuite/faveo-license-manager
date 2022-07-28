<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflCallbacks;
use App\Models\AfuCallbacks;
use Tests\TestCase;

class CallbacksControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_callbacksDelete_withIdsAndIsLicense_shouldReciveDeleted3Callbacks()
    {
        $this->withoutMiddleware();
        AflCallbacks::factory()->create(['callback_id' => 81]);
        AflCallbacks::factory()->create(['callback_id' => 82]);
        AflCallbacks::factory()->create(['callback_id' => 83]);
        $data = ['isLicense' => 1];
        $response = $this->json('POST', url('api/admin/callbackdelete?call[]=81&call[]=82&call[]=83'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Deleted 3 callback(s).']);
        $response->assertJson(['data' => 3]);
    }

    public function test_callbacksDelete_withIdsAndIsLicense0_shouldReciveDeleted3Callbacks()
    {
        $this->withoutMiddleware();
        AfuCallbacks::factory()->create(['callback_id' => 81]);
        AfuCallbacks::factory()->create(['callback_id' => 82]);
        AfuCallbacks::factory()->create(['callback_id' => 83]);
        $data = ['isLicense' => 0];
        $response = $this->json('POST', url('api/admin/callbackdelete?call[]=81&call[]=82&call[]=83'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Deleted 3 callback(s).']);
        $response->assertJson(['data' => 3]);
    }

    public function test_callbacksDelete_withIdsAndIsLicenseAndNoRecords_shouldReciveInvalidRecordSelected()
    {
        $this->withoutMiddleware();
        $data = ['isLicense' => 1];
        $response = $this->json('POST', url('api/admin/callbackdelete?call[]=81&call[]=82&call[]=83'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Callback could not be deleted because of this reason: Invalid record or database error.']);
    }

    public function test_callbacksDelete_withIdsAndIsLicense0AndNoRecords_shouldReciveInvalidRecordSelected()
    {
        $this->withoutMiddleware();
        $data = ['isLicense' => 0];
        $response = $this->json('POST', url('api/admin/callbackdelete?call[]=81&call[]=82&call[]=83'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Callback could not be deleted because of this reason: Invalid record or database error.']);
    }

    public function test_callbacksDelete_withIdsAndIsLicenseAndNoRecords_shouldReciveNoRecordSelected()
    {
        $this->withoutMiddleware();
        $data = ['isLicense' => 1];
        $response = $this->json('POST', url('api/admin/callbackdelete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Callback could not be deleted because of this reason: No record selected.']);
    }

    public function test_callbacksDelete_withIdsAndIsLicense0AndNoRecords_shouldReciveNoRecordSelected()
    {
        $this->withoutMiddleware();
        $data = ['isLicense' => 0];
        $response = $this->json('POST', url('api/admin/callbackdelete'), $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Callback could not be deleted because of this reason: No record selected.']);
    }
}
