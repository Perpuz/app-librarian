<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    return redirect()->to('index.html');
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('auth/login', 'Auth::login');
    $routes->post('auth/register', 'Auth::register');

    $routes->group('', ['filter' => 'jwt'], function($routes) {
        // Protected Routes
        $routes->get('dashboard', 'Transactions::dashboard');
        $routes->resource('books');
        $routes->resource('members');
        $routes->resource('transactions');
        $routes->get('openlibrary/search', 'OpenLibrary::search');
    });

    // Integration Routes (Secured by Secret Header)
    $routes->group('integration', ['namespace' => 'App\Controllers\Api'], function($routes) {
        $routes->get('users', 'Integration::users');
    });
});
