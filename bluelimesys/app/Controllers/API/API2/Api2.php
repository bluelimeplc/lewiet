<?php

namespace App\Controllers\API\API2;

class Api2 extends \App\Controllers\BaseController
{
    public function index(){
        $response = [
            'message' => 'Welcome to api2.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function test(){
        $response = [
            'message' => 'Every thing looks perfect.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }
}