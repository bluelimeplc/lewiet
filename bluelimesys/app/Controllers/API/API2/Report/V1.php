<?php

namespace App\Controllers\API\API2\Report;

use BluelimeSystem;
use BluelimeConfiguration;

class V1 extends \App\Controllers\BaseController
{
    public function index(){
        $response = [
            'message' => 'Welcome to report v1 api.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function ping(){
        $response = [
            'message' => 'pong',
            'status' => 'success',
            'my_time' => date('Y-m-d H:i:s'),
            'your_ip' => $this->request->getIPAddress(),
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function get_survey_report(){
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $from_date = $POST_VAL['from_date'] ?? '';
        $to_date = $POST_VAL['to_date'] ?? '';

        $patientModel = model('Hospital/PatientModel');
        $patientModel = $patientModel->select('COUNT(id) as total, survey')->where('survey <>', '');

        if(!empty($from_date))
            $patientModel = $patientModel->where('created_at >=', $from_date . ' 00:00:00');
        if(!empty($to_date))
            $patientModel = $patientModel->where('created_at <=', $to_date . ' 23:59:59');
        
        $survey_report = $patientModel->groupBy('survey')->findAll();

        $response = BluelimeSystem::create_jwt($survey_report,"success",$api_key);
        return $this->response->setJSON($response);
    }

    
}