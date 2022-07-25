<?php

namespace Database\Factories;

use App\Models\AfuInstallations;
use Illuminate\Database\Eloquent\Factories\Factory;

class AfuInstallationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => 100,
            'version_id' => 100,
            'installation_ip' => '127.0.0.1',
            'installation_path' => 'storage/public/app/index.html',
            'installation_date' => now(),
            'installation_status' => 1,
        ];
    }
}
