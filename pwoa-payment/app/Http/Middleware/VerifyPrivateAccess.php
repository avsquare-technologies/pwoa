<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPrivateAccess
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $timestamp = $request->header('X-TIMESTAMP');
    //     $signature = $request->header('X-SIGNATURE');

    //     \Log::info('Middleware', $request->all());

    //     if (!$timestamp || !$signature) {
    //         abort(403, 'Forbidden');
    //     }

    //     // Prevent replay attacks (5 min window)
    //     if (abs(time() - $timestamp) > 300) {
    //         abort(403, 'Expired request');
    //     }

    //     $body = $request->getContent();


    //     \Log::info('Middleware ' . $body . ' ' . config('services.private_api_key.secret') . ' ' . $timestamp . ' ' . $signature);

    //     $expected = hash_hmac(
    //         'sha256',
    //         $body . $timestamp,
    //         config('services.private_api_key.secret')
    //     );

    //     if (!hash_equals($expected, $signature)) {
    //         abort(403, 'Invalid signature');
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');
        $secret = config('services.private_api_key.secret');

        if (!$timestamp || !$signature) {
            abort(403, 'Forbidden');
        }

        // 1. Replicate the payload logic from the Client
        $method = strtoupper($request->getMethod());
        $endpoint = $request->getPathInfo(); // This gets '/api/endpoint'

        if ($method === 'GET') {
            $data = $request->query();
            ksort($data);
            $payload = http_build_query($data);
        } else {
            // Use raw content for POST/PUT to match the client's json_encode
            $payload = $request->getContent();
        }

        // 2. Rebuild the SAME canonical string used in PrivateApiClient
        $canonical = implode('|', [
            $method,
            $endpoint,
            $payload,
            $timestamp,
        ]);

        // 3. Hash and Compare
        $expected = hash_hmac('sha256', $canonical, $secret);

        if (!hash_equals($expected, $signature)) {
            \Log::error('Signature Mismatch', [
                'received' => $signature,
                'expected' => $expected,
                'canonical_built' => $canonical
            ]);
            abort(403, 'Invalid signature');
        }

        return $next($request);
    }
}
