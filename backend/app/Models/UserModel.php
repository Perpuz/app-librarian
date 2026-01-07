<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['username', 'password', 'created_at'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
}
