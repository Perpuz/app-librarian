<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during normal execution.
     * However, when an abnormal state is found, it should return
     * an instance of CodeIgniter\HTTP\Response.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        if (!$header) {
            $header = $request->getHeaderLine('Authorization');
        }

        if (!$header) {
            return service('response')
                ->setJSON(['status' => 401, 'error' => 'Token Required'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $token = null;
        // Extract token from "Bearer <token>"
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        }

        if (is_null($token)) {
            return service('response')
                ->setJSON(['status' => 401, 'error' => 'Invalid Token Format'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            // Add decoded user data to request object if needed
            $request->user = $decoded;

        } catch (Exception $e) {
            return service('response')
                ->setJSON(['status' => 401, 'error' => 'Invalid or Expired Token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
