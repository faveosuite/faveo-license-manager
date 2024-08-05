<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\AflApiKeys;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * Consist of functionalities for the Product page in Auto Faveo licenser
 * Class ProductsController
 */
class ProductsController extends Controller
{
    /**
     * stores the product details into the database
     *
     * @param  ProductRequest  $request
     * @param $api_key_secret
     * @param $product_title
     * @param $product_sku
     * @param $product_status
     * @param $product_description
     * @param $product_url_homepage
     * @param $product_url_download
     * @param $product_version
     * @param $product_envato_id
     * @return response that a product details is added with a success response
     */
    public function productAdd(ProductRequest $request)
    {
        $api_action_success = 0;
        $api_error_detected = 0;
        $added_records = 0;

        $api_key_secret = $request->get('api_key_secret');
        $product_title = $request->get('product_title');
        $product_sku = $request->get('product_sku');
        $product_status = $request->get('product_status');
        $product_description = $request->get('product_description');
        $product_url_homepage = $request->get('product_url_homepage');
        $product_url_download = $request->get('product_url_download');
        $product_version = $request->get('product_version');
        $product_envato_id = $request->get('product_envato_id');

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }
        if (! empty($api_key_secret)) {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get()->toArray();

            if (empty($api)) {
                return errorResponse(Lang::get('lang.invalid_api_key'), 404);
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');

                if (! empty($api_ips)) {
                    if (! $api_ips->contains($ip_address)) {
                        $api_error_detected = 1;

                        return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                    } else {
                        $api_action_success = 1;
                    }
                } else {
                    $api_action_success = 1;
                }
            }

            if (! empty($product_title) && ! empty($product_sku) && aflValidateIntegerValue($product_status, 0, 2) && $api_action_success == 1) {
                if (! empty($product_url_homepage) && ! filter_var($product_url_homepage, FILTER_VALIDATE_URL)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.error_producturl'), 400);
                }

                if (! empty($product_envato_id) && ! aflValidateIntegerValue($product_envato_id)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.error_product_envato'), 400);
                }

                if ($api_error_detected != 1) {
                    if (! aflValidateIntegerValue($product_envato_id)) {
                        $product_envato_id = null;
                    }

                    $product_date = date('Y-m-d');

                    //$added_records=doMysqlQuery("INSERT IGNORE INTO apl_products (product_title, product_description, product_sku, product_url_homepage, product_url_download, product_date, product_version, product_envato_id, product_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($product_title, $product_description, $product_sku, $product_url_homepage, $product_url_download, $product_date, $product_version, $product_envato_id, $product_status), array("s", "s", "s", "s", "s", "s", "s", "i", "i"));

                    try {
                        $in = DB::table('afl_products')->insertOrIgnore([
                            'product_title' => $product_title,
                            'product_description' => $product_description,
                            'product_sku' => $product_sku,
                            'product_url_homepage' => $product_url_homepage,
                            'product_url_download' => $product_url_download,
                            'product_date' => $product_date,
                            'product_version' => $product_version,
                            'product_envato_id' => $product_envato_id,
                            'product_status' => $product_status,
                        ]);
                        $added_records += 1;
                    } catch (Exception $e) {
                        $added_records += 0;
                    }
                    if (! aflValidateIntegerValue($added_records)) {
                        $api_error_detected = 1;

                        return errorResponse(Lang::get('lang.invalid'), 400);
                    } else {
                        $api_action_success = 1;

                        return successResponse(Lang::get('lang.Product_Add'), $in, 200);
                    }
                }
            } else {
                return errorResponse(Lang::get('lang.invalid'), 400);
            }
        }
    }

    /**
     * Shows the product details from the database
     *
     * @param
     * @return array of all the products that is present in the database
     */
    public function show(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','product_id');
        $products = AflProducts::select('product_id','product_title','product_sku','product_status')
            ->with(['versions' => function($query) {
                $query->select('version_id','product_id','version_number')->latest();
            }])
            ->withCount(['versions','licenses', 'installations'])
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where('product_title', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('product_sku', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhereHas('versions', function ($query) use ($searchQuery) {
                        $query->where('version_number', 'LIKE', '%' . $searchQuery . '%');
                    });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return successResponse(Lang::get('lang.Product_Show'), $products, 200);
    }

    /**
     * Deletes the product details from the database by using product id
     *
     * @param $product_id
     * @return response that a product has been deleted  with it's cascaded values
     */
    //delete product
    public function deleteProduct(Request $request)
    {
        $api_error_detected = 0;
        $api_action_success = 0;
        $removed_records = 0;
        $product_id = $request->get('product_id');
        $api_key_secret = $request->get('api_key_secret');

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        if (! empty($api_key_secret)) {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get()->toArray();
            if (empty($api)) {
                return errorResponse(Lang::get('lang.invalid_api_key'), 404);
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');

                if (! empty($api_ips)) {
                    if (! $api_ips->contains($ip_address)) {
                        $api_error_detected = 1;

                        return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                    } else {
                        $api_action_success = 1;
                    }
                } else {
                    $api_action_success = 1;
                }
            }

            if (aflValidateIntegerValue($product_id)) {
                DB::beginTransaction(); //mysqli_begin_transaction($GLOBALS["mysqli"]);
                $transaction_errors_array = [];
                try {
                    AFlCallbacks::where('product_id', $product_id)->delete(); //doMysqlQuery("DELETE FROM apl_callbacks WHERE product_id=?", array($product_id), array("i")); //delete callbacks

                    AFlInstallations::where('product_id', $product_id)->delete(); //doMysqlQuery("DELETE FROM apl_installations WHERE product_id=?", array($product_id), array("i")); //delete installations

                    AFlLicenses::where('product_id', $product_id)->delete(); //doMysqlQuery("DELETE FROM apl_licenses WHERE product_id=?", array($product_id), array("i")); //delete licenses

                    $removed_records += AFlProducts::where('product_id', $product_id)->delete(); //$removed_records+=doMysqlQuery("DELETE FROM apl_products WHERE product_id=?", array($product_id), array("i"));

                    DB::commit();
                } catch (Exception $e) {
                    $transaction_errors_array[] = $e->getMessage();
                    DB::rollBack();
                    $removed_records = 0;

                    return errorResponse(Lang::get('lang.invalid'), 400);
                }
            }

            return successResponse(Lang::get('lang.Product_Destroy'), $removed_records, 200);
        }
    }

    public function edit($product_id)
    {
        $product = AflProducts::where('product_id', $product_id)->firstOrFail();

        if (! empty($product)) {
            return successResponse('', ['product' => $product], 200);
        }

        return errorResponse(Lang::get('lang.invalid'), 400);
    }

    /**
     * Updates the product details into the database
     *
     * @param  ProductRequest  $request
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
     * @return response that a product details of the records found is Updated with a success response
     */
    public function productUpdate(Request $request)
    {
        $api_key_secret = $request->get('api_key_secret');
        $product_id = $request->get('product_id');
        $product_title = $request->get('product_title');
        $product_sku = $request->get('product_sku');
        $product_status = $request->get('product_status');
        $product_description = $request->get('product_description');
        $product_url_homepage = $request->get('product_url_homepage');
        $product_url_download = $request->get('product_url_download');
        $product_version = $request->get('product_version');
        $product_envato_id = $request->get('product_envato_id');

        if (empty($product_id) || ! aflValidateIntegerValue($product_id) || empty($rows_array = AflProducts::where('product_id', $product_id)->get()->toArray())) { //invalid record
            return errorResponse(Lang::get('lang.invalid'), 404);
        }

        $api_action_success = 0;
        $api_error_detected = 0;
        $updated_records = 0;
        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        if (! empty($api_key_secret)) {
            $api = AflApiKeys::where('api_key_secret', $api_key_secret)->where('api_key_status', 1)->get()->toArray();
            if (empty($api)) {
                return errorResponse(Lang::get('lang.invalid_api_key'), 404);
            } else {
                $api_ip = new AflApiKeys();
                $api_ips = $api_ip->value('api_key_ip');

                if (! empty($api_ips)) {
                    if (! $api_ips->contains($ip_address)) {
                        $api_error_detected = 1;

                        return errorResponse(Lang::get('lang.Api_Acess_not_allowed'), 400);
                    } else {
                        $api_action_success = 1;
                    }
                } else {
                    $api_action_success = 1;
                }
            }
            if (! empty($product_title) && ! empty($product_sku) && aflValidateIntegerValue($product_status, 0, 2) && $api_action_success == 1) {
                if (! empty($product_url_homepage) && ! filter_var($product_url_homepage, FILTER_VALIDATE_URL)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.url_error'), 400);
                }

                if (! empty($product_envato_id) && ! aflValidateIntegerValue($product_envato_id)) {
                    $api_error_detected = 1;

                    return errorResponse(Lang::get('lang.envato_error'), 400);
                }

                if ($api_error_detected != 1) {
                    if (! aflValidateIntegerValue($product_envato_id)) {
                        $product_envato_id = null;
                    }

                    $updated_records += DB::table('afl_products')
                        ->where('product_id', $product_id)
                        ->update([
                            'product_title' => $product_title,
                            'product_description' => $product_description,
                            'product_sku' => $product_sku,
                            'product_url_homepage' => $product_url_homepage,
                            'product_url_download' => $product_url_download,
                            'product_version' => $product_version,
                            'product_envato_id' => $product_envato_id,
                            'product_status' => $product_status,
                        ]);

                    if (! aflValidateIntegerValue($updated_records)) {
                        $api_error_detected = 1;

                        return errorResponse(Lang::get('lang.nothing_updated'), 400);
                    } else {
                        return successResponse(Lang::get('lang.Product_Update'), $updated_records, 200);
                    }
                }
            } else {
                return errorResponse(Lang::get('lang.invalid'), 400);
            }
        }
    }
}
