<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // API routes
        'api/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Debug logging for local environment
        if (app()->environment('local')) {
            \Log::info('CSRF Debug Info:', [
                'method' => $request->method(),
                'url' => $request->url(),
                'csrf_token_from_request' => $request->input('_token'),
                'csrf_token_from_session' => $request->session()->token(),
                'session_id' => $request->session()->getId(),
                'has_session' => $request->hasSession(),
            ]);
        }
        
        return parent::handle($request, $next);
    }
}
