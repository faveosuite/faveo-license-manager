<?php

namespace Tests\Unit\Backend\Admin\Views;

use App\Models\AfuCallbacks;
use App\Models\AfuVersions;
use App\Models\AflCallbacks;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VersionsViewControllerTest extends TestCase
{
    use DatabaseTransactions;
    private $versionId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createVersionDetails();
    }

    protected function createVersionDetails()
    {
        $version = AfuVersions::factory()->create();
        AfuCallbacks::factory()->create(['version_id' => $version->version_id]);
        AfuCallbacks::factory()->create(['version_id' => $version->version_id]);
        $this->versionId = $version->version_id;
    }

    public function test_can_retrieve_version_info()
    {
        $response = $this->get('api/admin/versionView/' . $this->versionId);
        $response->assertStatus(200);
        $version = json_decode($response->content())->data;
        $this->assertNotNull($version);
    }

    public function test_callbacks_belong_to_correct_version()
    {
        $response = $this->get('api/admin/versionCallbacks/' . $this->versionId);
        $response->assertStatus(200);
        $versionCallbacks = json_decode($response->content())->data->data;
        $this->assertNotNull($versionCallbacks);
        $this->assertCount(2, $versionCallbacks);
    }

    public function test_empty_callbacks_for_new_version()
    {
        $version = AfuVersions::factory()->create(['version_number' => "1.0.0"]);
        $response = $this->get('api/admin/versionCallbacks/' . $version->version_id);
        $response->assertStatus(200);
        $versionCallbacks = json_decode($response->content())->data->data;
        $this->assertCount(0, $versionCallbacks);
    }
}
