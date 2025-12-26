<?php
namespace App\Models;
use CodeIgniter\Model;

class BookModel extends Model {
    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'author', 'isbn', 'stock', 'cover_url', 'description'];
    protected $useTimestamps = false;
}

namespace App\Models;
use CodeIgniter\Model;

class MemberModel extends Model {
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $allowedFields = ['member_code', 'name', 'email', 'phone', 'status', 'joined_at'];
    protected $useTimestamps = false;
}

namespace App\Models;
use CodeIgniter\Model;

class TransactionModel extends Model {
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['member_id', 'book_id', 'borrow_date', 'due_date', 'return_date', 'status', 'fine'];
    protected $useTimestamps = false;
}
