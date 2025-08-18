<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Exception;

try {
    // Basic Laravel bootstrap check
    $app = require __DIR__ . '/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Database connection check
    DB::connection()->getPdo();

    // Redis connection check
    if (config('cache.default') === 'redis') {
        Redis::connection()->ping();
    }

    // Storage write check
    Storage::disk('local')->put('healthcheck', 'ok');
    Storage::disk('local')->delete('healthcheck');

    http_response_code(200);
    echo json_encode([
        'status' => 'ok',
        'services' => [
            'database' => true,
            'redis' => config('cache.default') === 'redis',
            'storage' => true
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
}
