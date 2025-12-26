<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class OpenLibrary extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';
    
    // Search books from OpenLibrary API
    public function search()
    {
        $query = $this->request->getVar('q');
        if (!$query) {
            return $this->fail('Query parameter "q" is required', 400);
        }

        $url = "https://openlibrary.org/search.json?q=" . urlencode($query) . "&limit=10";
        
        // Use CURL to fetch
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev
        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output, true);
        
        $books = [];
        if (isset($data['docs'])) {
            foreach ($data['docs'] as $doc) {
                // Get cover URL
                $cover = null;
                if (isset($doc['cover_i'])) {
                    $cover = "https://covers.openlibrary.org/b/id/" . $doc['cover_i'] . "-M.jpg";
                }

                $books[] = [
                    'title' => $doc['title'] ?? 'Unknown Title',
                    'author' => isset($doc['author_name']) ? implode(', ', $doc['author_name']) : 'Unknown Author',
                    'isbn' => isset($doc['isbn']) ? $doc['isbn'][0] : null,
                    'cover_url' => $cover,
                    'publish_year' => $doc['first_publish_year'] ?? null
                ];
            }
        }

        return $this->respond($books);
    }
}
