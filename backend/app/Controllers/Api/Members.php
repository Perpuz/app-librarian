<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MemberModel;

class Members extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\MemberModel';
    protected $format    = 'json';

    public function index()
    {
        // Sync from External App-Member if configured
        $memberApiUrl = env('MEMBER_API_URL');
        $integrationSecret = env('INTEGRATION_SECRET');

        if ($memberApiUrl && $integrationSecret) {
            $memberApiUrl = trim($memberApiUrl);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $memberApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-INTEGRATION-SECRET: ' . $integrationSecret,
                'Accept: application/json'
            ]);
            // Fail fast (2s)
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $output = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            // Log for debugging (check public/sync_debug.txt if issues persist)
            @file_put_contents(FCPATH . 'sync_debug.txt', date('Y-m-d H:i:s') . " - Code: $httpCode - Err: $err - Out: " . substr($output, 0, 50) . "\n", FILE_APPEND);

            if ($httpCode == 200 && $output) {
                $json = json_decode($output, true);
                // Handle wrapped response { data: [...] } or direct array [...]
                $externalUsers = isset($json['data']) ? $json['data'] : (is_array($json) ? $json : []);
                
                if (is_array($externalUsers)) {
                    foreach ($externalUsers as $extUser) {
                         if (!isset($extUser['email'])) continue;
                        
                        $existing = $this->model->where('email', $extUser['email'])->first();
                        
                        $memberData = [
                            'name' => $extUser['name'] ?? $extUser['username'] ?? 'Unknown',
                            'email' => $extUser['email'],
                            // Generate a code if new, or keep existing. hash id for uniqueness
                            'member_code' => $existing ? $existing['member_code'] : 'M' . str_pad($extUser['id'] ?? rand(1,9999), 4, '0', STR_PAD_LEFT),
                            'status' => 'active'
                        ];

                        if ($existing) {
                            $this->model->update($existing['id'], $memberData);
                        } else {
                            $this->model->insert($memberData);
                        }
                    }
                }
            }
        }

        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound('No member found with id ' . $id);
    }

    public function create()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[members.email]',
            'member_code' => 'required|is_unique[members.member_code]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getVar();
        // default status
        if(!isset($data->status)) $data->status = 'active';

        if ($this->model->insert($data)) {
            $data->id = $this->model->getInsertID();
            return $this->respondCreated($data);
        }

        return $this->fail('Failed to create member');
    }

    public function update($id = null)
    {
         if (!$this->model->find($id)) {
             return $this->failNotFound('No member found with id ' . $id);
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
        return $this->fail('Failed to update member');
    }

    public function delete($id = null)
    {
        if ($this->model->find($id)) {
            $this->model->delete($id);
            return $this->respondDeleted(['id' => $id]);
        }
        return $this->failNotFound('No member found with id ' . $id);
    }
}
