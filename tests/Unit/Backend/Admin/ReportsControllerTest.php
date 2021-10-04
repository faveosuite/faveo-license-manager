<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AflReports;
use Tests\TestCase;

class ReportsControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_reports_whenArrayOfIdsIsSent_ShouldRecieveCrackingReportDeleted(){
        $this->withoutMiddleware();
        AflReports::factory()->create(['report_id'=>81]);
        AflReports::factory()->create(['report_id'=>82]);
        $data = ['which_report'=>'Cracking'];
        $response = $this->json('POST',url('api/admin/reports/delete?arr[]=81&arr[]=82'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"Deleted 3 Cracking report(s)."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveLicenseReportDeleted(){
        $this->withoutMiddleware();
        AflReports::factory()->create(['report_id'=>81]);
        AflReports::factory()->create(['report_id'=>82]);
        $data = ['which_report'=>'License'];
        $response = $this->json('POST',url('api/admin/reports/delete?arr[]=81&arr[]=82'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"Deleted 3 License report(s)."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveSystemReportDeleted(){
        $this->withoutMiddleware();
        AflReports::factory()->create(['report_id'=>81]);
        AflReports::factory()->create(['report_id'=>82]);
        $data = ['which_report'=>'System'];
        $response = $this->json('POST',url('api/admin/reports/delete?arr[]=81&arr[]=82'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"Deleted 3 System report(s)."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveUpdateReportDeleted(){
        $this->withoutMiddleware();
        AflReports::factory()->create(['report_id'=>81]);
        AflReports::factory()->create(['report_id'=>82]);
        $data = ['which_report'=>'Update'];
        $response = $this->json('POST',url('api/admin/reports/delete?arr[]=81&arr[]=82'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"Deleted 3 Update report(s)."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveUpdateNoRecordSelected(){
        $this->withoutMiddleware();
        $data = ['which_report'=>'Update'];
        $response = $this->json('POST',url('api/admin/reports/delete'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"Update report(s) could not be deleted because of this reason: No record selected."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveLicenseNoRecordSelected(){
        $this->withoutMiddleware();
        $data = ['which_report'=>'License'];
        $response = $this->json('POST',url('api/admin/reports/delete'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"License report(s) could not be deleted because of this reason: No record selected."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveSystemNoRecordSelected(){
        $this->withoutMiddleware();
        $data = ['which_report'=>'System'];
        $response = $this->json('POST',url('api/admin/reports/delete'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"System report(s) could not be deleted because of this reason: No record selected."]);
    }
    public function test_reports_whenArrayOfIdsIsSent_shouldRecieveCrackingNoRecordSelected(){
        $this->withoutMiddleware();
        $data = ['which_report'=>'Cracking'];
        $response = $this->json('POST',url('api/admin/reports/delete'),$data);
        $response->assertStatus(200);
        $response->assertJson(['message'=>"Cracking report(s) could not be deleted because of this reason: No record selected."]);
    }
}
