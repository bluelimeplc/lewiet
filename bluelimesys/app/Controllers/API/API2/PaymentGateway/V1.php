<?php

namespace App\Controllers\API\API2\PaymentGateway;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeSystem;
use BluelimeConfiguration;

class V1 extends \App\Controllers\BaseController
{
    public function ping(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $payment_gateway = $POST_VAL['payment_gateway'] ?? null;

        $api_key = $_GET['api_key'] ?? '';
        
        $pong = [
            "status"    => "success",
            "message"   => "pong",
            "payment_gateway"   => $payment_gateway,
            "API_KEY"   => $api_key,
            "Your IP"   => $_SERVER['REMOTE_ADDR'],
            "Your User Agent" => $_SERVER['HTTP_USER_AGENT'],
            "My time"   => date('Y-m-d H:i:s')
        ];
        return $this->response->setJSON($pong);
    }

    public function notify(){
        
        $POST_VAL = $this->request->getJSON(true);

        $signature = $_SERVER['HTTP_SIGNATURE'] ?? '';
        $signature2 = BluelimeSystem::create_jwt_signature($POST_VAL, $_GET['api_key']);

        // $log = $this->request->getBody();
        // $log .= "\nsignature: $signature\nsignature2: $signature2\n";
        // BluelimeSystem::write_log('Bluelime/api2','notify_payment',$log);

        if($signature != $signature2){
            echo "Invalid signature";
            die();
        }
        
        $status = $POST_VAL['status'];
        $raw_data = $POST_VAL['raw_data'];
        $payment_id = $POST_VAL['payment_id'];
        $payment_id_segment = explode('-', $payment_id);
        $pos_bill_id = $payment_id_segment[3];

        $pOSBillModel = model('POS/POSBillModel');

        $pos_bill = $pOSBillModel->find($pos_bill_id);
        $digital_payment_detail = BluelimeSystem::convert_to_array($pos_bill['digital_payment_detail'] ?? '[]');
        //$digital_payment_detail['response_data'] ??= [];
        $digital_payment_detail['response_data'][] = [
            'update_time' => date('Y-m-d H:i:s'),
            'payment_id' => $payment_id,
            'raw_data' => $raw_data,
        ];

        $pOSBillModel->update($pos_bill_id, [
            'digital_payment_detail' => BluelimeSystem::convert_array_to_string($digital_payment_detail),
            'digital_payment_status' => $status
        ]);


        echo "Acknowledged";
        die();
    }
}