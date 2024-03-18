<?php

namespace Database\Factories;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflClientsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_fname' => 'admin',
            'client_lname' => 'admin',
            'client_password' => '$2y$10$CPDb8Ck93jBsgRke67TcFuRzkf8PwAF1CQVI2fRIJgasAdvHwPr/S',
            'client_role' => 'admin',
            'client_active_date' => now(),
            'client_status' => 1,
            'client_email' => $this->faker->unique()->safeEmail(),
            'client_profile_pic' => $this->faker->imageUrl(),
            'client_address' => $this->faker->address(),
            'client_organization' => $this->faker->company(),
        ];
    }
}
