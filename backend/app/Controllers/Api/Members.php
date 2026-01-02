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
                        
                        if ($existing) {
                             // Fix member_code if it was temporary (starts with M) and we have a real NIM
                             if (str_starts_with($existing['member_code'], 'M') && isset($extUser['nim'])) {
                                 $this->model->update($existing['id'], ['member_code' => $extUser['nim']]);
                             }
                             // PERPUZ-FIX: Do NOT overwrite Name/Email. Use local data as master if it exists.
                             // This allows Admin to edit details without being reverted by Sync.
                         } else {
                             // Insert New
                             $memberData = [
                                'name' => $extUser['name'] ?? $extUser['username'] ?? 'Unknown',
                                'email' => $extUser['email'],
                                'member_code' => $extUser['nim'] ?? 'M' . str_pad($extUser['id'] ?? rand(1,9999), 4, '0', STR_PAD_LEFT),
                                'status' => 'active'
                             ];
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
         @file_put_contents(FCPATH . 'action_debug.txt', date('Y-m-d H:i:s') . " - UPDATE Request ID: $id\n", FILE_APPEND);
         
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

        @file_put_contents(FCPATH . 'action_debug.txt', date('Y-m-d H:i:s') . " - Data: " . json_encode($data) . "\n", FILE_APPEND);

        if (empty($data)) {
             @file_put_contents(FCPATH . 'action_debug.txt', " - Error: Empty Data\n", FILE_APPEND);
            return $this->fail('No data provided for update or invalid JSON');
        }

        // Validate uniqueness if changing unique fields
        $rules = [
            'email' => "valid_email|is_unique[members.email,id,{$id}]",
            'member_code' => "is_unique[members.member_code,id,{$id}]"
        ];
        
        // Only validate fields present in data
        $validationRules = array_intersect_key($rules, $data);
        if (!empty($validationRules) && !$this->validate($validationRules)) {
             $errs = json_encode($this->validator->getErrors());
             @file_put_contents(FCPATH . 'action_debug.txt', " - Valid Error: $errs\n", FILE_APPEND);
             return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->model->update($id, $data)) {
            @file_put_contents(FCPATH . 'action_debug.txt', " - Success Update Local\n", FILE_APPEND);
            
            // Push Update to App-Member
            $memberApiUrl = env('MEMBER_API_URL');
            $integrationSecret = env('INTEGRATION_SECRET');
            
            // Re-fetch updated data to be sure
            $updatedMember = $this->model->find($id);
            $nim = $updatedMember['member_code'];

            if ($memberApiUrl && $integrationSecret && $nim && !str_starts_with($nim, 'M')) {
                // Prepare URL: .../api/integration/users/{nim}
                // Assumes MEMBER_API_URL ends with /users
                $targetUrl = rtrim($memberApiUrl, '/') . '/' . $nim;
                
                $pushData = [
                    'name' => $updatedMember['name'],
                    'email' => $updatedMember['email']
                    // Password update not supported via this sync yet
                ];

                try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $targetUrl);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pushData));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'X-INTEGRATION-SECRET: ' . $integrationSecret,
                        'Accept: application/json'
                    ]);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                    $resp = curl_exec($ch);
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    @file_put_contents(FCPATH . 'action_debug.txt', " - Sync Push to $targetUrl: Code $code\n", FILE_APPEND);
                } catch (\Exception $e) {
                    @file_put_contents(FCPATH . 'action_debug.txt', " - Sync Push Error: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }

            return $this->respond($data);
        }
        @file_put_contents(FCPATH . 'action_debug.txt', " - Fail Update (Model)\n", FILE_APPEND);
        return $this->fail('Failed to update member');
    }

    public function delete($id = null)
    {
        @file_put_contents(FCPATH . 'action_debug.txt', date('Y-m-d H:i:s') . " - DELETE Request ID: $id\n", FILE_APPEND);
        
        if ($this->model->find($id)) {
            // Check for transactions constraint
            $trxModel = new \App\Models\TransactionModel();
            $count = $trxModel->where('member_id', $id)->countAllResults();
            
            if ($count > 0) {
                @file_put_contents(FCPATH . 'action_debug.txt', " - Blocked: Has $count Transactions\n", FILE_APPEND);
                return $this->fail('Cannot delete member: This member has associated transactions. Please delete the transactions first.');
            }

            $this->model->delete($id);
            @file_put_contents(FCPATH . 'action_debug.txt', " - Success Delete\n", FILE_APPEND);
            return $this->respondDeleted(['id' => $id]);
        }
        return $this->failNotFound('No member found with id ' . $id);
    }
}
