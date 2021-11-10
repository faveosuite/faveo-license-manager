<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\ProductRequest;
use App\Models\afl_products;
use Illuminate\Http\Response;

class ProductsController extends Controller
{
    public function store(ProductRequest $request){

       $date = Carbon::now();
       $product = new afl_products(array(           
            'product_title'=> $request->get('product_title'),
            'product_description' => $request->get('product_description'),
            'product_sku' => $request->get('product_sku'),
            'product_url_homepage'=>$request->get('product_url_homepage'),
            'product_url_download'=>$request->get('product_url_download') ,
            'product_date'=> $date->toDateString(),
            'product_version'=> $request->get('product_version'),
            'product_envato_id'=> $request->get('product_envato_id'),
            'product_status'=> $request->get('product_status')
       ));
       $product->save();
       return response([
           'message'=> 'Product Updated',
           'product'=> $product
          
       ]);

    }
    public function show(){
        $products = afl_products::all();
        return response($products);
    }

     public function destroy($product_title)
    {
        $product = afl_products::where('product_title',$product_title)->firstOrFail();
        $product->delete();
        return response([$product_title,'Product is deleted']);
    }

}
