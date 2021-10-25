<?php

namespace Database\Factories;

use App\Models\AflAdmins;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflAdminsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflAdmins::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_fname' => 'Sandesh',
            'admin_lname' => 'Menath',
            'admin_email' => 'sandesh@gmail.com',
            'admin_password'=>'$2y$04$05PqD4ZF.l2RGZPnmBowDOXMbfyXvQrExZ28BR60zlvF8VUv32.1m',
            'admin_date' => now(),
            'admin_status' =>1
        ];
    }
}
