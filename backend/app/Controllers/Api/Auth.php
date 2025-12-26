<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $model = new UserModel();
        $user = $model->where('username', $username)->first();

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        if (!password_verify($password, $user['password'])) {
            return $this->fail('Invalid password', 400);
        }

        $key = getenv('JWT_SECRET');
        $iat = time();
        $exp = $iat + 3600; // 1 hour

        $payload = [
            'iss' => 'perpuz_system',
            'iat' => $iat,
            'exp' => $exp,
            'uid' => $user['id'],
            'username' => $user['username']
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
            'message' => 'Login success',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
    }

    public function register()
    {
         $rules = [
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $model = new UserModel();
        $data = [
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];
        
        $model->insert($data);
        
        return $this->respondCreated(['message' => 'User created successfully']);
    }
}
