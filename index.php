<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/Config.php';

// Check installation status
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isInstallPage = strpos($requestUri, '/install') !== false;

if (!Config::isInstalled() && !$isInstallPage) {
  header('Location: /install');
  exit;
} elseif (Config::isInstalled() && $isInstallPage) {
  header('Location: /');
  exit;
}

// Start session
Session::start();

// Initialize request and router
$request = new Request();
$router = new Router($request);

// Middlewares
$authMiddleware = function() use ($request) {
  if (!$request->isAuthenticated()) {
    Session::setFlash('error', 'يجب تسجيل الدخول أولاً');
    Response::redirect('/login');
  }
};

$adminMiddleware = function() use ($request) {
  if (!$request->isAdmin()) {
    Session::setFlash('error', 'غير مصرح بالوصول');
    Response::redirect('/dashboard');
  }
};

// Installation routes
$router->addRoute('GET', '/install', 'InstallController@showInstallForm');
$router->addRoute('POST', '/install', 'InstallController@processInstall');

// Authentication routes
$router->addRoute('GET', '/login', 'AuthController@login');
$router->addRoute('POST', '/login', 'AuthController@login');
$router->addRoute('GET', '/logout', 'AuthController@logout');

// Registration routes
$router->addRoute('GET', '/register', 'AuthController@showRegister');
$router->addRoute('POST', '/register', 'AuthController@register');

// Invoice routes (authenticated)
$router->addRoute('GET', '/dashboard', 'InvoiceController@dashboard', [$authMiddleware]);
$router->addRoute('GET', '/invoice/create', 'InvoiceController@create', [$authMiddleware]);
$router->addRoute('POST', '/invoice/create', 'InvoiceController@create', [$authMiddleware]);
$router->addRoute('GET', '/invoice/{id}', 'InvoiceController@show', [$authMiddleware]);

// Admin routes
$router->addRoute('GET', '/admin/dashboard', 'InvoiceController@dashboard', [$authMiddleware, $adminMiddleware]);
$router->addRoute('GET', '/admin/invoice/{id}', 'InvoiceController@show', [$authMiddleware, $adminMiddleware]);
$router->addRoute('POST', '/admin/invoice/{id}/update-status', 'InvoiceController@updateStatus', [$authMiddleware, $adminMiddleware]);

// Default route
$router->addRoute('GET', '/', function() {
  if (Session::get('user_id')) {
    Response::redirect(Session::get('user_role') === Config::ROLE_ADMIN
      ? '/admin/dashboard'
      : '/dashboard');
  } else {
    Response::redirect('/login');
  }
});

// Dispatch the request
$router->dispatch();