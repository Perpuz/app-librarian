<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\BookModel;

class Books extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\BookModel';
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound('No book found with id ' . $id);
    }

    public function create()
    {
        $rules = [
            'title' => 'required',
            'stock' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getVar();
        if ($this->model->insert($data)) {
            $data->id = $this->model->getInsertID();
            return $this->respondCreated($data);
        }

        return $this->fail('Failed to create book');
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
             return $this->failNotFound('No book found with id ' . $id);
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
        if ($this->model->update($id, $data)) {
            return $this->respond($data);
        }
        return $this->fail('Failed to update book');
    }

    public function delete($id = null)
    {
        if ($this->model->find($id)) {
            $this->model->delete($id);
            return $this->respondDeleted(['id' => $id]);
        }
        return $this->failNotFound('No book found with id ' . $id);
    }
}
