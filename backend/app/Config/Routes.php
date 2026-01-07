<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default Route to API Message
$routes->get('/', function() {
    return response()->setJSON([
        'status' => 'ok',
        'message' => 'Perpuz Librarian Backend API',
        'version' => '1.0'
    ]);
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('auth/login', 'Auth::login');
    $routes->options('auth/login', function() {
        return response()->setStatusCode(200);
    });
    $routes->post('auth/register', 'Auth::register');

    // Handle all other OPTIONS requests
    $routes->options('(:any)', function() {
        return response()->setStatusCode(200);
    });

    // Public recommendation endpoint
    $routes->get('recommendations/daily', 'Recommendations::daily');

    $routes->group('', ['filter' => 'jwt'], function($routes) {
        // Protected Routes
        $routes->get('dashboard', 'Transactions::dashboard');
        $routes->resource('books');
        $routes->resource('members');
        $routes->resource('transactions');
        $routes->get('openlibrary/search', 'OpenLibrary::search');
        
        // Recommendations (Admin)
        $routes->resource('recommendations');
        $routes->put('recommendations/reorder', 'Recommendations::reorder');
    });

    // Integration Routes (Secured by Secret Header)
    $routes->group('integration', ['namespace' => 'App\Controllers\Api'], function($routes) {
        $routes->get('users', 'Integration::users');
        $routes->get('books', 'Integration::books');
        // Sync Endpoints (Push from Member App)
        $routes->post('sync/member', 'Integration::sync_member');
        $routes->post('sync/transaction', 'Integration::sync_transaction');
    });
});
