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
        //this factory ois used to create admin data in clients table
        $faker = Faker::create();
        return [
            'client_fname' => 'admin',
            'client_lname' => 'admin',
            'client_email' => $faker->email,
            'client_password' => '$2y$10$CPDb8Ck93jBsgRke67TcFuRzkf8PwAF1CQVI2fRIJgasAdvHwPr/S',
            'client_role' => 'admin',
            'client_active_date' => now(),
            'client_status' => 1,
        ];
    }
}
