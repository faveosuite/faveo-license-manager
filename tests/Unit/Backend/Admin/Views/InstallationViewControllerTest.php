<?php

namespace Tests\Unit\Backend\Admin\Views;

use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InstallationViewControllerTest extends TestCase
{
    use DatabaseTransactions;
    private $installationId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createInstallationDetails();
    }

    protected function createInstallationDetails()
    {
        $installation = AflInstallations::factory()->create(["license_code" => "REDYGUUJHYGTFDXS","installation_domain" => "sadha.localhost/manager"]);
        AflCallbacks::factory()->create(["license_code" => "REDYGUUJHYGTFDXS","callback_domain" => "sadha.localhost/manager"]);
        AflCallbacks::factory()->create(["license_code" => "REDYGUUJHYGTFDXS","callback_domain" => "sadha.localhost/manager"]);
        $this->installationId = $installation->installation_id;
    }

    public function test_can_retrieve_installation_details()
    {
        $response = $this->get('api/admin/installationView/' . $this->installationId);
        $response->assertStatus(200);
        $installation = json_decode($response->content())->data;
        $this->assertNotNull($installation);
    }

    public function test_callbacks_belong_to_correct_installation()
    {
        $response = $this->get('api/admin/installationCallbacks/' . $this->installationId);
        $response->assertStatus(200);
        $installationCallbacks = json_decode($response->content())->data->data;
        $this->assertNotNull($installationCallbacks);
        $this->assertCount(2, $installationCallbacks);
    }

    public function test_empty_callbacks_for_new_installation()
    {
        $installation = AflInstallations::factory()->create(['license_code' => "SDFGHUJIKJBHNJM", 'installation_ip' => "127.0.0.1"]);
        $response = $this->get('api/admin/installationCallbacks/' . $installation->installation_id);
        $response->assertStatus(200);
        $installationCallbacks = json_decode($response->content())->data->data;
        $this->assertCount(0, $installationCallbacks);
    }
}
