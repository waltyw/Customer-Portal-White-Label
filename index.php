<?php

declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';

use App\Core\Security;
use App\Core\View;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TicketController;
use App\Controllers\InvoiceController;
use App\Controllers\PaymentController;
use App\Controllers\WebhookController;
use App\Controllers\AdminController;
use App\Controllers\AccountController;
use App\Controllers\HelpController;

// Stripe webhook must read raw body before session/output start
$isWebhook = ($_SERVER['REQUEST_METHOD'] === 'POST'
    && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/webhook/stripe');

if (!$isWebhook) {
    session_start();
    Security::setHeaders();
}

View::init(__DIR__ . '/templates');

// ── Routing ──────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$path   = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($path !== '/' && str_ends_with($path, '/')) {
    Security::redirect(rtrim($path, '/'));
}

$routes = [
    'GET /'                        => [AuthController::class,      'loginForm'],
    'GET /login'                   => [AuthController::class,      'loginForm'],
    'POST /login'                  => [AuthController::class,      'login'],
    'GET /logout'                  => [AuthController::class,      'logout'],
    'GET /forgot-password'         => [AuthController::class,      'forgotForm'],
    'POST /forgot-password'        => [AuthController::class,      'forgotSubmit'],
    'GET /reset-password'          => [AuthController::class,      'resetForm'],
    'POST /reset-password'         => [AuthController::class,      'resetSubmit'],
    'GET /dashboard'               => [DashboardController::class, 'index'],
    'GET /tickets'                 => [TicketController::class,    'index'],
    'GET /tickets/create'          => [TicketController::class,    'create'],
    'POST /tickets/create'         => [TicketController::class,    'store'],
    'GET /invoices'                => [InvoiceController::class,   'index'],
    'GET /account'                 => [AccountController::class,   'index'],
    'POST /account'                => [AccountController::class,   'update'],
    'GET /help'                    => [HelpController::class,      'index'],
    'GET /payment/success'         => [PaymentController::class,   'success'],
    'GET /payment/cancelled'       => [PaymentController::class,   'cancelled'],
    'POST /webhook/stripe'         => [WebhookController::class,   'stripe'],
    'GET /admin'                   => [AdminController::class,     'dashboard'],
    'GET /admin/system-status'     => [AdminController::class,     'systemStatus'],
    'GET /admin/customers'         => [AdminController::class,     'customers'],
    'GET /admin/customers/create'  => [AdminController::class,     'createCustomer'],
    'POST /admin/customers/create' => [AdminController::class,     'storeCustomer'],
    'GET /admin/tickets'           => [AdminController::class,     'tickets'],
    'GET /admin/service-status'    => [AdminController::class,     'serviceStatus'],
    'POST /admin/service-status/add' => [AdminController::class,   'addService'],
    'GET /admin/invoices'          => [AdminController::class,     'invoices'],
    'GET /admin/invoices/create'   => [AdminController::class,     'createInvoice'],
    'POST /admin/invoices/create'  => [AdminController::class,     'storeInvoice'],
    'GET /admin/settings'          => [AdminController::class,     'settings'],
    'POST /admin/settings'         => [AdminController::class,     'saveSettings'],
];

$key = $method . ' ' . $path;
if (isset($routes[$key])) {
    [$class, $action] = $routes[$key];
    (new $class())->$action();
    exit;
}

$dynamicRoutes = [
    '#^GET /tickets/(\d+)$#'                 => [TicketController::class,  'show'],
    '#^POST /tickets/(\d+)/reply$#'          => [TicketController::class,  'reply'],
    '#^GET /invoices/(\d+)$#'               => [InvoiceController::class, 'show'],
    '#^GET /invoices/(\d+)/pay$#'           => [PaymentController::class, 'showPay'],
    '#^POST /invoices/(\d+)/pay$#'          => [PaymentController::class, 'processStripe'],
    '#^GET /admin/customers/(\d+)$#'        => [AdminController::class,   'viewCustomer'],
    '#^POST /admin/customers/(\d+)/update$#'=> [AdminController::class,   'updateCustomer'],
    '#^POST /admin/customers/(\d+)/toggle$#'=> [AdminController::class,   'toggleCustomer'],
    '#^POST /admin/service-status/(\d+)/update$#' => [AdminController::class, 'updateServiceStatus'],
    '#^POST /admin/service-status/(\d+)/delete$#' => [AdminController::class, 'deleteService'],
    '#^GET /admin/tickets/(\d+)$#'          => [AdminController::class,   'viewTicket'],
    '#^POST /admin/tickets/(\d+)/reply$#'   => [AdminController::class,   'replyTicket'],
    '#^POST /admin/tickets/(\d+)/status$#'  => [AdminController::class,   'updateTicketStatus'],
];

$requestLine = $method . ' ' . $path;
foreach ($dynamicRoutes as $pattern => [$class, $action]) {
    if (preg_match($pattern, $requestLine, $matches)) {
        (new $class())->$action((int)$matches[1]);
        exit;
    }
}

http_response_code(404);
View::renderRaw('errors/404', ['title' => 'Page Not Found']);
