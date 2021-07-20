<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    
    private function postCurl($post_url, $post_info)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_info);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /*
    *  Add New Product
    */
    public function addNewProduct(Request $request,$product_name, $product_sku)
    {
        $url =  $request->get('url');
        $api_key_secret = $request->get('api_key_secret');
        $addProduct = $this->postCurl($url."api/admin/products/add", "api_key_secret=$api_key_secret&api_function=products_add&product_title=$product_name&product_sku=$product_sku&product_status=1");
        dd($addProduct);

    }
}
