<?php

namespace Database\Factories;

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
            'product_id' => fake()->numberBetween(1, 1000),

            'license_code' => \Str::upper(\Str::random(16)),

            'installation_ip' => fake()->ipv4(),

            'installation_domain' => fake()->domainName(),

            'installation_disable_ip_verification' => fake()->boolean() ? 1 : 0,

            'installation_date' => fake()->dateTimeBetween('-1 year', 'now'),

            'installation_status' => fake()->randomElement([0, 1]),

            'installation_hash' => hash('sha256', \Str::random(40)),
        ];
    }
}
