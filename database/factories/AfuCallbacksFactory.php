<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AfuCallbacksFactory extends Factory
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
            'version_id' => 2,
            'callback_type' => 'version check',
            'callback_path' => 'script/signature',
            'callback_ip' => '106.51.140.178',
            'callback_date_time' => now(),
            'callback_status' => 1,
        ];
    }
}
