<?php

namespace App\Http\Requests;

use App\Models\AflProducts;
use App\Http\Controllers\Admin\ProductsController;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.      
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_title'=> 'string|unique:afl_products,product_title',
            'product_description' => 'string',
            'product_sku' => 'string|unique:afl_products,product_sku',
            'product_url_homepage'=> 'string',
            'product_url_download'=> 'string',
            'product_date'=> 'date',
            'product_version'=> 'string',
            'product_envato_id'=> 'integer|unique:afl_products,product_envato_id',
            'product_status'=> 'boolean'

        ];
    }
}
