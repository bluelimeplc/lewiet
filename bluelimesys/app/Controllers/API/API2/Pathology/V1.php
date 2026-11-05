<?php

namespace App\Controllers\API\API2\Pathology;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeSystem;
use BluelimeConfiguration;

class V1 extends \App\Controllers\BaseController
{
    public function index(){
        $response = [
            'message' => 'Welcome to pathology v1 api.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function get_biopsy_orders(){
        $api_key = $_GET['api_key'] ?? '';
        $orderMainModel = model('Hospital/OrderMainModel');
        $userModel = model('System/UserModel');
        $orders = $orderMainModel->select('*, \''.$_ENV['BRANCH'].'\' AS branch')->where('order_service_type','Pathology')->where('order_status >=',2)->where('hide',0)->where('order_status <=',5)->findAll();
        $company_header = BluelimeSystem::get_company_header();
        unset($company_header['logo_printouts']);
        unset($company_header['logo_small']);
        unset($company_header['logo']);
        foreach($orders as $index => $rt){
            $orders[$index]['ordered_by_name'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"";
            $orders[$index]['age'] = BluelimeSystem::calculate_age($rt['date_of_birth']);
        }
        $payload = [
            'company_header' => $company_header,
            'orders' => $orders
        ];

        $response = BluelimeSystem::create_jwt($payload,"success",$api_key);
    
        
        
        //BluelimeSystem::show_and_die($orders['company_header']);
        return $this->response->setJSON($response);
        
    }
    public function get_biopsy_completed(){
        $api_key = $_GET['api_key'] ?? '';
        $orderMainModel = model('Hospital/OrderMainModel');
        $userModel = model('System/UserModel');
        $orders = $orderMainModel->select('*, \''.$_ENV['BRANCH'].'\' AS branch')->where('order_service_type','Pathology')->where('hide',0)->where('order_status >=',6)->findAll();
        $company_header = BluelimeSystem::get_company_header();
        unset($company_header['logo_printouts']);
        unset($company_header['logo_small']);
        unset($company_header['logo']);
        foreach($orders as $index => $rt){
            $orders[$index]['ordered_by_name'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"";
            $orders[$index]['age'] = BluelimeSystem::calculate_age($rt['date_of_birth']);
        }
        $payload = [
            'company_header' => $company_header,
            'orders' => $orders
        ];

        $response = BluelimeSystem::create_jwt($payload,"success",$api_key);
    
        
        
        //BluelimeSystem::show_and_die($orders['company_header']);
        return $this->response->setJSON($response);
        
    }

    public function get_order_result(){
        $POST_VAL = file_get_contents('php://input');
        //BluelimeSystem::show_and_die($POST_VAL);
        $POST_VAL = json_decode($POST_VAL, true);
        
        $order_id = $POST_VAL['order_id'];
        $api_key = $_GET['api_key'] ?? '';

        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $biopsy_result = $biopsyResultModel->where('order_id', $order_id)->orderBy('id', 'DESC')->first();
        
        $response = BluelimeSystem::create_jwt($biopsy_result,"success",$api_key);
        return $this->response->setJSON($response);
    }
    public function get_order(){
        $POST_VAL = file_get_contents('php://input');
        //BluelimeSystem::show_and_die($POST_VAL);
        $POST_VAL = json_decode($POST_VAL, true);
        
        $order_id = $POST_VAL['order_id'];
        $api_key = $_GET['api_key'] ?? '';

        $orderMainModel = model('Hospital/OrderMainModel');
        $order = $orderMainModel->where('id', $order_id)->first();
        
        $response = BluelimeSystem::create_jwt($order,"success",$api_key);
        return $this->response->setJSON($response);
    }
    public function get_scanned_document(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = json_decode($POST_VAL, true);
        
        $order_id = $POST_VAL['order_id'];
        $api_key = $_GET['api_key'] ?? '';

        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $biopsy_result = $biopsyResultModel->where('order_id', $order_id)->first();
        $orderMainModel = model('Hospital/OrderMainModel');
        $scanned_documents = $orderMainModel->where('id', $order_id)->findColumn('scanned_documents');;
        BluelimeSystem::show_and_die($scanned_documents);
        
        $response = BluelimeSystem::create_jwt($biopsy_result,"success",$api_key);
        return $this->response->setJSON($response);
    }
    public function approveresult()
    {
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = json_decode($POST_VAL, true);
        $order_id = $POST_VAL['order_id'];
        $user_id = $POST_VAL['user_id'];
        $api_key = $_GET['api_key'] ?? '';


        $orderMainModel = model('Hospital/OrderMainModel');
        $notificationModel = model('Hospital/NotificationModel');

        $data = [
            'order_status' => 6,
            'result_status' => 1,
            'result_approved_by' => $user_id,
            'result_approved_date' => date('Y-m-d H:i:s')
        ];
        $orderMainModel->update(intval($order_id),$data);
        $order = $orderMainModel->where('id',$order_id)->find()[0];
        //BluelimeSystem::show_and_die($order['ordered_by']);       
            $notfiy_data = [
                'notification_type'=>  "Pathology",
                'description'=> $order['patient_name'],
                'link_type'=> 'order_main_id',
                'link_id'=> $order_id,
                'user_id'=> $order['ordered_by'],
             ];
            $notificationModel->insert($notfiy_data,true);
        
            //$website = "\nBook your Appointment @ www.rankderma.com";
            $branch = $_ENV['BRANCH']??"";
            if($branch == "Wollosefer"){
                $branch = "ወሎ ሰፈር";
            }
            else if($branch == "Lebu"){
                $branch = "ለቡ";
            }
            else if($branch == "Gazebo"){
                $branch = "ጋዜቦ";
            }
            $welcome_message = "ጤና ይስጥልን ".ucwords($order['patient_name'])." የምርመራ ውጤትዎ ስለደረሰ መጥተው መውሰድ ይችላሉ። \n\nራንክ ልዩ የቆዳ ክሊኒክ ".$branch." ቅርንጫፍ";
            //$telegram_bot = "\nJoin Telegram: https://t.me/RankDerma_bot";
    
           // BluelimeSystem::send_telegram_message($mobile, $welcome_message);
            if(BluelimeSystem::check_sms_service()){
                BluelimeSystem::send_sms_message($order['mobile'], $welcome_message);
            }
            BluelimeSystem::send_telegram_message($order['mobile'], $welcome_message);
        
            
        $result = ['updated'];
        $signature = BluelimeSystem::create_jwt_signature($result, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$result, 'signature'=>$signature]);
            
       
    }
    public function get_company_header(){
        $company_header = BluelimeSystem::get_company_header();
        $api_key = $_GET['api_key'] ?? '';
        
        $response = BluelimeSystem::create_jwt($company_header,"success",$api_key);
        return $this->response->setJSON($response);
    }

    public function save_order_result(){
        $POST_VAL = file_get_contents('php://input');
        //BluelimeSystem::show_and_die($POST_VAL);
        $POST_VAL = json_decode($POST_VAL, true);
        $order_id = $POST_VAL['order_id'];
        
        
        $biopsy_number = $POST_VAL['biopsy_number'];
        $gross_morphology = $POST_VAL['gross_morphology'];
        $microscopy = $POST_VAL['microscopy'];
        $conclusion = $POST_VAL['conclusion'];
        $recommendation = $POST_VAL['recommendation'];
        $scanned_documents = $POST_VAL['scanned_documents']??"";
        $order_status = $POST_VAL['order_status']??0;
        $api_key = $_GET['api_key'] ?? '';
        //BluelimeSystem::show_and_die($order_id);
        $orderMainModel = model('Hospital/OrderMainModel');
        
        $data_order = [
            'order_status' => $order_status
        ];
        if($order_status == 5){
            $orderMainModel->update(intval($order_id),$data_order);
        }

        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $biopsy_result = $biopsyResultModel->where('order_id', $order_id)->first();

        if(empty($biopsy_result)){
            $orderMainModel = model('Hospital/OrderMainModel');
            $order_main = $orderMainModel->find($order_id);
            $userModel = model('System/UserModel');
            
            
            if(!empty($order_main)){
                $order_main['result_approved_by']==-1?$doctor_name="N/A":
                $doctor_name = $userModel->where('id', $order_main['result_approved_by'])->findColumn('name')[0];
                //BluelimeSystem::show_and_die($doctor_name);
                $biopsy_result = [
                    'patient_id' => $order_main['patient_id'],
                    'patient_name' => $order_main['patient_name'],
                    'sex' => $order_main['sex'],
                    'age' => BluelimeSystem::calculate_age($order_main['date_of_birth']),
                    'mobile' => $order_main['mobile'],
                    'doctor_id' => $order_main['result_approved_by'],
                    'doctor_name' => $doctor_name
                ];
            }
            $biopsy_result['revision']= 0;
            $biopsy_result['micrscopic_image'] = $scanned_documents??"";
        }

        $biopsy_result['order_id'] = $order_id;
       // $biopsy_result['revision']= 0;
        
        
       // BluelimeSystem::show_and_die($biopsy_result);
        $order_result = $biopsy_result['biopsy_result'] ?? [];
        $order_result['biopsy_number'] = $biopsy_number;
        $order_result['gross_morphology'] = $gross_morphology;
        $order_result['microscopy'] = $microscopy;
        $order_result['conclusion'] = $conclusion;
        $order_result['recommendation'] = $recommendation;

        $biopsy_result['biopsy_result'] = json_encode($order_result);
        $biopsy_result['revision'] = $biopsy_result['revision']+1;
        $biopsy_result['micrscopic_image'] = $scanned_documents;

       //BluelimeSystem::show_and_die($biopsy_result);

        $biopsyResultModel->save($biopsy_result);
        $data = $biopsyResultModel->where('order_id',$order_id)->first();
        $response = BluelimeSystem::create_jwt($data,"success",$api_key);
        return $this->response->setJSON($response);
    }

    public function update_order(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = json_decode($POST_VAL, true);
       
        
        $order_id = $POST_VAL['order_id'];
        $additional_info = $POST_VAL['additional_info'] ?? [];
        //BluelimeSystem::show_and_die($additional_info);
        $api_key = $_GET['api_key'] ?? '';

        $orderMainModel = model('Hospital/OrderMainModel');
        
        // Get current order data
        $order = $orderMainModel->find($order_id);
        if (!$order) {
            $response = BluelimeSystem::create_jwt(null, "Order not found", $api_key);
            return $this->response->setJSON($response);
        }

        // Decode existing additional_info or initialize as empty array
        $current_additional_info = [];
        if (isset($order['additional_info'])) {
            if (is_array($order['additional_info'])) {
                $current_additional_info = $order['additional_info'];
            } elseif (is_string($order['additional_info']) && !empty(trim($order['additional_info']))) {
                $decoded = json_decode($order['additional_info'], true);
                if (is_array($decoded)) {
                    $current_additional_info = $decoded;
                }
            }
        }

        // Merge new additional_info with existing data
        $updated_additional_info = array_merge($current_additional_info, $additional_info);

        // Update the order with new additional_info
        $data = [
            'additional_info' => json_encode($updated_additional_info)
        ];
        
        $orderMainModel->update($order_id, $data);

        // Return updated order data
        $updated_order = $orderMainModel->find($order_id);
        $response = BluelimeSystem::create_jwt($updated_order, "success", $api_key);
        return $this->response->setJSON($response);
    }

    public function reverse_approval()
    {
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = json_decode($POST_VAL, true);
        $order_id = $POST_VAL['order_id'];
        $user_id = $POST_VAL['user_id'];
        $reason = $POST_VAL['reason'] ?? '';
        $api_key = $_GET['api_key'] ?? '';

        $orderMainModel = model('Hospital/OrderMainModel');
        $notificationModel = model('Hospital/NotificationModel');

        // Get current order
        $order = $orderMainModel->find($order_id);
        if (!$order) {
            $response = BluelimeSystem::create_jwt(null, "Order not found", $api_key);
            return $this->response->setJSON($response);
        }

        // Check if order is approved
        if ($order['result_status'] != 1) {
            $response = BluelimeSystem::create_jwt(null, "Order is not approved", $api_key);
            return $this->response->setJSON($response);
        }

        // Reverse approval: set status back to pending/result entry (status 5)
        $data = [
            'order_status' => 5, // Back to result entry status
            'result_status' => 0, // Unapproved
            'result_approved_by' => null,
            'result_approved_date' => null
        ];

        // Add reverse approval note to result_remark
        $current_remark = $order['result_remark'] ?? '';
        $reverse_note = "Approval reversed by user ID {$user_id} on " . date('Y-m-d H:i:s');
        if (!empty($reason)) {
            $reverse_note .= " - Reason: {$reason}";
        }
        $new_remark = $current_remark . "\n" . $reverse_note;
        $data['result_remark'] = trim($new_remark);

        $orderMainModel->update(intval($order_id), $data);

        // Send notification to the original orderer
        $notfiy_data = [
            'notification_type' => "Pathology",
            'description' => $order['patient_name'] . ' - Result approval reversed',
            'link_type' => 'order_main_id',
            'link_id' => $order_id,
            'user_id' => $order['ordered_by'],
        ];
        $notificationModel->insert($notfiy_data, true);

        $result = ['approval_reversed'];
        $signature = BluelimeSystem::create_jwt_signature($result, $api_key);
        return $this->response->setJSON(['status' => 'success', 'data' => $result, 'signature' => $signature]);
    }

    public function amend_result()
    {
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = json_decode($POST_VAL, true);
        $biopsy_result_id = $POST_VAL['original_biopsy_result_id'];
        $user_id = $POST_VAL['user_id'];
        $amendment_notes = $POST_VAL['amendment_notes'] ?? '';
        $updated_result = $POST_VAL['updated_result'] ?? [];
        
        $api_key = $_GET['api_key'] ?? '';

        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $notificationModel = model('Hospital/NotificationModel');

        // Get current biopsy result
        $current_result = $biopsyResultModel->where('id', $biopsy_result_id)->first();
        if (!$current_result) {
            $response = BluelimeSystem::create_jwt(null, "Biopsy result not found", $api_key);
            return $this->response->setJSON($response);
        }

        // Get order details
        $order = $orderMainModel->find($current_result['order_id']);
        if (!$order) {
            $response = BluelimeSystem::create_jwt(null, "Order not found", $api_key);
            return $this->response->setJSON($response);
        }
        
        // Decode current biopsy_result JSON
        $current_biopsy_data = [];
        if (!empty($current_result['biopsy_result'])) {
            if (is_string($current_result['biopsy_result'])) {
                $current_biopsy_data = json_decode($current_result['biopsy_result'], true) ?? [];
            } elseif (is_array($current_result['biopsy_result'])) {
                $current_biopsy_data = $current_result['biopsy_result'];
            }
        }

        // Merge updated result with current data
        $amended_biopsy_data = array_merge($current_biopsy_data, $updated_result);
//BluelimeSystem::show_and_die($amended_biopsy_data);
        // Add amendment notes
        if (!empty($amendment_notes)) {
            $amended_biopsy_data['amended_by'] = $user_id;
            $amended_biopsy_data['amended_date'] = date('Y-m-d H:i:s');
        }

        // Create new result entry with incremented revision
        $new_result = $current_result;
        unset($new_result['id']); // Remove ID for new insert
        $new_result['biopsy_result'] = json_encode($amended_biopsy_data);
        $new_result['revision'] = ($current_result['revision'] ?? 0) + 1;
        //BluelimeSystem::show_and_die($new_result);
        // Insert the amended result
        $biopsyResultModel->insert($new_result);

        

        // Add amendment note to order result_remark
        $current_remark = $order['result_remark'] ?? '';
        $amendment_note = "Result amended by user ID {$user_id} on " . date('Y-m-d H:i:s') . " - Revision: " . $new_result['revision'];
        if (!empty($amendment_notes)) {
            $amendment_note .= " - Notes: {$amendment_notes}";
        }
        $new_remark = $current_remark . "\n" . $amendment_note;
        $orderMainModel->update($current_result['order_id'], ['result_remark' => trim($new_remark)]);

        // Send notification to the original orderer
        $notfiy_data = [
            'notification_type' => "Pathology",
            'description' => $order['patient_name'] . ' - Result amended',
            'link_type' => 'order_main_id',
            'link_id' => $current_result['order_id'],
            'user_id' => $order['ordered_by'],
        ];
        $notificationModel->insert($notfiy_data, true);

        $result = ['result_amended', 'new_revision' => $new_result['revision']];
        $signature = BluelimeSystem::create_jwt_signature($result, $api_key);
        return $this->response->setJSON(['status' => 'success', 'data' => $result, 'signature' => $signature]);
    }
}