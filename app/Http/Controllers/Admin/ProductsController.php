<?php

namespace App\Http\Controllers\Admin;

use App\Models\AflProducts;
use App\Models\AflApiKeys;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;

/**
 * Consist of functionalities for the Product page in Auto Faveo licenser
 * Class ProductsController
 * @package App\Http\Controllers\Admin
 */
class ProductsController extends Controller
{
    public function __construct(){
        $this->ip_address=request()->server('REMOTE_ADDR');
    }

    /**
     * stores the product details into the database
     * @param ProductRequest $request
     * @param $api_key_secret
     * @param $product_title
     * @param $product_sku
     * @param $product_status
     * @param $product_description
     * @param $product_url_homepage
     * @param $product_url_download
     * @param $product_version
     * @param $product_envato_id
     *
     * @return response that a product details is added with a success response
    */
public function productAdd(ProductRequest $request)
     {
        $api_error_detected=0;
        $added_records =0;
        $api_key_secret = $request->get('api_key_secret');
        $product_title = $request->get('product_title');
        $product_sku = $request->get('product_sku');
        $product_status = $request->get('product_status');
        $product_description= $request->get('product_description');
        $product_url_homepage= $request->get('product_url_homepage');
        $product_url_download= $request->get('product_url_download');
        $product_version= $request->get('product_version');
        $product_envato_id= $request->get('product_envato_id');

        $api_key = new ApiKeysController();
        $api_action_success=$api_key->apiKeyCheck($api_key_secret,$this->ip_address);
        $optional_api_parameters_array=array("product_description", "product_url_homepage", "product_url_download", "product_version", "product_envato_id"); //optional API parameters for this page
         foreach ($optional_api_parameters_array as $optional_api_parameter) //in case some required parameter was not submitted, set its value empty to prevent "undefined variable" errors
         {
             if (!isset($$optional_api_parameter))
             {
                 $$optional_api_parameter="";
             }
         }
          if (!empty($product_title) && !empty($product_sku) && aflValidateIntegerValue($product_status, 0, 2) && $api_action_success==1)
                {
                if (!empty($product_url_homepage) && !filter_var($product_url_homepage, FILTER_VALIDATE_URL))
                    {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.error_producturl'),400);
                    }

                if (!empty($product_envato_id) && !aflValidateIntegerValue($product_envato_id))
                    {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.error_product_envato'),400);
                    }

                if ($api_error_detected!=1)
                    {
                    $product_date=date("Y-m-d");
                    try{
                        $in=DB::table('afl_products')->insertOrIgnore([
                            'product_title' => $product_title,
                            'product_description' => $product_description,
                            'product_sku' => $product_sku,
                            'product_url_homepage' => $product_url_homepage,
                            'product_url_download' => $product_url_download,
                            'product_date' => $product_date,
                            'product_version' => $product_version,
                            'product_envato_id' => $product_envato_id,
                            'product_status' => $product_status
                        ]);
                        $added_records +=1;
                    }
                    catch(Exception $e){
                        $added_records+=0;
                    }
                    if (!aflValidateIntegerValue($added_records))
                        {
                        $api_error_detected=1;
                         return errorResponse(Lang::get('lang.invalid'),400);
                        }
                    else
                        {
                        $api_action_success=1;
                        return successResponse(Lang::get('lang.Product_Add'),$in,200);
                        }
                    }
                }
                else
                {
                    return errorResponse(Lang::get('lang.invalid'),400);
                }
    }

     /**
     * Shows the product details from the database
     * @param
     *
     * @return array of all the products that is present in the database
    */
    public function show(){

        $products = productArray();
        return successResponse(Lang::get('lang.Product_Show'),$products,200);

    }

