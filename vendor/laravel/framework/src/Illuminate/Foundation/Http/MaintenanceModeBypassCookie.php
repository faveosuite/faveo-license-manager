<?php

namespace Illuminate\Foundation\Http;

use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Cookie;

class MaintenanceModeBypassCookie
{
    /**
     * Create a new maintenance mode bypass cookie.
     *
     * @param  string  $key
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public static function create(string $key)
    {
        $expiresAt = Carbon::now()->addHours(12);

        return new Cookie('laravel_maintenance', base64_encode(json_encode([
            'expires_at' => $expiresAt->getTimestamp(),
<<<<<<< HEAD
<<<<<<< HEAD
            'mac' => hash_hmac('sha256', $expiresAt->getTimestamp(), $key),
=======
            'mac' => hash_hmac('SHA256', $expiresAt->getTimestamp(), $key),
>>>>>>> 22c0e54 (table changes)
=======
            'mac' => hash_hmac('sha256', $expiresAt->getTimestamp(), $key),
>>>>>>> f330c64 (optimization in progress)
        ])), $expiresAt);
    }

    /**
     * Determine if the given maintenance mode bypass cookie is valid.
     *
     * @param  string  $cookie
     * @param  string  $key
     * @return bool
     */
    public static function isValid(string $cookie, string $key)
    {
        $payload = json_decode(base64_decode($cookie), true);

        return is_array($payload) &&
            is_numeric($payload['expires_at'] ?? null) &&
            isset($payload['mac']) &&
<<<<<<< HEAD
<<<<<<< HEAD
            hash_equals(hash_hmac('sha256', $payload['expires_at'], $key), $payload['mac']) &&
=======
            hash_equals(hash_hmac('SHA256', $payload['expires_at'], $key), $payload['mac']) &&
>>>>>>> 22c0e54 (table changes)
=======
            hash_equals(hash_hmac('sha256', $payload['expires_at'], $key), $payload['mac']) &&
>>>>>>> f330c64 (optimization in progress)
            (int) $payload['expires_at'] >= Carbon::now()->getTimestamp();
    }
}
