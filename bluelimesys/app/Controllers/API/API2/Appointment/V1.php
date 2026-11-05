<?php

namespace App\Controllers\API\API2\Appointment;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeSystem;
use BluelimeConfiguration;

class V1 extends \App\Controllers\BaseController
{
    public function index(){
        $response = [
            'message' => 'Welcome to appointment v1 api.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function get_physician_list(){
        $api_key = $_GET['api_key'] ?? '';
        $userModel = model('System/UserModel');
            $physician_list = $userModel
                ->select('id, username, name, title, role,\''.$_ENV['BRANCH'].'\' AS branch')
                ->groupStart()
                    ->where('role', BluelimeConfiguration::USER_ROLE::DOCTOR)
                    ->orGroupStart()
                        ->where('role', BluelimeConfiguration::USER_ROLE::NURSE)
                        ->like('extra', BluelimeConfiguration::USER_ROLE_EXTRA::SHOW_ON_APPOINTMENT)
                    ->groupEnd()
                ->groupEnd()
                ->where('active',1)
                ->findAll();
        
        $response = BluelimeSystem::create_jwt($physician_list,"success",$api_key);
        return $this->response->setJSON($response);
    }

    public function get_weekly_physician_schedule(){
        $api_key = $_GET['api_key'] ?? '';
        $scheduleModel = model('Hospital/ScheduleModel');
        //$physician_schedule_all = $scheduleModel->where('status',1)->findAll();

        $query = 'SELECT  `hospital_schedule`.* FROM `hospital_schedule` INNER JOIN
(SELECT  MAX(`hospital_schedule`.`id`) AS `id`, `hospital_schedule`.`doctor_id`,`hospital_schedule`.`day`  FROM `hospital_schedule` INNER JOIN 
(
SELECT MAX(`schedule_start_date`)as `schedule_start_date`,`day`,`doctor_id` FROM `hospital_schedule` 
WHERE `status` = 1 AND
`schedule_start_date` <= \''.date('Y-m-d').'\' AND
`deleted_at` IS NULL
GROUP BY `day`,`doctor_id`) AS `flitered_schedule`

ON `hospital_schedule`.`doctor_id` = `flitered_schedule`.`doctor_id` AND
`hospital_schedule`.`day` = `flitered_schedule`.`day` AND
`hospital_schedule`.`schedule_start_date` = `flitered_schedule`.`schedule_start_date`

WHERE `status` = 1 AND
`deleted_at` IS NULL

GROUP BY `hospital_schedule`.`doctor_id`,`hospital_schedule`.`day`) as `latest_schedule`

ON `latest_schedule`.`id` = `hospital_schedule`.`id`  
ORDER BY `hospital_schedule`.`doctor_id` DESC';

        $physician_schedule_all = $scheduleModel->db->query($query)->getResultArray();

        $userModel = model('System/UserModel');
        $physician_list = $userModel->select('id, username')->where('active',1)->findAll();
        $physician_list = BluelimeSystem::index_object_list_array_by_unique_field($physician_list, 'id');

        $physician_schedule = [];
        foreach($physician_schedule_all as $schedule){
            $user_name = $physician_list['id_'.$schedule['doctor_id']]['username'];
            if(!isset($physician_schedule[$user_name])){
                $physician_schedule[$user_name] = [
                    "1"=>['morning'=>0,'afternoon'=>0],
                    "2"=>['morning'=>0,'afternoon'=>0],
                    "3"=>['morning'=>0,'afternoon'=>0],
                    "4"=>['morning'=>0,'afternoon'=>0],
                    "5"=>['morning'=>0,'afternoon'=>0],
                    "6"=>['morning'=>0,'afternoon'=>0],
                    "7"=>['morning'=>0,'afternoon'=>0],
                ];
            }

            if($schedule['start_time'] < '12:00:00')
                $physician_schedule[$user_name][$schedule['day']]['morning'] = 1;
            if($schedule['end_time'] >= '15:00:00')
                $physician_schedule[$user_name][$schedule['day']]['afternoon'] = 1;
        }

        $response = BluelimeSystem::create_jwt($physician_schedule,"success",$api_key);
        return $this->response->setJSON($response);
    }

    public function get_daily_physician_schedule(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $api_key = $_GET['api_key'] ?? '';
        $date = $POST_VAL['date'] ?? '2025-12-16';
        $doctor_username = $POST_VAL['doctor_username'] ?? 'ruth';

        $userModel = model('System/UserModel');
        $doctor = $userModel->where('username',$doctor_username)->first();
        $physician_id = $doctor['id'] ?? -1;

        $scheduleModel = model('Hospital/ScheduleModel');
        $day_of_week = BluelimeSystem::day_to_no(date('D', strtotime($date)));
        $physician_schedule = $scheduleModel->where('doctor_id',$physician_id)->where('day',$day_of_week)->where('status',1)->where('schedule_start_date <=',$date)->orderBy('schedule_start_date','DESC')->first();

        $appointmentModel = model('Hospital/AppointmentModel');
        $appointments = $appointmentModel->where('doctor_id',$physician_id)->where('appointment_date',$date)->where('appointment_status',BluelimeConfiguration::APPOINTMENT_ACTIVE)->orderBy('time','ASC')->findAll();

        $appointment_slots = [];
        if(!empty($physician_schedule)){
            $start_time = strtotime($date." ".$physician_schedule['start_time']);
            $end_time = strtotime($date." ".$physician_schedule['end_time']);
            $per_patient_time = intval($physician_schedule['per_patient_time'])*60;

            for($time = $start_time; $time + $per_patient_time <= $end_time; $time += $per_patient_time){
                //echo $time.' ';
                //$time_slot = date_format(date_create($time), 'H:i A');
                $time_slot = date('h:i A', $time);
                $appointment_slots[$time_slot] = [
                    'time' => date('h:i A', $time),
                    'hour' => date('H', $time),
                    'minute' => date('i', $time),
                    'status' => 'available',
                    'patient_name' => '',
                    'remarks' => ''
                ];
            }

            foreach($appointments as $appointment){
                $appointment_time = $appointment['time'];
                if(isset($appointment_slots[$appointment_time])){
                    $appointment_slots[$appointment_time]['status'] = 'booked';
                    $appointment_slots[$appointment_time]['patient_name'] = $appointment['full_name'];
                    $appointment_slots[$appointment_time]['appointment_reason'] = $appointment['appointment_reason'];
                }
            }
        }

        $response = BluelimeSystem::create_jwt($appointment_slots,"success",$api_key);
        return $this->response->setJSON($response);
    }

    public function get_patient_info_by_mobile(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $api_key = $_GET['api_key'] ?? '';
        $mobile = $POST_VAL['mobile'] ?? '';

        $patientModel = model('Hospital/PatientModel');
        $patient_info = $patientModel->where('mobile',$mobile)->orderBy('first_name')->orderBy('middle_name')->orderBy('last_name')->findAll();
        //echo $mobile;

        $data = [];
        if(!empty($patient_info)){
            foreach($patient_info as $patient){
                $data[] = [
                    'mobile' => $patient['mobile'],
                    'name'   => $patient['first_name'] .' '.$patient['middle_name'] . ' '.$patient['last_name'],
                    'mrn'    => $patient['id'],
                ];
            }
        }
        
        $response = BluelimeSystem::create_jwt($data,"success",$api_key);
        return $this->response->setJSON($response);
    }

    private function send_appointment_message($appointment_time, $appointment_date, $doctor_name, $patient_mobile, $canceled = false){
        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        
        $time_from = BluelimeSystem::roundToNearestThirty($appointment_time);
        $time_range = BluelimeSystem::formatThirtyMinuteRange($time_from);
        $appointment_date_w = date('Y-m-d (l)', strtotime($appointment_date));
        $appointment_message = "Appointment Registered\nDate: $appointment_date_w \nTime: $time_range\nDoctor name: $doctor_name\n\nRANK Specialized Dermatology Clinic\n";
        if($canceled)
            $appointment_message = "Appointment Canceled\nDate: $appointment_date_w \nTime: $time_range\nDoctor name: $doctor_name\n\nRANK Specialized Dermatology Clinic\n";
        
        //$telegram_bot = "Join Telegram: https://t.me/RankDerma_bot";
        $sms_message = $appointment_message.$branch;
        BluelimeSystem::send_sms_message($patient_mobile, $sms_message);

        $telegram_message = $appointment_message.$branch;
        BluelimeSystem::send_telegram_message($patient_mobile, $telegram_message);

    }

    public function book_appointment(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $api_key = $_GET['api_key'] ?? '';
        $doctor_username = $POST_VAL['doctor_username'];
        $appointment_date = $POST_VAL['date'];
        $appointment_time = $POST_VAL['time'];
        $patient_name = $POST_VAL['patient_name'];
        $patient_mobile = $POST_VAL['patient_mobile'];
        $patient_mrn    = $POST_VAL['patient_mrn'];
        $appointment_reason = $POST_VAL['user_company'] ?? '';
        $user_email = $POST_VAL['user_email'] ?? '';

        $userModel = model('System/UserModel');
        $doctor = $userModel->where('username',$doctor_username)->first();
        $physician_id = $doctor['id'] ?? -1;
        $doctor_name = $doctor['name'];

        $appointmentModel = model('Hospital/AppointmentModel');
        $appointment_data = [
            'doctor_id' => $physician_id,
            'doctor_name' => $doctor_name,   
            'appointment_date' => $appointment_date,
            'time' => $appointment_time,
            'patient_id' => $patient_mrn,
            'full_name' => $patient_name,
            'mobile' => $patient_mobile,
            'appointment_reason' => $appointment_reason,
            'appointment_status' => BluelimeConfiguration::APPOINTMENT_ACTIVE,
            'registered_by_name' => $user_email,
        ];
        $appointment = $appointmentModel->where('doctor_id',$physician_id)->where('appointment_date',$appointment_date)->where('time',$appointment_time)->first();
        if(!empty($appointment)){
            $response_data = [
                'appointment_id' => $appointment['id'],
                'status' => 'failed',
                'message' => 'Time slot already booked'
            ];

            $response = BluelimeSystem::create_jwt($response_data,"success",$api_key);
            return $this->response->setJSON($response);
        }

        $appointment_id = $appointmentModel->insert($appointment_data);

        $this->send_appointment_message($appointment_time, $appointment_date, $doctor_name, $patient_mobile);
        
        $response_data = [
            'appointment_id' => $appointment_id,
            'status' => 'success'
        ];

        $response = BluelimeSystem::create_jwt($response_data,"success",$api_key);
        return $this->response->setJSON($response);
    }

    public function get_appointment_list(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $api_key = $_GET['api_key'] ?? '';

        $date_filter = $POST_VAL['date_filter'] ?? '';
        $patient_search = $POST_VAL['patient_search'] ?? '';

        $appointmentModel = model('Hospital/AppointmentModel');
        
        if(($date_filter == '') && ($patient_search == '')){
            $date_filter = date('Y-m-d');
        }

        if($date_filter != ''){
            $appointmentModel = $appointmentModel->where('appointment_date',$date_filter);
        }
            
        if($patient_search != ''){
            if(is_numeric($patient_search)){
                $mobile = BluelimeSystem::correct_mobile($patient_search);
                if($mobile != '')
                    $appointmentModel = $appointmentModel->where('mobile',$mobile);
            }
            else{
                $patient_search = preg_replace('/\s+/', ' ', $patient_search);
                $appointmentModel = $appointmentModel->like('full_name',$patient_search);
            }
        }

        $userModel = model('System/UserModel');
        $users = $userModel->findAll();
        $users = BluelimeSystem::index_object_list_array_by_unique_field($users, 'id');

        $appointments = $appointmentModel->findAll();
        foreach($appointments as &$appointment){
            $appointment['physician_username'] = $users['id_'.$appointment['doctor_id']]['username']??'';

            if($appointment['mobile'] != $patient_search)
                $appointment['mobile'] = substr($appointment['mobile'],0,4)."****".substr($appointment['mobile'],-2,2);
        }

        $response = BluelimeSystem::create_jwt($appointments,"success",$api_key);
        return $this->response->setJSON($response);
    }

    function get_appointment_detail(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $api_key = $_GET['api_key'] ?? '';

        $appointment_id = $POST_VAL['appointment_id'];
        $appointmentModel = model('Hospital/AppointmentModel');
        $appointment = $appointmentModel->find($appointment_id);
        $response = BluelimeSystem::create_jwt($appointment,"success",$api_key);
        return $this->response->setJSON($response);
    }

    function update_appointment_reason(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $api_key = $_GET['api_key'] ?? '';

        $appointment_id = $POST_VAL['appointment_id'];
        $reason = $POST_VAL['reason'];
        $custom_note = $POST_VAL['custom_note'];

        $appointmentModel = model('Hospital/AppointmentModel');

        $response = BluelimeSystem::create_jwt([],"success",$api_key);
        if($appointmentModel->update($appointment_id, ['custom_note'=>$custom_note, 'cancelation_reason'=>$reason]))
            return $this->response->setJSON($response);
    
        $response = BluelimeSystem::create_jwt([],"error",$api_key);
        return $this->response->setJSON($response);
    }

    function update_appointment(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $api_key = $_GET['api_key'] ?? '';

        $appointment_id = $POST_VAL['appointment_id'];
        $time = $POST_VAL['time'];
        $appointment_date = $POST_VAL['appointment_date'];
        $physician_username = $POST_VAL['physician_username'];
        
        $userModel = model('System/UserModel');
        $user = $userModel->where('username', $physician_username)->first();

        $update_data = [
            'time'=>$time,
            'appointment_date'=>$appointment_date,
            'doctor_name' => $user['name'],
            'doctor_id' => $user['id'],
            'appointment_status' => BluelimeConfiguration::APPOINTMENT_ACTIVE
        ];

        $appointmentModel = model('Hospital/AppointmentModel');

        $appointment = $appointmentModel->find($appointment_id);

        $response = BluelimeSystem::create_jwt([],"success",$api_key);
        if($appointmentModel->update($appointment_id, $update_data)){
            $this->send_appointment_message($time, $appointment_date, $user['name'], $appointment['mobile']);
            return $this->response->setJSON($response);
        }
    
        $response = BluelimeSystem::create_jwt([],"error",$api_key);
        return $this->response->setJSON($response);
    }

    function cancel_appointment(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $api_key = $_GET['api_key'] ?? '';

        $appointment_id = $POST_VAL['appointment_id'];
        $appointment_status = $POST_VAL['appointment_status'];

        $update_data = [
            'appointment_status'=>$appointment_status
        ];

        $appointmentModel = model('Hospital/AppointmentModel');

        $appointment = $appointmentModel->find($appointment_id);


        $response = BluelimeSystem::create_jwt([],"success",$api_key);
        if($appointmentModel->update($appointment_id, $update_data)){
            if($appointment_status == BluelimeConfiguration::APPOINTMENT_CANCELED)
                $this->send_appointment_message($appointment['time'], $appointment['appointment_date'], $appointment['doctor_name'], $appointment['mobile'], true);
            else 
                $this->send_appointment_message($appointment['time'], $appointment['appointment_date'], $appointment['doctor_name'], $appointment['mobile']);

            return $this->response->setJSON($response);
        }
    
        $response = BluelimeSystem::create_jwt([],"error",$api_key);
        return $this->response->setJSON($response);
    }
}