<?php

namespace Database\Factories;

use App\Models\AflReports;
use Illuminate\Database\Eloquent\Factories\Factory;

class AflReportsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AflReports::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => 14,
            'account_id' => 1,
            'license_code' => 'AK12BJSI9OP3BDJ8',
            'report_date_time' => now(),
            'report_text' =>'The configuration file could not be generated because of this reason: Invalid product, license verification period, license storage type, license file location or MySQL table name.',
            'report_system'=>1,
            'report_status'=>1
        ];
    }
}
