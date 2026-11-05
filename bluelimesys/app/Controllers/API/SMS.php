<?php

namespace App\Controllers\API;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeConfiguration;
use BluelimeError;
use BluelimeSystem;

class SMS extends \App\Controllers\BaseController
{
    public function index(){
        return "Welcome to SMS api.";
    }

    public function relay_send_sms()
    {
        $mobile = $_POST['mobile'] ??'';
        $message = $_POST['message'];
        $signature = $_POST['signature'];
        $my_security = $mobile . ',' . $message . ',' . date('Y-m-d') . ',' . 'BluelimePlc';
        $my_security = md5($my_security);

        BluelimeSystem::write_log('abiy_test','sms_check',$_POST,'SMS Test');

        if($my_security != $signature)
            return BluelimeSystem::get_error_ajax_return(BluelimeError::get_message('E_4_001'));
        
        $mobile = $this->correct_mobile($mobile);
        if($mobile == '')
            return BluelimeSystem::get_error_ajax_return(BluelimeError::get_message('E_4_002'));

        BluelimeSMS::send_sms([$mobile], $message);
        //BluelimeSystem::send_sms_message($mobile, $message);

        return BluelimeSystem::get_success_ajax_return('Sent 2');
    }

    public function send_tomorrow_appointments(){
        $appointmentModel = model('Hospital/AppointmentModel');
        $appointments = $appointmentModel->get_tomorrow_appointments();
        

        $messages = [];
        foreach($appointments as $row){
            $mobile = $this->correct_mobile($row['mobile']);
            if(empty($mobile))
                continue;
                //$website = "\nBook Appointment @ www.rankderma.com";
                $branch = $_ENV['BRANCH']??"";
            $time_from = BluelimeSystem::roundToNearestThirty(($row['time']));
            $time_range = BluelimeSystem::formatThirtyMinuteRange($time_from);
            $text = "Appointment reminder.\nName: " . $row['full_name'] . "\nDate: " . date('(l) Y-m-d', strtotime($row['appointment_date'])) . "\nTime: " . $time_range . "\n\nRANK Specialized Dermatology Clinic ";
            $message = [
                'mobile' => $mobile,
                'text' => $text.$branch
            ];
            
            array_push($messages,$message);  
        }
        BluelimeSystem::check_sms_service();
        $cnt = 0;
        foreach($messages as $message){
            echo "Mobile: " . $message['mobile'] . "<br>Text: " . $message['text'] . "<br><br>";
            BluelimeSystem::send_sms_message($message['mobile'], $message['text']);
            BluelimeSystem::send_telegram_message($message['mobile'], $message['text']);
            $cnt++;
        }

        $log_message = "Appointment reminder done\n\nSent for $cnt mobiles.\nBranch ".($_ENV['BRANCH']??'');
        BluelimeSystem::send_sms_message('0913144095', $log_message);
        BluelimeSystem::send_telegram_message('0913144095', $log_message);
    }
    public function send_undelivered_results_reminder(){
        $orderMainModel = model('Hospital/OrderMainModel');
        $undelivered_orders = $orderMainModel->where('order_service_type','pathology')->where('order_status',6)->where('result_status',1)->findAll();
        

        $messages = [];
        foreach($undelivered_orders as $row){
            $mobile = $this->correct_mobile($row['mobile']);
            if(empty($mobile))
                continue;
                $branch = $_ENV['BRANCH']??"";
                $text = "ጤና ይስጥልን ".ucwords($row['patient_name'])." የምርመራ ውጤትዎ ስለደረሰ መጥተው መውሰድ ይችላሉ። \n\nራንክ ልዩ የቆዳ ክሊኒክ ".$branch." ቅርንጫፍ";
                $message = [
                'mobile' => $mobile,
                'text' => $text
                ];
            
            array_push($messages,$message);  
        }
        BluelimeSystem::check_sms_service();
        $cnt = 0;
        foreach($messages as $message){
            echo "Mobile: " . $message['mobile'] . "<br>Text: " . $message['text'] . "<br><br>";
            BluelimeSystem::send_sms_message($message['mobile'], $message['text']);
            BluelimeSystem::send_telegram_message($message['mobile'], $message['text']);
            $cnt++;
        }

        $log_message = "Result reminder done\n\nSent for $cnt mobiles.\nBranch ".($_ENV['BRANCH']??'');
        BluelimeSystem::send_sms_message('0911481526', $log_message);
        BluelimeSystem::send_telegram_message('0911481526', $log_message);
    }
    public function send_tomorrow_labrequests_delivery(){
        // $labRequestModel = model('App\Models\LabRequestModel');
        
        // $labrequests = $labRequestModel->get_tomorrow_delivery();
        // //\BluelimeSystem::show_and_die($labrequests);
        // $messages = [];
        // foreach($labrequests as $row){
        //     $mobile = $this->correct_mobile($row['company_mobile']);
        //     if(empty($mobile))
        //         continue;
            
        //     $text = "Labratory Request Delivery Reminder!\nCompany: " . $row['company_name'] . "\nPatient Name: " . $row['patient_name']. "\nType: " . $row['type']. "\nSpecial wish: " . $row['special_wish'] . "\nDelivery Date: " . substr($row['delivery_date'],0,10) . "\n\nRANK Specialized Dermatology Clinic";
        //     $message = [
        //         'mobile' => $mobile,
        //         'text' => $text
        //     ];
            
        //     array_push($messages,$message);  
        // }
        // \BluelimeSystem::check_sms_service();
        //     foreach($messages as $message){
        //         echo "Mobile: " . $message['mobile'] . "<br>Text: " . $message['text'] . "<br><br>";
        //         \BluelimeSystem::send_sms_message($message['mobile'], $message['text']);
        //         \BluelimeSystem::send_telegram_message($message['mobile'], $message['text']);
        //     }
        
    }
    public function import_web_appointment(){
                \BluelimeSystem::import_appointment();
    }
    

    private function correct_mobile($mobile){
        $mobile = trim($mobile);
        $mobile = str_ireplace(' ', '', $mobile);
        $mobile = @intval($mobile);
        $mobile = "0$mobile";

        $new_mobile = '';
        if(strlen($mobile) == 10)
            $new_mobile = $mobile;
        return $new_mobile;
    }
}