<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\RecommendationModel;
use App\Models\BookModel;

class Recommendations extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\RecommendationModel';
    protected $format    = 'json';

    /**
     * Get today's active daily recommendations (public endpoint)
     * Returns up to 5 books with full details
     */
    public function daily()
    {
        $bookModel = new BookModel();
        
        $recommendations = $this->model->select('daily_recommendations.*, books.*')
                                      ->join('books', 'books.id = daily_recommendations.book_id')
                                      ->where('daily_recommendations.is_active', 1)
                                      ->orderBy('daily_recommendations.display_order', 'ASC')
                                      ->limit(5)
                                      ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $recommendations,
            'count' => count($recommendations)
        ]);
    }

    /**
     * Get all recommendations (admin)
     */
    public function index()
    {
        $recommendations = $this->model->select('daily_recommendations.*, books.title, books.author, books.isbn')
                                      ->join('books', 'books.id = daily_recommendations.book_id')
                                      ->orderBy('daily_recommendations.display_order', 'ASC')
                                      ->findAll();
        
        return $this->respond($recommendations);
    }

    /**
     * Add book to daily recommendations (admin)
     * Max 5 active recommendations allowed
     */
    public function create()
    {
        // Check current count
        $activeCount = $this->model->where('is_active', 1)->countAllResults();
        
        if ($activeCount >= 5) {
            return $this->fail('Maximum 5 recommendations allowed. Please remove one before adding a new recommendation.', 400);
        }

        $rules = [
            'book_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getVar();
        
        // Check if book already in recommendations
        $existing = $this->model->where('book_id', $data->book_id)
                               ->where('is_active', 1)
                               ->first();
        
        if ($existing) {
            return $this->fail('This book is already in recommendations', 400);
        }

        // Set display order to next position
        $nextOrder = $activeCount + 1;
        
        $recommendationData = [
            'book_id' => $data->book_id,
            'display_order' => $nextOrder,
            'is_active' => 1
        ];

        if ($this->model->insert($recommendationData)) {
            $recommendationData['id'] = $this->model->getInsertID();
            return $this->respondCreated($recommendationData);
        }

        return $this->fail('Failed to add recommendation');
    }

    /**
     * Remove book from recommendations (admin)
     */
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Recommendation not found');
        }

        if ($this->model->delete($id)) {
            // Reorder remaining recommendations
            $this->reorderAfterDelete();
            return $this->respondDeleted(['id' => $id, 'message' => 'Recommendation removed']);
        }

        return $this->fail('Failed to delete recommendation');
    }

    /**
     * Update display order of recommendations (admin)
     */
    public function reorder()
    {
        $data = $this->request->getJSON(true);
        
        if (!isset($data['recommendations']) || !is_array($data['recommendations'])) {
            return $this->fail('Invalid data format. Expected array of {id, display_order}');
        }

        foreach ($data['recommendations'] as $item) {
            if (isset($item['id']) && isset($item['display_order'])) {
                $this->model->update($item['id'], ['display_order' => $item['display_order']]);
            }
        }

        return $this->respond(['message' => 'Order updated successfully']);
    }

    /**
     * Helper: Reorder recommendations after deletion
     */
    private function reorderAfterDelete()
    {
        $recommendations = $this->model->where('is_active', 1)
                                      ->orderBy('display_order', 'ASC')
                                      ->findAll();
        
        $order = 1;
        foreach ($recommendations as $rec) {
            $this->model->update($rec['id'], ['display_order' => $order]);
            $order++;
        }
    }
}
