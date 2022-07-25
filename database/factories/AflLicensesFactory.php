<?php

namespace Database\Factories;

use App\Models\AflLicenses;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflLicensesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflLicenses::class;

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
