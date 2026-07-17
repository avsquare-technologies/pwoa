<?php


file_put_contents(__DIR__.'/../git_output_2.txt', "=== WHOAMI ===\n" . shell_exec('whoami') . "\n=== GIT SHOW SUBSCRIBE PHP ===\n" . shell_exec('git show 61685ee7a0b51aaa563fde295cb934ecf382eaaf:app/Livewire/Subscribe.php 2>&1') . "\n=== GIT SHOW ROUTE WEB ===\n" . shell_exec('git show 61685ee7a0b51aaa563fde295cb934ecf382eaaf:routes/web.php 2>&1'));


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
