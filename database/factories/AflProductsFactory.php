<?php

namespace Database\Factories;

use App\Models\AflProducts;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflProductsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflProducts::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_title' => 'Helpdesk Product 2',
            'product_sku' => 'FAVEO-UPTST',
            'product_status' => 1,
            'product_description' => 'This is a test product for license manager of faveo',
            'product_url_homepage' => 'www.faveo.com',
            'product_url_download' => 'www.download.com',
            'product_version' => '4.6.2',
            'product_envato_id' => 1,
        ];
    }
}
