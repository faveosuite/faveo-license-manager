<?php

namespace App\Http\Middleware;
use Illuminate\Session\TokenMismatchException;
use Closure;
use Lang;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/apl_callbacks/connection_test.php',
        '/apl_callbacks/license_install.php',
        '/apl_callbacks/license_scheme.php',
        '/apl_callbacks/license_verify.php',
        'db-setup',
    ];
    protected $config = [];


    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            return errorResponse(Lang::get('lang.token_expired'), 422);
        }
    }

    protected function addCookieToResponse($request, $response): void
    {
        $this->setSessionConfig();

        $this->config['csrfCookieHttpOnly'] ?
            $this->appendCSRFCookieToResponse($request, $response) :
            parent::addCookieToResponse($request, $response);
    }

    protected function tokensMatch($request): bool
    {
        $this->setSessionConfig();

        return (!$this->config['csrfCookieHttpOnly']) ?
            parent::tokensMatch($request) : $this->validateToken($request);
    }

    private function appendCSRFCookieToResponse($request, $response): void
    {
        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                $this->availableAt(60 * $this->config['lifetime']),
                $this->config['path'],
                $this->config['domain'],
                $this->config['secure'],
                true,
                false,
                $this->config['same_site'] ?? null,
                $this->config['partitioned'] ?? false
            )
        );
    }

    private function validateToken($request): bool
    {
        return is_string($sessionToken = $request->session()->token())
            && is_string($requestToken = $this->fetchTokenFromRequest($request))
            && hash_equals($sessionToken, $requestToken);
    }

    private function fetchTokenFromRequest($request)
    {
        return $request->cookies->get('XSRF-TOKEN');
    }

    private function setSessionConfig(): void
    {
        $this->config = config('session');
    }
}
