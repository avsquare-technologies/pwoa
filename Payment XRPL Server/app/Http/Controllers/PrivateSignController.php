<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrivateSignController extends Controller
{
    public function sign(Request $request): JsonResponse
    {
        // Validate input (keep it strict)
        $validated = $request->validate([
            'data' => ['required', 'string'],
        ]);


        // Fake signing for now
        $signature = hash('sha256', $validated['data'] . 'signed');

        return response()->json([
            'success' => true,
            'signature' => $signature,
        ]);
    }

    public function test()
    {
        return response()->json([
            'status' => 'private-ok',
            'time' => now()->toDateTimeString(),
        ]);
    }

}
