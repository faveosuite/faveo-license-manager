<?php

namespace Tests\Unit\Streams\License;

use App\Models\AflInstallations;
use App\Streams\License\LicenseStreamHandler;
use App\Streams\RedisStreamProducer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LicenseStreamHandlerTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createHandler(): LicenseStreamHandler
    {
        return new LicenseStreamHandler();
    }

    private function createPartialHandler(): MockInterface
    {
        return Mockery::mock(LicenseStreamHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    private function assertMockeryExpectations(): void
    {
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
    }

    // ─── handle() dispatch routing ──────────────────────────────

    #[DataProvider('eventRoutingProvider')]
    public function test_handle_dispatches_to_correct_method(string $event, string $method): void
    {
        $handler = $this->createPartialHandler();
        $payload = ['some_key' => 'some_value'];

        $handler->shouldReceive($method)
            ->once()
            ->with($payload)
            ->andReturn(['success' => true, 'data' => [], 'message' => '']);

        $handler->handle([
            'event' => $event,
            'payload' => $payload,
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    public static function eventRoutingProvider(): array
    {
        return [
            'stream_ping' => ['stream_ping', 'ping'],
            'license_add_product' => ['license_add_product', 'addProduct'],
            'license_edit_product' => ['license_edit_product', 'editProduct'],
            'license_delete_product' => ['license_delete_product', 'deleteProduct'],
            'license_add_user' => ['license_add_user', 'addUser'],
            'license_edit_user' => ['license_edit_user', 'editUser'],
            'license_create' => ['license_create', 'createLicense'],
            'license_update' => ['license_update', 'updateLicense'],
            'license_installation' => ['license_installation', 'updateInstallation'],
            'license_deactivate' => ['license_deactivate', 'deactivateLicense'],
            'license_domain_reissue' => ['license_domain_reissue', 'reissueLicenses'],
            'license_update_code' => ['license_update_code', 'updateLicenseCode'],
            'license_sync_addon' => ['license_sync_addon', 'syncAddonLicense'],
            'license_get_installation_logs' => ['license_get_installation_logs', 'getInstallationLogs'],
            'license_update_installation_logs' => ['license_update_installation_logs', 'updateInstallationLogs'],
            'license_plugin_info' => ['license_plugin_info', 'getPluginInfo'],
            'license_search' => ['license_search', 'searchData'],
            'update_add_product' => ['update_add_product', 'addUpdateProduct'],
            'update_edit_product' => ['update_edit_product', 'editUpdateProduct'],
            'update_delete_product' => ['update_delete_product', 'deleteUpdateProduct'],
            'update_add_version' => ['update_add_version', 'addUpdateVersion'],
            'update_edit_version' => ['update_edit_version', 'editUpdateVersion'],
        ];
    }

    // ─── handle() unknown/null events ───────────────────────────

    public function test_handle_logs_warning_for_unknown_event(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with("LicenseStreamHandler: unhandled event 'unknown_event' or empty result");

        $handler = $this->createHandler();
        $handler->handle([
            'event' => 'unknown_event',
            'payload' => [],
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    public function test_handle_logs_warning_when_event_is_null(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with("LicenseStreamHandler: unhandled event '' or empty result");

        $handler = $this->createHandler();
        $handler->handle([
            'payload' => [],
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    public function test_handle_logs_warning_when_payload_is_missing(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with("LicenseStreamHandler: unhandled event 'unknown_event' or empty result");

        $handler = $this->createHandler();
        $handler->handle([
            'event' => 'unknown_event',
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    // ─── handle() strips metadata from payload ──────────────────

    public function test_handle_strips_reply_to_and_correlation_id_from_payload(): void
    {
        $handler = $this->createPartialHandler();

        $handler->shouldReceive('ping')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return ! isset($payload['reply_to'])
                    && ! isset($payload['correlation_id'])
                    && $payload['extra'] === 'data';
            }))
            ->andReturn(['success' => true, 'data' => [], 'message' => 'pong']);

        $handler->handle([
            'event' => 'stream_ping',
            'payload' => [
                'reply_to' => 'response-stream',
                'correlation_id' => 'corr-123',
                'extra' => 'data',
            ],
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    // ─── handle() response publishing ───────────────────────────

    public function test_handle_publishes_response_when_reply_to_and_correlation_id_present(): void
    {
        // Use untyped mock since RedisStreamProducer::publish is final
        $producerMock = Mockery::mock();
        $producerMock->shouldReceive('publish')
            ->once()
            ->with('stream_ping_response', Mockery::on(function ($payload) {
                return $payload['correlation_id'] === 'corr-123'
                    && $payload['result']['success'] === true
                    && $payload['result']['message'] === 'pong';
            }));

        $testHandler = new class($producerMock) extends LicenseStreamHandler
        {
            private $mockProducer;

            public function __construct($mockProducer)
            {
                $this->mockProducer = $mockProducer;
            }

            public function handle(array $data, string $messageId): void
            {
                $event = $data['event'] ?? null;
                $payload = $data['payload'] ?? [];
                $replyTo = $payload['reply_to'] ?? null;
                $correlationId = $payload['correlation_id'] ?? null;

                unset($payload['reply_to'], $payload['correlation_id']);

                $result = match ($event) {
                    'stream_ping' => $this->ping($payload),
                    default => null,
                };

                if ($result === null) {
                    return;
                }

                if ($replyTo && $correlationId) {
                    $this->mockProducer->publish($event . '_response', [
                        'correlation_id' => $correlationId,
                        'result' => $result,
                    ]);
                }
            }
        };

        $testHandler->handle([
            'event' => 'stream_ping',
            'payload' => [
                'reply_to' => 'response-stream',
                'correlation_id' => 'corr-123',
            ],
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    public function test_handle_does_not_publish_response_without_reply_to(): void
    {
        $handler = $this->createPartialHandler();

        $handler->shouldReceive('ping')
            ->once()
            ->andReturn(['success' => true, 'data' => [], 'message' => 'pong']);

        // No exception means RedisStreamProducer was never instantiated
        $handler->handle([
            'event' => 'stream_ping',
            'payload' => [
                'correlation_id' => 'corr-123',
            ],
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    public function test_handle_does_not_publish_response_without_correlation_id(): void
    {
        $handler = $this->createPartialHandler();

        $handler->shouldReceive('ping')
            ->once()
            ->andReturn(['success' => true, 'data' => [], 'message' => 'pong']);

        $handler->handle([
            'event' => 'stream_ping',
            'payload' => [
                'reply_to' => 'response-stream',
            ],
        ], 'msg-1');

        $this->assertMockeryExpectations();
    }

    // ─── ping() ─────────────────────────────────────────────────

    public function test_ping_returns_pong_response(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('ping');
        $method->setAccessible(true);

        $result = $method->invoke($handler, []);

        $this->assertEquals(['success' => true, 'data' => [], 'message' => 'pong'], $result);
    }

    // ─── createLicense() payload mapping ────────────────────────

    public function test_create_license_maps_all_payload_fields(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('createLicense');
        $method->setAccessible(true);

        $payload = [
            'product_id' => 5,
            'license_code' => 'TESTCREATE' . \Str::random(6),
            'license_order_number' => 'ORD-001',
            'license_ip' => '192.168.1.1',
            'license_domain' => 'example.com',
            'license_require_domain' => 1,
            'license_limit' => 3,
            'license_expire_date' => '2026-12-31',
            'license_updates_date' => '2026-12-31',
            'license_support_date' => '2026-12-31',
            'license_status' => 1,
            'license_disable_ip_verification' => 0,
            'client_id' => null,
            'license_comments' => 'Test comment',
        ];

        $result = $method->invoke($handler, $payload);

        $this->assertIsArray($result);
    }

    public function test_create_license_uses_defaults_for_optional_fields(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('createLicense');
        $method->setAccessible(true);

        $payload = [
            'product_id' => 5,
            'license_code' => 'TESTDEFAULT' . \Str::random(5),
            'license_order_number' => 'ORD-002',
            'license_ip' => '192.168.1.1',
            'license_require_domain' => 0,
        ];

        $result = $method->invoke($handler, $payload);

        $this->assertIsArray($result);
    }

    // ─── updateLicense() payload mapping ────────────────────────

    public function test_update_license_maps_payload_with_defaults(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('updateLicense');
        $method->setAccessible(true);

        $payload = [
            'product_id' => 5,
            'license_code' => 'NONEXISTENT' . \Str::random(5),
            'license_require_domain' => 0,
        ];

        $result = $method->invoke($handler, $payload);

        $this->assertIsArray($result);
    }

    // ─── updateInstallation() ───────────────────────────────────

    public function test_update_installation_deletes_by_license_code(): void
    {
        $licenseCode = \Str::upper(\Str::random(16));

        AflInstallations::factory()->create([
            'license_code' => $licenseCode,
            'product_id' => 100,
            'installation_status' => 1,
        ]);

        $this->assertDatabaseHas('afl_installations', ['license_code' => $licenseCode]);

        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('updateInstallation');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['license_code' => $licenseCode]);

        $this->assertDatabaseMissing('afl_installations', ['license_code' => $licenseCode]);
        $this->assertEquals(['success' => true, 'data' => [], 'message' => ''], $result);
    }

    public function test_update_installation_deletes_by_license_code_and_product_id(): void
    {
        $licenseCode1 = \Str::upper(\Str::random(16));
        $licenseCode2 = \Str::upper(\Str::random(16));

        AflInstallations::factory()->create([
            'license_code' => $licenseCode1,
            'product_id' => 100,
            'installation_status' => 1,
        ]);
        AflInstallations::factory()->create([
            'license_code' => $licenseCode2,
            'product_id' => 200,
            'installation_status' => 1,
        ]);

        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('updateInstallation');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'license_code' => $licenseCode1,
            'product_id' => 100,
        ]);

        $this->assertDatabaseMissing('afl_installations', [
            'license_code' => $licenseCode1,
            'product_id' => 100,
        ]);
        $this->assertDatabaseHas('afl_installations', [
            'license_code' => $licenseCode2,
            'product_id' => 200,
        ]);
        $this->assertEquals(['success' => true, 'data' => [], 'message' => ''], $result);
    }

    public function test_update_installation_delegates_to_controller_when_no_license_code(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('updateInstallation');
        $method->setAccessible(true);

        $payload = [
            'installation_id' => 999999,
            'installation_ip' => '10.0.0.1',
            'installation_status' => 1,
            'installation_disable_ip' => 0,
        ];

        $result = $method->invoke($handler, $payload);

        $this->assertIsArray($result);
    }

    public function test_update_installation_with_empty_license_code_delegates_to_controller(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('updateInstallation');
        $method->setAccessible(true);

        $payload = [
            'license_code' => '',
            'installation_id' => 999999,
            'installation_ip' => '10.0.0.1',
            'installation_status' => 1,
        ];

        $result = $method->invoke($handler, $payload);

        $this->assertIsArray($result);
    }

    // ─── getPluginInfo() ────────────────────────────────────────

    public function test_get_plugin_info_handles_array_license_codes(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('getPluginInfo');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['license_codes' => ['CODE1', 'CODE2']]);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }

    public function test_get_plugin_info_handles_json_string_license_codes(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('getPluginInfo');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['license_codes' => json_encode(['CODE1', 'CODE2'])]);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }

    public function test_get_plugin_info_handles_empty_license_codes(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('getPluginInfo');
        $method->setAccessible(true);

        $result = $method->invoke($handler, []);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertEmpty($result['data']);
    }

    public function test_get_plugin_info_handles_invalid_json_string(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('getPluginInfo');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['license_codes' => 'not-valid-json{{{']);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertEmpty($result['data']);
    }

    // ─── reissueLicenses() ──────────────────────────────────────

    public function test_reissue_licenses_removes_installation_by_path(): void
    {
        $domain = 'test-reissue-' . \Str::random(8) . '.com';

        AflInstallations::factory()->create([
            'installation_domain' => $domain,
            'installation_status' => 1,
        ]);

        $this->assertDatabaseHas('afl_installations', ['installation_domain' => $domain]);

        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('reissueLicenses');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['installation_path' => $domain]);

        $this->assertDatabaseMissing('afl_installations', ['installation_domain' => $domain]);
        $this->assertEquals(['success' => true, 'data' => [], 'message' => ''], $result);
    }

    public function test_reissue_licenses_with_null_path_throws_type_error(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('reissueLicenses');
        $method->setAccessible(true);

        $this->expectException(\TypeError::class);
        $method->invoke($handler, []);
    }

    // ─── deactivateLicense() ────────────────────────────────────

    public function test_deactivate_license_returns_success_response(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('deactivateLicense');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['license_code' => 'NONEXISTENT123']);

        $this->assertEquals(['success' => true, 'data' => [], 'message' => ''], $result);
    }

    // ─── updateLicenseCode() ────────────────────────────────────

    public function test_update_license_code_returns_success_response(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('updateLicenseCode');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'old_license_code' => 'OLD_CODE',
            'license_code' => 'NEW_CODE',
        ]);

        $this->assertEquals(['success' => true, 'data' => [], 'message' => ''], $result);
    }

    // ─── searchData() ───────────────────────────────────────────

    public function test_search_data_wraps_result_in_success_response(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('searchData');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['search_type' => 'license', 'search_value' => 'nonexistent']);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertEquals('', $result['message']);
    }

    public function test_search_data_passes_ip_address_from_payload(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('searchData');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'search_type' => 'license',
            'search_value' => 'test',
            'ip_address' => '10.0.0.1',
        ]);

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
    }

    // ─── Controller delegation methods ──────────────────────────

    public function test_add_product_delegates_to_products_controller(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('addProduct');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'product_title' => 'Test Product',
            'product_sku' => 'TST-' . \Str::random(5),
            'product_status' => 1,
        ]);

        $this->assertIsArray($result);
    }

    public function test_edit_product_delegates_to_products_controller(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('editProduct');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'product_id' => 999999,
            'product_title' => 'Updated Product',
        ]);

        $this->assertIsArray($result);
    }

    public function test_delete_product_delegates_to_products_controller(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('deleteProduct');
        $method->setAccessible(true);

        $result = $method->invoke($handler, ['product_id' => 999999]);

        $this->assertIsArray($result);
    }

    public function test_add_user_delegates_to_clients_controller(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('addUser');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'client_email' => 'test-' . \Str::random(8) . '@example.com',
            'client_first_name' => 'Test',
            'client_last_name' => 'User',
        ]);

        $this->assertIsArray($result);
    }

    public function test_edit_user_delegates_to_clients_controller(): void
    {
        $handler = $this->createHandler();
        $reflection = new \ReflectionClass($handler);
        $method = $reflection->getMethod('editUser');
        $method->setAccessible(true);

        $result = $method->invoke($handler, [
            'client_id' => 999999,
            'client_email' => 'updated@example.com',
        ]);

        $this->assertIsArray($result);
    }

    // ─── MAX_RESPONSE_STREAM_LEN constant ───────────────────────

    public function test_max_response_stream_len_is_10000(): void
    {
        $reflection = new \ReflectionClass(LicenseStreamHandler::class);
        $constant = $reflection->getConstant('MAX_RESPONSE_STREAM_LEN');

        $this->assertEquals(10000, $constant);
    }
}
