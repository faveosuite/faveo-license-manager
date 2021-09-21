<?php

namespace Database\Factories;

use App\Models\AflBannedHosts;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflBannedHostsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflBannedHosts::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'banned_host_ip' => '109.89.89.22',
            'banned_host_comments' => 'This is a search for a banned host',
            'banned_host_date' => now(),
            'banned_host_blocks'=> 1,
            'banned_host_last_block_date' => '2021-09-01'
        ];
    }
}
