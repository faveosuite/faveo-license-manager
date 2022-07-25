<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AflCallbacksFactory extends Factory
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
            'license_code' => 'CH2NW4MI0OTL0002',
            'callback_ip' => '106.51.140.178',
            'callback_date_time' => now(),
            'callback_status' => 1,
        ];
    }
}