     /**
     * Deletes the product details from the database by using product id
     * @param $product_id
     *
     * @return response that a product has been deleted  with it's cascaded values
    */
    //delete product
public function deleteProduct(Request $request)
    {
    $removed_records=0;
    $product_id = $request->get('product_id');
    $api_key_secret = $request->get('api_key_secret');
    $api_key = new ApiKeysController();
    $api_action_success = $api_key->apiKeyCheck($api_key_secret,$this->ip_address);
    if (aflValidateIntegerValue($product_id) && $api_action_success==1)
        {
        DB::beginTransaction();//mysqli_begin_transaction($GLOBALS["mysqli"]);
        $transaction_errors_array=array();
        try{
        AflCallbacks::where('product_id',$product_id)->delete();//Delete all callback for that product
        AflInstallations::where('product_id',$product_id)->delete();//Delete all installations for that product
        AflLicenses::where('product_id',$product_id)->delete();//Delete all licenses for that product
        $removed_records+=AFlProducts::where('product_id',$product_id)->delete();//Delete the product
        DB::commit();
        }
        catch(Exception $e){
             $transaction_errors_array[]=$e->getMessage();
             DB::rollBack();
             $removed_records=0;
             return errorResponse(Lang::get('lang.invalid'),400);
        }
        }
    return successResponse(Lang::get('lang.delete'),$removed_records,200);


    }

       /**
     * Updates the product details into the database
     * @param ProductRequest $request
     * @param $api_key_secret
     * @param $product_id
     * @param $product_title
     * @param $product_sku
     * @param $product_status
     * @param $product_description
     * @param $product_url_homepage
     * @param $product_url_download
     * @param $product_version
     * @param $product_envato_id
     *
     * @return response that a product details of the records found is Updated with a success response
    */

Public function productUpdate(Request $request){

        $api_action_success=0;
        $api_error_detected=0;
        $updated_records = 0;
        $api_key_secret = $request->get('api_key_secret');
        $product_id = $request->get('product_id');
        $product_title = $request->get('product_title');
        $product_sku = $request->get('product_sku');
        $product_status = $request->get('product_status');
        $product_description= $request->get('product_description');
        $product_url_homepage= $request->get('product_url_homepage');
        $product_url_download= $request->get('product_url_download');
        $product_version= $request->get('product_version');
        $product_envato_id= $request->get('product_envato_id');

        if (empty($product_id) || !aflValidateIntegerValue($product_id) || empty($rows_array=AflProducts::where('product_id',$product_id)->get()->toArray())) //invalid record
        {
          return errorResponse(Lang::get('lang.invalid'),404);
        }
        $api_key = new ApiKeysController();
        $api_action_success=$api_key->apiKeyCheck($api_key_secret,$this->ip_address);
         if (!empty($product_title) && !empty($product_sku) && aflValidateIntegerValue($product_status, 0, 2) && $api_action_success==1)
                {
                if (!empty($product_url_homepage) && !filter_var($product_url_homepage, FILTER_VALIDATE_URL))
                    {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.url_error'),400);
                    }

                if (!empty($product_envato_id) && !aflValidateIntegerValue($product_envato_id))
                    {
                    $api_error_detected=1;
                    return errorResponse(Lang::get('lang.envato_error'),400);
                    }

                if ($api_error_detected!=1)
                    {
                    if (!aflValidateIntegerValue($product_envato_id))
                        {
                        $product_envato_id=null;
                        }
                    $updated_records+=DB::table('afl_products')
                                      ->where('product_id',$product_id)
                                      ->update([
                                          'product_title'=> $product_title,
                                          'product_description'=> $product_description,
                                          'product_sku'=> $product_sku,
                                          'product_url_homepage'=> $product_url_homepage,
                                          'product_url_download'=> $product_url_download,
                                          'product_version'=> $product_version,
                                          'product_envato_id'=> $product_envato_id,
                                          'product_status'=> $product_status
                                      ]);
                    if (!aflValidateIntegerValue($updated_records))
                        {
                        return errorResponse(Lang::get('lang.error'),400);
                        }
                    else
                        {
                        return successResponse(Lang::get('lang.Product_Update'),$updated_records,200);
                        }
                    }
                }
                else{
                    return errorResponse(Lang::get('lang.invalid'),400);
                }
    }
}
