<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

// Prevent deprecation/warning messages from being printed during bootEnv()
// which can send output before headers (causing session start to fail).
$previousDisplayErrors = ini_get('display_errors');
ini_set('display_errors', '0');
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
// Restore display_errors and enable Debug if in debug mode
if ($_SERVER['APP_DEBUG']) {
    ini_set('display_errors', $previousDisplayErrors);
    umask(0000);

    Debug::enable();
} else {
    ini_set('display_errors', $previousDisplayErrors);
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
