<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\ProductRequest;
use App\Models\AflProducts;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;


/**
 * Consist of functionalities for the Product page in Auto Faveo licenser 
 * Class ProductsController
 * @package App\Http\Controllers\Admin
 */
class ProductsController extends Controller
{


    
    /**
     * stores the product details into the database
     * @param ProductRequest $request
     *
     * @return response that a product details is added
    */
    public function store(ProductRequest $request){

       $date = Carbon::now();
       $product = new AflProducts(array(           
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
     return \successResponse(Lang::get('lang.Product_Add'),$product,201);

    }



     /**
     * Shows the product details from the database
     * @param
     *
     * @return array of all the products that is present in the database
    */
    public function show(){
        $products = AflProducts::all();
        return \successResponse(Lang::get('lang.Product_Show'),$products,200);
    }


    
     /**
     * Deletes the product details from the database by using product id
     * @param $product_id
     *
     * @return response that a product has been deleted 
    */
     public function destroy($product_id)
     {
        $product = AflProducts::where('product_id',$product_id)->firstOrFail();
        $product->delete();
        return \successResponse(Lang::get('lang.Product_Destroy'),$product,200);
     }

        /* public function edit($product_id)
       {

        $ticket = Ticket::where('product_id',$product_id)->firstOrFail();
        return view('',compact('ticket'));

       }*/

        

         
     /**
     * Updates the product details from the database by using the product title
     * 
     * @param Request $request
     * @param $product_title
     *
     * @return response that a product has been Updated
    */
     public function update(Request $request, $product_title)
       {
        $product = AflProducts::where('product_title',$product_title)->firstOrFail();
        $product->product_title = $request->get('product_title');
        $product->product_description = $request->get('product_description');
        $product->product_sku = $request->get('product_sku');
        $product->product_url_homepage = $request->get('product_url_homepage');
        $product->product_version = $request->get('product_version');
        $product->product_status = $request->get('product_status');
        $product->save();
          return \successResponse(Lang::get('lang.Product_Update'),$product,201);

       
    }

}
