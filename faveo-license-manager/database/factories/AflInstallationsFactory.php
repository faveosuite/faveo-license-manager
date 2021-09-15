<?php

namespace Database\Factories;

use App\Models\AflInstallations;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflInstallationsFactory extends Factory
{
   /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflInstallations::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => 100,
            'client_id' => null,
            'license_code' => 'CH2NW4MI0OTL0002',
            'installation_ip' => '106.51.140.178',
            'installation_domain' => 'sandesh.com',
            'installation_disable_ip_verification' => 0,
            'installation_date' => now(),
            'installation_status' => 1,
            'installation_hash' => 'd991ff928bc03e7a60fee65ea0bb135f72744fde248761f6d266a6ff9e6941ce'
        ];
    }
}
