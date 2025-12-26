<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Integration extends ResourceController
{
    protected $format = 'json';

    public function users()
    {
        // 1. Check Secret Header
        $secret = $this->request->getHeaderLine('X-INTEGRATION-SECRET');
        $envSecret = getenv('INTEGRATION_SECRET');

        if (empty($secret) || $secret !== $envSecret) {
            return $this->failForbidden('Invalid or missing integration secret.');
        }

        // 2. Fetch Users
        $model = new UserModel();
        // Only select safe fields
        $users = $model->select('id, username, created_at')->findAll();

        return $this->respond([
            'status' => 200,
            'count' => count($users),
            'data' => $users
        ]);
    }
}
