<?php

namespace App\Controllers\API\API2\Test;

use BluelimeSystem;
use stdClass;

class V1 extends \App\Controllers\BaseController
{
    public function index(){
        $api_key = $_GET['api_key'] ?? '';
        $response = [
            'message' => 'Welcome to test v1 api.',
            'status' => 'success',
            'api_key' => $api_key,
            'data' => []
        ];
        
        return $this->response->setJSON($response);
    }
}