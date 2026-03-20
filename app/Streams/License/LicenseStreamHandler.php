<?php

namespace App\Streams\License;

use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\LicenseController;
use App\Streams\RedisStreamProducer;
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
            'license_plugin_info' => $this->getPluginInfo($payload),
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

    protected function getPluginInfo(array $payload)
    {
        $codes = collect($payload['license_codes'] ?? []);

        return collect(new LicenseController()->getLicensePluginData($codes->toArray()))->toArray();
    }

    protected function reissueLicenses(array $payload)
    {
        $installationPath = $payload['installation_path'] ?? null;

        return new InstallationController()->removeInstallation($installationPath);
    }
}
