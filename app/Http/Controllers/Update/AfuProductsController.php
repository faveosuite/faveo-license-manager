<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Admin\ApiKeysController;
use App\Http\Controllers\Controller;
use App\Models\AfuCallbacks;
use App\Models\AfuInstallations;
use App\Models\AfuProducts;
use App\Models\AfuVersions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class AfuProductsController extends Controller
{
    public function __construct()
    {
        $this->ip_address = request()->server('REMOTE_ADDR');
    }

    /**
     * stores the product details into the database
     *
     * @param  Request  $request
     * @return response that a product details is added with a success response
     */
    public function productUpdateAdd(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_id' => $request->get('product_id'),
            'product_title' => $request->get('product_title'),
            'product_sku' => $request->get('product_sku'),
            'product_short_description' => $request->get('product_short_description'),
            'product_full_description' => $request->get('product_full_description'),
            'product_key' => $request->get('product_key'),
            'product_status' => $request->get('product_status'),
            'product_url_homepage' => $request->get('product_url_homepage'),
            'product_url_order' => $request->get('product_url_order'),
            'product_price' => $request->get('product_price'),
            'product_max_active_versions' => $request->get('product_max_active_versions'),
        ];

        $result = $this->processProductAdd($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.Product_Add'), $result['data'], 200);
    }

    public function processProductAdd(array $data, string $ipAddress = ''): array
    {
        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_title = $data['product_title'] ?? null;
        $product_sku = $data['product_sku'] ?? null;
        $product_key = $data['product_key'] ?? null;
        $product_status = $data['product_status'] ?? null;
        $product_url_homepage = $data['product_url_homepage'] ?? null;

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (empty($product_title) || empty($product_sku) || ! aflValidateIntegerValue($product_status, 0, 2)) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        if (! empty($product_url_homepage) && ! filter_var($product_url_homepage, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => Lang::get('lang.error_producturl'), 'data' => [], 'status_code' => 400];
        }

        $product_date = date('Y-m-d');

        try {
            $in = DB::table('afu_products')->insertOrIgnore([
                'product_id' => $data['product_id'] ?? null,
                'product_title' => $product_title,
                'product_sku' => $product_sku,
                'product_short_description' => $data['product_short_description'] ?? '',
                'product_full_description' => $data['product_full_description'] ?? '',
                'product_key' => $product_key ?? str_random(16),
                'product_url_homepage' => $product_url_homepage ?? '',
                'product_url_order' => $data['product_url_order'] ?? '',
                'product_price' => $data['product_price'] ?? '',
                'product_date' => $product_date,
                'product_status' => $product_status,
                'product_max_active_versions' => $data['product_max_active_versions'] ?? '',
            ]);

            if (! aflValidateIntegerValue($in)) {
                return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
            }

            return ['success' => true, 'data' => $in, 'message' => ''];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }
    }

    public function deleteUpdateProduct(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_id' => $request->get('product_id'),
            'soft_delete' => $request->get('soft_delete'),
        ];

        $result = $this->processProductDelete($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.delete'), $result['data'], 200);
    }

    public function processProductDelete(array $data, string $ipAddress = ''): array
    {
        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $soft_delete = $data['soft_delete'] ?? null;
        $removed_records = 0;

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (! aflValidateIntegerValue($product_id)) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        if ($soft_delete === 0) {
            DB::beginTransaction();
            try {
                AfuCallbacks::where('product_id', $product_id)->delete();
                AfuInstallations::where('product_id', $product_id)->delete();
                AfuVersions::where('product_id', $product_id)->delete();
                $removed_records += AfuProducts::where('product_id', $product_id)->forceDelete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
            }
        } else {
            AfuProducts::where('product_id', $product_id)->update(['product_status' => 0]);
            $removed_records += AfuProducts::where('product_id', $product_id)->delete();
        }

        return ['success' => true, 'data' => $removed_records, 'message' => ''];
    }

    public function productUpdateUpdate(Request $request)
    {
        $data = [
            'api_key_secret' => $request->get('api_key_secret'),
            'product_id' => $request->get('product_id'),
            'product_title' => $request->get('product_title'),
            'product_sku' => $request->get('product_sku'),
            'product_short_description' => $request->get('product_short_description'),
            'product_full_description' => $request->get('product_full_description'),
            'product_key' => $request->get('product_key'),
            'product_status' => $request->get('product_status'),
            'product_url_homepage' => $request->get('product_url_homepage'),
            'product_url_order' => $request->get('product_url_order'),
            'product_price' => $request->get('product_price'),
            'product_max_active_versions' => $request->get('product_max_active_versions'),
        ];

        $result = $this->processProductUpdate($data, $this->ip_address);

        if (! $result['success']) {
            return errorResponse($result['message'], $result['status_code']);
        }

        return successResponse(Lang::get('lang.Product_Update'), $result['data'], 200);
    }

    public function processProductUpdate(array $data, string $ipAddress = ''): array
    {
        $api_key_secret = $data['api_key_secret'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $product_title = $data['product_title'] ?? null;
        $product_sku = $data['product_sku'] ?? null;
        $product_key = $data['product_key'] ?? null;
        $product_status = $data['product_status'] ?? null;
        $product_url_homepage = $data['product_url_homepage'] ?? null;

        if (empty($product_id) || ! aflValidateIntegerValue($product_id) || empty(AfuProducts::where('product_id', $product_id)->get()->toArray())) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 404];
        }

        if ($api_key_secret) {
            $api_key = new ApiKeysController();
            $api_key->apiKeyCheck($api_key_secret, $ipAddress);
        }

        if (empty($product_title) || empty($product_sku) || ! aflValidateIntegerValue($product_status, 0, 2)) {
            return ['success' => false, 'message' => Lang::get('lang.invalid'), 'data' => [], 'status_code' => 400];
        }

        if (! empty($product_url_homepage) && ! filter_var($product_url_homepage, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => Lang::get('lang.url_error'), 'data' => [], 'status_code' => 400];
        }

        $updated_records = DB::table('afu_products')
            ->where('product_id', $product_id)
            ->update([
                'product_title' => $product_title,
                'product_sku' => $product_sku,
                'product_short_description' => $data['product_short_description'] ?? '',
                'product_full_description' => $data['product_full_description'] ?? '',
                'product_key' => $product_key ?? '',
                'product_url_homepage' => $product_url_homepage ?? '',
                'product_url_order' => $data['product_url_order'] ?? '',
                'product_price' => $data['product_price'] ?? '',
                'product_status' => $product_status,
                'product_max_active_versions' => $data['product_max_active_versions'] ?? '',
            ]);

        if (! aflValidateIntegerValue($updated_records)) {
            return ['success' => false, 'message' => Lang::get('lang.nothing_updated'), 'data' => [], 'status_code' => 400];
        }

        return ['success' => true, 'data' => $updated_records, 'message' => ''];
    }

    public function getProducts(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $searchQuery = $request->input('search_query');
        $sortOrder= $request->input('sort_order','desc');
        $sortField = $request->input('sort_field','product_id');
        $product = AfuProducts::orderBy($sortField, $sortOrder)
            ->where('product_title','LIKE', '%' . $searchQuery . '%')
            ->paginate($perPage, ['*'], 'page', $page);
        return successResponse('',$product);
    }
}
