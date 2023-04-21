<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Logos>
 */
class LogosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'logo_title' => 'logo',
             'logo_type' => 'sidebar' ,
            'logo_path' => 'public/hello.png1681992788' ,
             ];
    }
}
