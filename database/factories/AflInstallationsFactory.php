<?php

namespace Database\Factories;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

class AflInstallationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [     
                'license_code' => Str::random(10),
                'installation_ip' => Str::random(10),
                'installation_domain' => Str::random(10),
                'installation_disable_ip_verification' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'installation_date' => now(),
                'installation_status' => 1,
                'installation_hash' => Str::random(20),     
        ];
    }
}
