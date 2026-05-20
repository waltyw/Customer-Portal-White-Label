<?php

declare(strict_types=1);

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
    'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS',
    'SMTP_HOST', 'SMTP_USER', 'SMTP_PASS',
    'STRIPE_SECRET_KEY', 'STRIPE_PUBLISHABLE_KEY', 'STRIPE_WEBHOOK_SECRET',
    'APP_KEY',
]);

// Session configuration — must be set before session_start()
$sessionName = $_ENV['SESSION_NAME'] ?? 'bbz_portal';
$sessionLifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', (string)$sessionLifetime);
ini_set('session.use_strict_mode', '1');
session_name($sessionName);

// Error handling
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__) . '/storage/logs/php_errors.log');
}

// Timezone
date_default_timezone_set('Europe/London');

// Stripe SDK init
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
\Stripe\Stripe::setApiVersion('2024-11-20.acacia');
