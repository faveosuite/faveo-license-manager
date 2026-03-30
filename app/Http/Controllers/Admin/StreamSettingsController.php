<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Streams\StreamConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamSettingsController extends Controller
{
    public function show(): JsonResponse
    {
        $currentValues = [
            'stream_redis_host' => StreamConfig::value('STREAM_REDIS_HOST', env('REDIS_HOST', '127.0.0.1')),
            'stream_redis_port' => StreamConfig::value('STREAM_REDIS_PORT', env('REDIS_PORT', '6379')),
            'stream_redis_username' => StreamConfig::value('STREAM_REDIS_USERNAME', ''),
            'stream_redis_password' => StreamConfig::value('STREAM_REDIS_PASSWORD', ''),
            'stream_redis_database' => StreamConfig::value('STREAM_REDIS_DATABASE', '2'),
        ];

        return successResponse('', $currentValues);
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'stream_redis_host' => 'required|string',
                'stream_redis_port' => 'required|numeric',
                'stream_redis_database' => 'required|numeric',
            ]);

            $this->testConnection($request->all());

            StreamConfig::modify($request->only([
                'stream_redis_host',
                'stream_redis_port',
                'stream_redis_username',
                'stream_redis_password',
                'stream_redis_database',
            ]));

            return successResponse('Redis Stream configuration updated successfully.');
        } catch (\Exception $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    private function testConnection(array $args): void
    {
        $host = $args['stream_redis_host'] ?? '127.0.0.1';
        $port = (int) ($args['stream_redis_port'] ?? 6379);
        $password = $args['stream_redis_password'] ?? null;
        $database = (int) ($args['stream_redis_database'] ?? 2);
        $username = $args['stream_redis_username'] ?? null;

        $client = config('database.redis.client', 'phpredis');

        if ($client === 'phpredis') {
            throw_if(
                ! extension_loaded('redis'),
                new \Exception('The phpredis extension is not installed.')
            );

            $redis = new \Redis();
            $redis->connect($host, $port, 5);

            if ($password) {
                $redis->auth($username ? [$username, $password] : $password);
            }

            $redis->select($database);
            $redis->ping();
            $redis->close();
        } else {
            $config = [
                'host' => $host,
                'port' => $port,
                'database' => $database,
            ];

            if ($password) {
                $config['password'] = $password;
            }
            if ($username) {
                $config['username'] = $username;
            }

            $predis = new \Predis\Client($config);
            $predis->ping();
            $predis->disconnect();
        }
    }
}
