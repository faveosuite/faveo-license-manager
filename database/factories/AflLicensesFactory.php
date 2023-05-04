<?php

namespace Database\Factories;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

class AflLicensesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => 1,
            'client_id' => 1,
            'license_code' => 'Str::random(10)',
            'license_require_domain' => 0,
            'license_limit' => 2,
            'license_date' => now(),
            'license_cancel_date' => now(),
            'license_expire_date' => now(),
            'license_expire_email_date' => now(),
            'license_updates_date' => now(),
            'license_updates_email_date' => now(),
            'license_support_date' => now(),
            'license_support_email_date' => now(),
            'license_envato' => 0,
            'license_status' => 1,
        ];
    }
}
