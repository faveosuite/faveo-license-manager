<?php

namespace App\Streams\License;

use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\InstallationLogsController;
use App\Http\Controllers\Admin\LicenseController;
use App\Models\AflCallbacks;
use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use App\Models\InstallationLogs;
use App\Http\Controllers\Admin\SearchController;
use App\Streams\RedisStreamProducer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenseStreamHandler
{
    /**
     * Called by ConsumeStreamCommand for each message from the stream.
     * Handles correlation/reply_to dynamically for all events.
     */
    public function handle(array $data, string $messageId): void
    {
        $event = $data['event'] ?? null;
        $payload = $data['payload'] ?? [];
        $replyTo = $payload['reply_to'] ?? null;
        $correlationId = $payload['correlation_id'] ?? null;

        $result = match ($event) {
            'stream_ping' => $this->ping($payload),
            'license_add_product' => $this->addProduct($payload),
            'license_edit_product' => $this->editProduct($payload),
            'license_delete_product' => $this->deleteProduct($payload),
            'license_create' => $this->createLicense($payload),
            'license_update' => $this->updateLicense($payload),
            'license_installation' => $this->updateInstallation($payload),
            'license_deactivate' => $this->deactivateLicense($payload),
            'license_domain_reissue' => $this->reissueLicenses($payload),
            'license_update_code' => $this->updateLicenseCode($payload),
            'license_sync_addon' => $this->syncAddonLicense($payload),
            'license_get_installation_logs' => $this->getInstallationLogs($payload),
            'license_update_installation_logs' => $this->updateInstallationLogs($payload),
            'license_plugin_info' => $this->getPluginInfo($payload),
            'license_search' => $this->searchData($payload),
            default => null,
        };

        if ($result === null) {
            Log::warning("LicenseStreamHandler: unhandled event '{$event}' or empty result");
            return;
        }

        if ($replyTo && $correlationId) {
            $responseProducer = new RedisStreamProducer($replyTo);
            $responseProducer->publish($event . '_response', [
                'correlation_id' => $correlationId,
                'result' => $result,
            ]);
        }
    }

    protected function ping(array $payload): array
    {
        return ['success' => true, 'data' => [], 'message' => 'pong'];
    }

    protected function getPluginInfo(array $payload): array
    {
        $codes = collect($payload['license_codes'] ?? []);

        return ['success' => true, 'data' => collect(new LicenseController()->getLicensePluginData($codes->toArray()))->toArray(), 'message' => ''];
    }

    protected function reissueLicenses(array $payload): array
    {
        $installationPath = $payload['installation_path'] ?? null;
        new InstallationController()->removeInstallation($installationPath);

        return ['success' => true, 'data' => [], 'message' => ''];
    }

    protected function addProduct(array $payload): array
    {
        AflProducts::insertOrIgnore([
            'product_title' => $payload['product_title'],
            'product_sku' => $payload['product_sku'],
            'product_status' => $payload['product_status'] ?? 1,
            'product_date' => $payload['product_date'] ?? now(),
            'product_description' => $payload['product_description'] ?? null,
            'product_url_homepage' => $payload['product_url_homepage'] ?? null,
            'product_url_download' => $payload['product_url_download'] ?? null,
            'product_version' => $payload['product_version'] ?? null,
            'product_envato_id' => $payload['product_envato_id'] ?? null,
        ]);

        return ['success' => true, 'data' => [], 'message' => ''];
    }

    protected function editProduct(array $payload): array
    {
       $updated = AflProducts::where('product_id', $payload['product_id'])
            ->update([
                'product_title' => $payload['product_title'],
                'product_sku' => $payload['product_sku'],
                'product_status' => $payload['product_status'] ?? 1,
                'product_date' => $payload['product_date'] ?? now(),
                'product_description' => $payload['product_description'] ?? null,
                'product_url_homepage' => $payload['product_url_homepage'] ?? null,
                'product_url_download' => $payload['product_url_download'] ?? null,
                'product_version' => $payload['product_version'] ?? null,
                'product_envato_id' => $payload['product_envato_id'] ?? null,
            ]);

        return ['success' => (bool) $updated, 'data' => [], 'message' => ''];
    }
    protected function deleteProduct(array $payload): array
    {
        $product = AflProducts::where('product_id', $payload['product_id'])->first();

        if ($product) {
            $productId = $product->product_id;
            DB::transaction(function () use ($productId) {
                $licenses = AflLicenses::where('product_id', $productId)->pluck('license_code');
                foreach ($licenses as $licenseCode) {
                    InstallationLogs::where('license_code', $licenseCode)->delete();
                }
                AflCallbacks::where('product_id', $productId)->delete();
                AflInstallations::where('product_id', $productId)->delete();
                AflLicenses::where('product_id', $productId)->delete();
                AflProducts::where('product_id', $productId)->forceDelete();
            });
        }

        return ['success' => true, 'data' => [], 'message' => ''];
    }
    protected function createLicense(array $payload): array
    {
        $data = [
            'product_id' => $payload['product_id'],
            'license_code' => $payload['license_code'],
            'license_order_number' => $payload['license_order_number'],
            'license_ip' => $payload['license_ip'],
            'license_domain' => $payload['license_domain'] ?? '',
            'license_require_domain' => $payload['license_require_domain'],
            'license_limit' => $payload['license_limit'] ?? 1,
            'license_expire_date' => $payload['license_expire_date'] ?? '',
            'license_updates_date' => $payload['license_updates_date'] ?? '',
            'license_support_date' => $payload['license_support_date'] ?? '',
            'license_status' => $payload['license_status'] ?? 1,
            'client_id' => $payload['client_id'] ?? null,
            'license_comments' => $payload['license_comments'] ?? null,
        ];

        return new LicenseController()->processLicenseAdd($data);
    }

    protected function updateLicense(array $payload): array
    {
        $data = [
            'product_id' => $payload['product_id'],
            'license_code' => $payload['license_code'],
            'license_order_number' => $payload['license_order_number'],
            'license_ip' => $payload['license_ip'],
            'license_domain' => $payload['license_domain'] ?? '',
            'license_require_domain' => $payload['license_require_domain'],
            'license_limit' => $payload['license_limit'] ?? 1,
            'license_expire_date' => $payload['license_expire_date'] ?? '',
            'license_updates_date' => $payload['license_updates_date'] ?? '',
            'license_support_date' => $payload['license_support_date'] ?? '',
            'license_status' => $payload['license_status'] ?? 1,
            'client_id' => $payload['client_id'] ?? null,
            'license_comments' => $payload['license_comments'] ?? null,
        ];

        return new LicenseController()->processLicenseUpdate($data);
    }

    protected function deactivateLicense(array $payload): array
    {
        new LicenseController()->licenseDeactivating($payload['license_code']);

        return ['success' => true, 'data' => [], 'message' => ''];
    }

    protected function updateLicenseCode(array $payload): array
    {
        new LicenseController()->updatingLicenseCode($payload['old_license_code'], $payload['license_code']);

        return ['success' => true, 'data' => [], 'message' => ''];
    }

    protected function syncAddonLicense(array $payload): array
    {
        return new LicenseController()->processSyncLicense($payload);
    }

    protected function updateInstallation(array $payload): array
    {
        $data = [
            'installation_id' => $payload['installation_id'] ?? null,
            'installation_ip' => $payload['installation_ip'] ?? '',
            'installation_status' => $payload['installation_status'] ?? 1,
            'installation_disable_ip' => $payload['installation_disable_ip'] ?? null,
        ];

        return new InstallationController()->processInstallationUpdate($data);
    }
    protected function getInstallationLogs(array $payload): array
    {
        return new InstallationLogsController()->processGetInstallationLogs($payload);
    }

    protected function updateInstallationLogs(array $payload): array
    {
        return new InstallationLogsController()->processUpdateInstallationLogs($payload);
    }

    protected function searchData(array $payload): array
    {
        $result = new SearchController()->searchByParams($payload, $payload['ip_address'] ?? '');

        return ['success' => true, 'data' => $result, 'message' => ''];
    }
}
