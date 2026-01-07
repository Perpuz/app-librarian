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
        $envSecret = env('INTEGRATION_SECRET');

        if (empty($secret) || $secret !== $envSecret) {
            return $this->failForbidden('Invalid or missing integration secret.');
        }

        // 2. Fetch Users
        $model = new UserModel();
        // Only select safe fields
        $users = $model->select('id, username, created_at')->findAll();

        return $this->respond($users);
    }

    public function books()
    {
        // ... (existing code, keeping checking secret)
        $secret = $this->request->getHeaderLine('X-INTEGRATION-SECRET');
        $envSecret = env('INTEGRATION_SECRET');

        if (empty($secret) || $secret !== $envSecret) {
            return $this->failForbidden('Invalid or missing integration secret.');
        }

        $model = new \App\Models\BookModel();
        return $this->respond($model->findAll());
    }

    public function sync_member()
    {
        // 1. Check Secret
        $secret = $this->request->getHeaderLine('X-INTEGRATION-SECRET');
        if (empty($secret) || $secret !== env('INTEGRATION_SECRET')) {
            return $this->failForbidden();
        }

        // 2. Get Data
        $data = $this->request->getJSON(true); // as array
        if (!$data) return $this->fail('No data');

        $model = new \App\Models\MemberModel();
        
        // 3. Upsert Member
        // Match by member_code (NIM) or email
        $existing = $model->where('member_code', $data['nim'])->orWhere('email', $data['email'])->first();
        
        $saveData = [
            'member_code' => $data['nim'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active'
        ];

        if ($existing) {
            $model->update($existing['id'], $saveData);
            return $this->respond(['status' => 'updated', 'id' => $existing['id']]);
        } else {
            $id = $model->insert($saveData);
            return $this->respondCreated(['status' => 'created', 'id' => $id]);
        }
    }

    public function sync_transaction()
    {
        // 1. Check Secret
        $secret = $this->request->getHeaderLine('X-INTEGRATION-SECRET');
        if (empty($secret) || $secret !== env('INTEGRATION_SECRET')) {
            return $this->failForbidden();
        }

        $data = $this->request->getJSON(true);
        // Log Received Data
        log_message('error', 'Sync Transaction Data: ' . json_encode($data));
        
        // 2. Resolve IDs
        $memberModel = new \App\Models\MemberModel();
        $bookModel = new \App\Models\BookModel();
        $trxModel = new \App\Models\TransactionModel();

        $member = $memberModel->where('member_code', $data['nim'])->first();
        if (!$member) return $this->failNotFound('Member not found: ' . $data['nim']);

        $book = $bookModel->where('isbn', $data['book_isbn'])->first();
        if (!$book) {
            // Try to find by title if ISBN fails? Or just fail?
            // Fallback: try finding by Title roughly?
            if(isset($data['book_title'])) {
                $book = $bookModel->like('title', $data['book_title'])->first();
            }
            if(!$book) return $this->failNotFound('Book not found: ISBN ' . $data['book_isbn']);
        }

        // 3. Create Transaction
        // Check duplicate?
        // Basic insert for now as per requirement "muncul di page transaction"
        $trxData = [
            'member_id' => $member['id'],
            'book_id' => $book['id'],
            'borrow_date' => $data['borrow_date'],
            'due_date' => $data['due_date'],
            'status' => $data['status'] ?? 'borrowed'
        ];
        
        // Ideally checking for "Return" update logic too?
        // If Data has 'return_date', handle return
        if (isset($data['return_date']) && $data['status'] === 'returned') {
            // Find active transaction for this user/book
            $active = $trxModel->where('member_id', $member['id'])
                               ->where('book_id', $book['id'])
                               ->whereIn('status', ['borrowed', 'overdue']) // Handle both statuses
                               ->first();
            if ($active) {
                $trxModel->update($active['id'], [
                    'status' => 'returned', 
                    'return_date' => $data['return_date'],
                    'fine' => $data['fine'] ?? 0
                ]);
                // Restore Stock
                $bookModel->update($book['id'], ['stock' => $book['stock'] + 1]);
                return $this->respond(['status' => 'returned']);
            }
        }

        // New Borrow
        if ($book['stock'] < 1) {
             return $this->fail('Book out of stock in Librarian system');
        }

        $trxModel->insert($trxData);
        // Decrease Stock
        $bookModel->update($book['id'], ['stock' => $book['stock'] - 1]);

        return $this->respondCreated(['status' => 'created']);
    }
}
