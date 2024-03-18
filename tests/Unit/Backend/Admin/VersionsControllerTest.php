<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\AfuVersions;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VersionsControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createVersions();
    }

    protected function createVersions()
    {
        // Create versions and associated products
        for ($i = 0; $i < 3; $i++) {
            $version = AfuVersions::factory()->create();
        }
    }

    public function test_can_retrieve_versions_list()
    {
        $response = $this->get('api/admin/viewVersions');
        $response->assertStatus(200);
        $versions = json_decode($response->content())->data;
        $this->assertNotNull($versions);
        $this->assertCount(3, $versions->data);
    }

    public function test_can_search_versions()
    {
        $version = AfuVersions::factory()->create(['version_number' => '1.0.0']);
        $response = $this->get('api/admin/viewVersions?search_query=1.0.0');
        $response->assertStatus(200);
        $versions = json_decode($response->content())->data;
        $this->assertNotNull($versions);
        $this->assertCount(1, $versions->data);
        $this->assertEquals('1.0.0', $versions->data[0]->version_number);
    }

    public function test_can_sort_versions()
    {
        AfuVersions::factory()->create(['version_number' => '1.0.0']);
        AfuVersions::factory()->create(['version_number' => '2.0.0']);
        AfuVersions::factory()->create(['version_number' => '3.0.0']);

        $response = $this->get('api/admin/viewVersions?sort_field=version_number&sort_order=asc');
        $response->assertStatus(200);
        $versions = json_decode($response->content())->data;
        $this->assertNotNull($versions);
        $this->assertEquals('1.0.0', $versions->data[0]->version_number);
        $this->assertEquals('2.0.0', $versions->data[1]->version_number);
        $this->assertEquals('3.0.0', $versions->data[2]->version_number);
    }

    public function test_pagination_of_versions()
    {
        for ($i = 0; $i < 15; $i++) {
            AfuVersions::factory()->create();
        }

        $response = $this->get('api/admin/viewVersions?perPage=10&page=2');
        $response->assertStatus(200);
        $versions = json_decode($response->content())->data;
        $this->assertNotNull($versions);
        $this->assertCount(8, $versions->data);
    }
}
