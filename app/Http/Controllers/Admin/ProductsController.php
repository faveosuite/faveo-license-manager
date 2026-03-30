<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Update\AfuProductsController;
use App\Http\Requests\ProductRequest;
use App\Models\AflApiKeys;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\AfuProducts;
use App\Models\InstallationLogs;
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
     * @return response that a product details is added with a success response
     */
    public function productAdd(ProductRequest $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_title' => $request->get('product_title'),
            'product_sku' => $request->get('product_sku'),
            'product_status' => $request->get('product_status'),
            'product_description' => $request->get('product_description'),
            'product_url_homepage' => $request->get('product_url_homepage'),
            'product_url_download' => $request->get('product_url_download'),
            'product_version' => $request->get('product_version'),
            'product_envato_id' => $request->get('product_envato_id'),
        ];

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        $result = $this->processProductAdd($data, $ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.Product_Add'), $result['data'], 200);
    }

    public function processProductAdd(array $data, string $ipAddress = ''): array
    {
        $api_action_success = 0;
        $api_error_detected = 0;
        $added_records = 0;

        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_title = $data['product_title'] ?? null;
        $product_sku = $data['product_sku'] ?? null;
        $product_status = $data['product_status'] ?? null;
        $product_description = $data['product_description'] ?? null;
        $product_url_homepage = $data['product_url_homepage'] ?? null;
        $product_url_download = $data['product_url_download'] ?? null;
        $product_version = $data['product_version'] ?? null;
        $product_envato_id = $data['product_envato_id'] ?? null;

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if ($api_key_secret && ! $api_action_success) {
            return ['success' => false, 'message' => Lang::get('lang.invalid_api_key'), 'data' => [], 'status_code' => 404];
        }

        if (! empty($product_title) && ! empty($product_sku) && aflValidateIntegerValue($product_status, 0, 2)) {
            if (! empty($product_url_homepage) && ! filter_var($product_url_homepage, FILTER_VALIDATE_URL)) {
                return ['success' => false, 'message' => Lang::get('lang.error_producturl'), 'data' => [], 'status_code' => 400];
            }

            if (! empty($product_envato_id) && ! aflValidateIntegerValue($product_envato_id)) {
                return ['success' => false, 'message' => Lang::get('lang.error_product_envato'), 'data' => [], 'status_code' => 400];
            }

            if (! aflValidateIntegerValue($product_envato_id)) {
                $product_envato_id = null;
            }

            $product_date = date('Y-m-d');

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
            } catch (\Exception $e) {
                $added_records += 0;
            }

            if (! aflValidateIntegerValue($added_records)) {
                return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
            }

            return ['success' => true, 'data' => $in, 'message' => ''];
        }

        return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
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
        $sortOrder = $request->input('sort_order', 'desc');
        $sortField = $request->input('sort_field', 'product_id');
        $filter = $request->input('filter_field', 'active');

        $products = $this->buildProductQuery($filter, $searchQuery)
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        $products->getCollection()->transform(function ($product) {
            $product->versions = $product->product_latest_version;
            $product->versions_count = $product->product_version_count;
            return $product;
        });

        return successResponse(Lang::get('lang.Product_Show'), $products, 200);
    }

    /**
     * Deletes the product details from the database by using product id
     *
     * @param $product_id
     * @return response that a product has been deleted with it's cascaded values
     */
    public function deleteProduct(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_id' => $request->get('product_id'),
            'soft_delete' => $request->get('soft_delete'),
        ];

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        $result = $this->processProductDelete($data, $ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.Product_Destroy'), $result['data'], 200);
    }

    public function processProductDelete(array $data, string $ipAddress = ''): array
    {
        $api_action_success = 0;
        $removed_records = 0;

        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $soft_delete = $data['soft_delete'] ?? null;

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (! aflValidateIntegerValue($product_id)) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        if ($soft_delete === 0) {
            DB::beginTransaction();
            try {
                $licenses = AflLicenses::where('product_id', $product_id)->pluck('license_code');

                foreach ($licenses as $license_code) {
                    InstallationLogs::where('license_code', $license_code)->delete();
                }

                AflCallbacks::where('product_id', $product_id)->delete();
                AflInstallations::where('product_id', $product_id)->delete();
                AflLicenses::where('product_id', $product_id)->delete();
                $removed_records += AflProducts::where('product_id', $product_id)->forceDelete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
            }
        } else {
            AflProducts::where('product_id', $product_id)->update(['product_status' => 0]);
            $removed_records += AflProducts::where('product_id', $product_id)->delete();
        }

        return ['success' => true, 'data' => $removed_records, 'message' => ''];
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
     * @param  Request  $request
     * @return response that a product details of the records found is Updated with a success response
     */
    public function productUpdate(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_id' => $request->get('product_id'),
            'product_title' => $request->get('product_title'),
            'product_sku' => $request->get('product_sku'),
            'product_status' => $request->get('product_status'),
            'product_description' => $request->get('product_description'),
            'product_url_homepage' => $request->get('product_url_homepage'),
            'product_url_download' => $request->get('product_url_download'),
            'product_version' => $request->get('product_version'),
            'product_envato_id' => $request->get('product_envato_id'),
        ];

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        $result = $this->processProductUpdate($data, $ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.Product_Update'), $result['data'], 200);
    }

    public function processProductUpdate(array $data, string $ipAddress = ''): array
    {
        $api_action_success = 0;
        $api_error_detected = 0;

        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $product_title = $data['product_title'] ?? null;
        $product_sku = $data['product_sku'] ?? null;
        $product_status = $data['product_status'] ?? null;
        $product_description = $data['product_description'] ?? null;
        $product_url_homepage = $data['product_url_homepage'] ?? null;
        $product_url_download = $data['product_url_download'] ?? null;
        $product_version = $data['product_version'] ?? null;
        $product_envato_id = $data['product_envato_id'] ?? null;

        if (empty($product_id) || ! aflValidateIntegerValue($product_id) || empty(AflProducts::where('product_id', $product_id)->get()->toArray())) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 404];
        }

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_action_success = $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (! empty($product_title) && ! empty($product_sku) && aflValidateIntegerValue($product_status, 0, 2)) {
            if (! empty($product_url_homepage) && ! filter_var($product_url_homepage, FILTER_VALIDATE_URL)) {
                return ['success' => false, 'message' => Lang::get('lang.url_error'), 'data' => [], 'status_code' => 400];
            }

            if (! empty($product_envato_id) && ! aflValidateIntegerValue($product_envato_id)) {
                return ['success' => false, 'message' => Lang::get('lang.envato_error'), 'data' => [], 'status_code' => 400];
            }

            if (! aflValidateIntegerValue($product_envato_id)) {
                $product_envato_id = null;
            }

            $updated_records = DB::table('afl_products')
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
                return ['success' => false, 'message' => Lang::get('lang.nothing_updated'), 'data' => [], 'status_code' => 400];
            }

            return ['success' => true, 'data' => $updated_records, 'message' => ''];
        }

        return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
    }

    public function addAflAndAfuProduct(ProductRequest $request)
    {
        try {
            $response = $this->productAdd($request);
            $response = json_decode($response->getContent());
            if($response->success == true) {
                $productId = AflProducts::where('product_sku', $request->get('product_sku'))->pluck('product_id')->first();
                if ($productId) {
                    $afuResponse = $this->addNewProductToAUS($request, $productId);
                    if($afuResponse->success == false){
                        return errorResponse(Lang::get('lang.invalid'), 400);
                    }
                    return successResponse(Lang::get('lang.Product_Add'));
                } else {
                    return errorResponse(Lang::get('lang.invalid'), 400);
                }
            }
            else{
                return errorResponse($response->message, 400);
            }

        } catch (\Exception $e) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    private function addNewProductToAUS($request,$productId)
    {
        try {
            $key = str_random(16);

            $customRequest = new Request(array_merge($request->all(), ['product_key' => $key,'product_id' => $productId]));

            $afuProduct = app(AfuProductsController::class);
            $response = $afuProduct->productUpdateAdd($customRequest);

            return json_decode($response->getContent());

        } catch (\Exception $ex) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    public function updateAflAndAfuProduct(Request $request)
    {
        try {
            $responseFromProduct = $this->productUpdate($request);
            $response = json_decode($responseFromProduct->getContent());
            if ($response->success == true ) {
                $this->updateProductToAUS($request);
                return successResponse(Lang::get('lang.Product_Update'));
            } else {
                return errorResponse($response->message, 400);
            }

        } catch (\Exception $e) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    private function updateProductToAUS($request)
    {
        try {
            $key = AfuProducts::where('product_id', $request->get('product_id'))->pluck('product_key')->first();

            $data = $request->all();

            if(empty($request->get('product_url_homepage'))){
                unset($data['product_url_homepage']);
            }

            $data['product_key'] = $key;

            $afuProduct = app(AfuProductsController::class);

            $response = $afuProduct->productUpdateUpdate(new Request($data));

            return json_decode($response->getContent());
        } catch (\Exception $ex) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    public function deleteAflAndAfuProduct(Request $request)
    {
        try {
            $responseFromProduct = $this->deleteProduct($request);
            $response = json_decode($responseFromProduct->getContent());
            if ($response->success == true ) {
                $afuProduct = app(AfuProductsController::class);
                $afuProduct->deleteUpdateProduct($request);
                return successResponse(Lang::get('lang.product_suspended'));
            } else {
                return errorResponse($response->message, 400);
            }

        } catch (\Exception $e) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }
    }

    private function buildProductQuery($filter, $searchQuery)
    {
        $products = AflProducts::select('product_id', 'product_title', 'product_sku', 'product_status')
            ->withCount(['licenses', 'installations']);

        if ($filter === 'suspended') {
            $products->onlyTrashed();
        } elseif ($filter === 'active') {
            $products->whereNull('deleted_at');
        }

        if ($searchQuery) {
            $products->where(function ($query) use ($searchQuery) {
                $query->where('product_title', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('product_sku', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('product_status', 'LIKE', '%' . statusFormatter($searchQuery) . '%');
            });
        }

        return $products;
    }

    /**
     * Restore a suspended product (It restores the product from both AfuProducts and AflProducts)
     *
     * @param Request $request
     * @return Response
     */
    public function restoreSuspendedProduct(Request $request)
    {
        $product_id = $request->input('product_id');
        $api_key_secret = $request->input('api_key_secret');
        $api_key = new ApiKeysController();

        if (null !== (request()->server('REMOTE_ADDR'))) {
            $ip_address = request()->server('REMOTE_ADDR');
        } else {
            $ip_address = $request->ip();
        }

        if (!aflValidateIntegerValue($product_id) || !$api_key->apiKeyCheck($api_key_secret, $ip_address)) {
            return errorResponse(Lang::get('lang.invalid'), 400);
        }

        $aflProduct = AflProducts::onlyTrashed()->find($product_id);
        $afuProduct = AfuProducts::onlyTrashed()->find($product_id);

        if (empty($aflProduct) || empty($afuProduct)) {
            return errorResponse(Lang::get('lang.invalid_product'), 400);
        }

        $aflProduct->restore();
        $afuProduct->restore();

        $aflProduct->product_status = 1;
        $afuProduct->product_status = 1;

        $aflProduct->save();
        $afuProduct->save();

        return successResponse(Lang::get('lang.product_restored'), 1, 200);
    }

    public function getProductIdbyKey(Request $request)
    {
        return AfuProducts::where('product_key', $request->product_key)->value('product_id');
    }
}
