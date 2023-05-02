<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AfuVersionsFactory extends Factory
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
            'version_number' => 'v7.1.1',
            'version_install_file' => 'storage/public/app/install.zip',
            'version_upgrade_file' => 'upgrade.zip',
            'version_install_limit' => 100,
            'version_install_count' => 1,
            'version_upgrade_limit' => 100,
            'version_upgrade_count' => 1,
            'version_date' => now(),
            'version_comments' => 'This is a version comment',
            'version_status' => 1,
            'expired' => 1,
        ];
    }
}
