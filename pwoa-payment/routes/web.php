<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/db-check', function () {
    try {
        $pdo = DB::connection()->getPdo();

        return response()->json([
            'status' => 'success',
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'version' => DB::selectOne('SELECT VERSION() AS version')->version,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'failed',
            'message' => $e->getMessage(),
        ], 500);
    }
});
