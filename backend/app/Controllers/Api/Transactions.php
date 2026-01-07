<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\TransactionModel;
use App\Models\BookModel;
use App\Models\MemberModel;

class Transactions extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\TransactionModel';
    protected $format    = 'json';

    public function index()
    {
        // Join with books and members for display
        $data = $this->model->select('transactions.*, books.title as book_title, members.name as member_name')
                            ->join('books', 'books.id = transactions.book_id')
                            ->join('members', 'members.id = transactions.member_id')
                            ->orderBy('transactions.id', 'DESC')
                            ->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound('Transaction not found');
    }

    public function create()
    {
         $rules = [
            'member_code'     => 'required',
            'book_identifier' => 'required', // ISBN or Title
            'borrow_date'     => 'required',
            'due_date'        => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getVar();
        $bookModel = new BookModel();
        $memberModel = new MemberModel();

        // Lookup Member
        $member = $memberModel->where('member_code', $data->member_code)->first();
        if (!$member) {
            return $this->failNotFound('Member not found with code: ' . $data->member_code);
        }

        // Lookup Book (Try ISBN first, then Title containing string)
        $book = $bookModel->where('isbn', $data->book_identifier)->first();
        if (!$book) {
            // Try exact title matching first for better accuracy
            $book = $bookModel->where('title', $data->book_identifier)->first();
        }
        if (!$book) {
             // Fallback to LIKE query if needed, but for borrowing exact match is safer. 
             // Let's stick to exact title or ISBN for now to avoid borrowing wrong book.
             return $this->failNotFound('Book not found with ISBN or Title: ' . $data->book_identifier);
        }
        
        if ($book['stock'] < 1) {
            return $this->fail('Book "' . $book['title'] . '" is out of stock');
        }

        // Decrease stock
        $bookModel->update($book['id'], ['stock' => $book['stock'] - 1]);

        $trxData = [
            'member_id'   => $member['id'],
            'book_id'     => $book['id'],
            'borrow_date' => $data->borrow_date,
            'due_date'    => $data->due_date,
            'status'      => 'borrowed'
        ];

        if ($this->model->insert($trxData)) {
            $trxData['id'] = $this->model->getInsertID();
            return $this->respondCreated($trxData);
        }

        return $this->fail('Failed to create transaction');
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
             return $this->failNotFound('Transaction not found');
        }
        
        try {
            $data = $this->request->getJSON(true);
        } catch (\Exception $e) {
            $data = null;
        }

        if (empty($data)) {
            $data = $this->request->getRawInput();
        }

        if (empty($data)) {
            return $this->fail('No data provided for update or invalid JSON');
        }
        
        // Start handling return logic if status changed to returned
        if (isset($data['status']) && $data['status'] == 'returned') {
            $trx = $this->model->find($id);
            if ($trx['status'] != 'returned') {
                // Increase stock
                $bookModel = new BookModel();
                $book = $bookModel->find($trx['book_id']);
                $bookModel->update($trx['book_id'], ['stock' => $book['stock'] + 1]);
                
                // Calculate fine if needed (simple logic)
                if(!isset($data['return_date'])) $data['return_date'] = date('Y-m-d');
                $due = strtotime($trx['due_date']);
                $return = strtotime($data['return_date']);
                if ($return > $due) {
                     $days = ($return - $due) / (60 * 60 * 24);
                     $data['fine'] = $days * 1000; // 1000 IDR per day
                }
            }
        }

        if ($this->model->update($id, $data)) {
            return $this->respond($data);
        }
        return $this->fail('Failed to update transaction');
    }

    public function delete($id = null)
    {
        if ($this->model->find($id)) {
            $this->model->delete($id);
            return $this->respondDeleted(['id' => $id]);
        }
        return $this->failNotFound('Transaction not found');
    }

    public function dashboard()
    {
        $bookModel = new BookModel();
        $memberModel = new MemberModel();
        
        $totalBooks = $bookModel->countAll();
        $activeMembers = $memberModel->where('status', 'active')->countAllResults();
        $totalTransactions = $this->model->countAll();
        
        // Calculate Total Fines (Collected/Recorded)
        $totalFines = $this->model->selectSum('fine')->first()['fine'] ?? 0;
        
        $recentTransactions = $this->model->select('transactions.*, books.title as book_title, members.name as member_name')
                            ->join('books', 'books.id = transactions.book_id')
                            ->join('members', 'members.id = transactions.member_id')
                            ->orderBy('transactions.id', 'DESC')
                            ->limit(5)
                            ->find();

        return $this->respond([
            'total_books' => $totalBooks,
            'active_members' => $activeMembers,
            'total_transactions' => $totalTransactions,
            'total_fines' => $totalFines,
            'recent_transactions' => $recentTransactions
        ]);
    }
}
