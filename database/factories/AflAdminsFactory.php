<?php

namespace Database\Factories;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflAdminsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create();
        return [
            'admin_fname' => 'Sandesh',
            'admin_lname' => 'Menath',
            'admin_email' => $faker->email,
            'admin_password' => '$2y$04$05PqD4ZF.l2RGZPnmBowDOXMbfyXvQrExZ28BR60zlvF8VUv32.1m',
            'admin_date' => now(),
            'admin_status' => 1,
        ];
    }
}
