<?php

namespace App\Controllers;

use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;
use Exception;

class Home extends BaseController
{
    public function ping(){
        echo "\npong\n";
        echo "I am at: ".$_ENV['BRANCH']??"Unknown Branch";
        echo "\nMy time: ".date('Y-m-d H:i:s')."\n";
        echo "Your IP: ".$_SERVER['REMOTE_ADDR']."\n\n";
        die();
    }
    public function index()
    {
        $userModel = model('System/UserModel');
        $users = $userModel->where('active',1)->findAll();
        //BluelimeSystem::show_and_die($users);
        $data['users'] = $users;
        return 
             view('Include/_header', $data)
            .view('Include/_left_sideber')
            .view('Include/_dashboard')
            .view('Include/_footer');
    }

    public function welcome()
    {
        $today = "2023-12-30"; 
        $dob = "2024-12-31";
        echo BluelimeSystem::year_difference($today, $dob);

        // $data['loggeduserdata'] = \BluelimeSystem::$Authentication::get_logged_user_data();
        // return view('welcome_message',$data);
    }

    public function test(){

       $a = "KetoconazolShampoo100ml";
       $b = "Ketoconazol100mlShampooBp";

       $percent = 0;
       $smilarity = similar_text($a, $b,$percent);

       //$percent = round($percent);
       echo "Similarity: $smilarity<br>Percent: $percent";

        //return view('test');
    }
    public function display(){
        $appointmentModel = model('Hospital/AppointmentModel');
        $waitingListModel = model('Hospital/WaitingListModel');
        $today = date('Y-m-d').'%';

        $appointments = $appointmentModel->where('appointment_date like ',$today)->groupStart()->where('appointment_status',BluelimeConfiguration::APPOINTMENT_ACTIVE)->orWhere('appointment_status',BluelimeConfiguration::APPOINTMENT_MISSED)->groupEnd()->findAll();
        $waiting_list = $waitingListModel->where('created_at like',$today)->where('status',BluelimeConfiguration::APPOINTMENT_PENDING)->findAll();
        $no_of_appointments = count($appointments);
        $no_of_waiting = count($waiting_list);
        $data['appointments'] = $appointments;
        $data['waiting_list'] = $waiting_list;
        $data['no_of_appointments'] = $no_of_appointments;
        $data['no_of_waiting'] = $no_of_waiting;

        return view('display',$data);
    }
    public function notification(){
        $notificationModel = model('App\Models\NotificationModel');

        $notification = $notificationModel->where('notification_status',\BluelimeConfiguration::NOTIFICATION_NEW)->findAll();
        $data['notification'] = $notification;

    }
    public function addnotification(){
        $notificationModel = model('App\Models\NotificationModel');
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user_name = $loggeduserdata['name'];

        
        $notification_type    = $_POST['notification_type'];
        $status    = \BluelimeConfiguration::NOTIFICATION_NEW;
        $description    = $_POST['description'].' '.  $logged_user_name;
        $link_id    = $_POST['link_id'];

        $data = [
            'notification_type'        => $notification_type,
            'status'           => $status,
            'description'           => $description,
            'link_id'           => $link_id,
        ];
        $notificationModel->insert($data,true);
    }
    public function updatenotificationstatus(){
        $notificationModel = model('App\Models\NotificationModel');
        
        $status    = \BluelimeConfiguration::NOTIFICATION_SEEN;

        $data = [
            'status'           => $status
        ];
        $id = $_GET['nid'];
        $notificationModel->update($id,$data);
    }

    public function store_communication(){
        //header("Access-Control-Allow-Origin: *");
    
        $mobile = $_POST['mobile'] ?? '';
        $message = $_POST['message'] ?? '';
        
        $mobile = str_ireplace(' ', '', $mobile);
        if(substr($mobile,0,1) == '0')
            $mobile = "251".substr($mobile,1);
        elseif(strlen($mobile) == 9 )
            $mobile = "251".$mobile;

        $communicationModel = model('App\Models\CommunicationModel');

        $data = [
            'type'=>'Telegram',
            'direction' => 'OUT',
            'address'=>$mobile,
            'message'=>$message,
            'extra'=>''
        ];
        $id = $communicationModel->insert($data, true);
        
        $result = '{"status":"Error","message":"The message is not stored."}';
        if($id > 0)
            $result =  '{"status":"success"}';

        return $result;
    }

    public function read_pos_response(){
        //\BluelimeSystem::write_log('abiy_test','cron',time());
        $acknowladge_list =  BluelimePOS::read_response();
        BluelimeSystem::print_associated_array($acknowladge_list,false);
        echo 'v 2. Done';
        die();
    }

    public function pedsconfirmpayment(){
        echo  BluelimePOS::read_response_peds();
        die();
    }

    public function pedsconfirmpayment_show(){

        $serverName = '192.168.1.10\SECOND';
        $db_name = 'PEDSPOSLITE';
        $username = 'sa';
        $password = 'pass';
        $port = '1433';
        $reference_no = 'RANK-123';
        
        $maraki_db_conf = [
            'DSN'      => '',
            'hostname' => $serverName,
            'username' => $username,
            'password' => $password,
            'database' => $db_name,
            'DBDriver' => 'SQLSRV',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 1433,
        ];


        $peds_db = \Config\Database::connect($maraki_db_conf);

        $sql= 'SELECT * FROM [HoldSales]';

        var_dump($peds_db->query( $sql));

        die();

        $loggeduserdata = ['role' => 'a']; 
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = '';
        return view('peds/show', $data);
    }

    public function run_shell(){
        var_dump(set_time_limit(5));
        $command = $_GET['command'] ?? '';

        $output = null;
        $result_code = null;

        if($command != ''){
            var_dump (exec($command, $output, result_code: $result_code));
            BluelimeSystem::print_associated_array($output,false);
            BluelimeSystem::show_and_die( $result_code,false);

        }
        $output = null;
        $result_code = null;

        var_dump (exec('sh /sbin/bluelime_pos_dir', $output, result_code: $result_code));
        //var_dump (exec('', $output, result_code: $result_code));
        echo '<hr>';
         
        BluelimeSystem::print_associated_array($output,false);
        BluelimeSystem::show_and_die( $result_code,false);
        echo '--Done--';
    }

    public function reboot(){
        echo is_dir('c:\\');
        die();
    }

    public function newappointment()
    {
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $visitModel = model('Hospital/VisitModel');

        $patient_id = $_GET['id'] ?? -1;

        $patient_appointment=$appointmentModel->where('patient_id',@$_GET['id'])->findAll();
        $appointment=$appointmentModel->where('id',@$_GET['app_id'])->findAll();
        $data['patient_appointment'] = $patient_appointment;
       
        $data['appointment'] = $appointment;
        $data['active_menu'] = 'appointment';
      
       $patient = $patientModel->find(@$_GET['id']??-1);
        //BluelimeSystem::show_and_die($appointment);
       $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll();

       $visits = $visitModel->where('patient_id', $patient_id)->orderBy('id', 'DESC')->findAll();

       $last_visit = $visits[0] ?? [];

       $next_appointment_reason = $last_visit['extra']['next_appointment_reason'] ?? '';
        
        //$data['patient'] = $patients;
        $data['doctors'] = $doctors;
        $data['patient'] = $patient;
        $data['next_appointment_reason'] = $next_appointment_reason;

        /*
        $aa= $appointmentModel->where('appointment_date >= CURRENT_DATE()')->where('appointment_date <' ,'2023-09-17%')->where('doctor_id',31)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        //$aa= $appointmentModel->where('appointment_date >= ','2023-09-17%')->where('doctor_id',31)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        
        foreach($aa as $y){
            \BluelimeSystem::create_google_calendar_events(date('Y-m-d', strtotime($y['appointment_date'])),$y['time'],$y['appointment_reason'],$y['full_name'],30);

        }*/

        return 
        view('new_appointment', $data);


    }
    
    public function savedapp()
    {
        return 
        view('saved_appointment');


    }
    public function addappointment()
    {
        
        $appointmentModel = model('Hospital/AppointmentModel');
        $scheduleModel = model('Hospital/ScheduleModel');
        $userModel = model('System/UserModel');
        
        $patient_id     = $_POST['patient_id'];
        $doctor_id    = $_POST['doctor_id'];
        $appointment_date      = $_POST['date'];
        $appointment_reason  = 'Dental Consultation';
        $appointment_status            = \BluelimeConfiguration::APPOINTMENT_ACTIVE;
        $full_name        = ucwords($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']);
        $registered_by         = $_POST['registered_by'];
        $registered_by_name       = $_POST['registered_by_name'];
        $doctor = $userModel->where('id', $doctor_id)->findColumn('name');
        $doctor_name         = $doctor[0];
        $visit_id = $_POST['visit_id'];
        $reminder_sent     = $_POST['reminder_sent'];
        $schedule_id     = $_POST['schedule_id'];
        $paid_appointment     = 1;
        $mobile     = $_POST['mobile'];
       // \BluelimeSystem::show_and_die($mobile);
        $time     = $_POST['sequence'];
        $app_id = $_POST['app_id'];
        $data = [
            'patient_id'    => $patient_id,
            'doctor_id'   => $doctor_id,
            'appointment_date'     => $appointment_date,
            'appointment_reason' => $appointment_reason,
            'appointment_status'           => $appointment_status,
            'full_name'       => $full_name,
            'registered_by'        => $registered_by,
            'registered_by_name'      => $registered_by_name,
            'doctor_name'        => $doctor_name,
            'visit_id' => $visit_id,
            'reminder_sent'    => $reminder_sent,
            'schedule_id'    => $schedule_id,
            'mobile'    => $mobile,
            'time'    => $time,
            'paid_appointment'    => $paid_appointment
        ];
        
        $schedule_min = $scheduleModel->where('id',$schedule_id)->findColumn('per_patient_time');
        //\BluelimeSystem::show_and_die(intval($schedule_min[0]));
        $patient_appointment_exist = $appointmentModel->patientappointmentexist($patient_id);
        $appointment_time_exist = $appointmentModel->appointmentchecker($doctor_id,$appointment_date,$time);
       // \BluelimeSystem::show_and_die($patient_appointment_exist);
       
        if($appointment_time_exist){  
            return redirect()->to(site_url('/newappointment?id='.$patient_id.'&rt='.'1'));
        }
        $id = $_POST['app_id']?? 0;
        
        if (intval($app_id) != 0)
            $appointmentModel->update($id,$data);
        else
            $id = $appointmentModel->insert($data,true);
            $appointment_message = "Appointment date: $appointment_date\nTime: $time\nDoctor name: $doctor_name\n\nRank Dermatology Clinic ";
            // $telegram_bot = "\nJoin Telegram: https://t.me/RankDerma_bot";
            // $website = "\nBook Appointment @ www.rankderma.com";
            $branch = $_ENV['BRANCH']??"";
            if(BluelimeSystem::check_sms_service()){
                BluelimeSystem::send_sms_message($mobile, $appointment_message.$branch);
            }
                BluelimeSystem::send_telegram_message($mobile, $appointment_message.$branch);
                return redirect()->to(site_url('/savedapp'));
        }

    public function getschedule(){
        
        $appointmentModel = model('Hospital/AppointmentModel');
        $scheduleModel = model('Hospital/ScheduleModel');

        $doctor_id = $_POST['doctor_id'] ?? '-1';
        $date = $_POST['date'] ?? '-1';
        
        //$re = $scheduleModel->checkdoctorscheduledate($doctor_id,$date); 

        $skipDateModel = model('Hospital/SkipDateModel');
        $doc_skip_dates = $skipDateModel->where('skip_date',$date)->where('doctor_id',$doctor_id)->findColumn('id');
        $is_skip_date = false;
        if(!empty($doc_skip_dates))
            $is_skip_date = true;


        if(!$is_skip_date){
    
            $result1 = $scheduleModel->checkscheduledate($doctor_id,$date);
            
            if(!empty($result1)){

                $doctors_active_appointment_times = $appointmentModel->where('doctor_id',$doctor_id)->where('appointment_date', $date)->where('appointment_status',BluelimeConfiguration::APPOINTMENT_ACTIVE)->findColumn('time')??[];
               
                $start_time = strtotime($result1['start_time']);
                $end_time = strtotime($result1['end_time']);
                $total_m =  round(abs($end_time- $start_time) / 60,2);
    
                $per_patient_time = $total_m / $result1['per_patient_time'];
    
                echo '<div id="msg_c"> '.lang('Select Time').' </div>'; 
                        
                        for ($i = 1; $i <= $per_patient_time; $i++) {
    
                            $m_time = $i-1;
                            $time = ($m_time * $result1['per_patient_time']);
                                                
                            $patient_time =date('h:i A', strtotime($result1['start_time'])+$time*60);
                            
                            //$reason = $appointmentModel->appointmentreason($doctor_id,$date,$patient_time);
                            //\BluelimeSystem::show_and_die($reason);
                            //$button_color = $appointmentModel->appointmentchecker($doctor_id,$date,$patient_time);
                            
                            $button_color = false;
                            foreach($doctors_active_appointment_times as $active_appointment_time){
                                if($patient_time == $active_appointment_time){
                                    $button_color = true;
                                    break;
                                }
                            }
                            //if((date('Y-m-d',strtotime($date)) == date('Y-m-d')) && strtotime(date("G:i", strtotime($patient_time))) <= strtotime((date("G:i", strtotime(date("G:i", strtotime(date("h:i A")))))))){
                                if ($button_color || ((date('Y-m-d',strtotime($date)) == date('Y-m-d')) && strtotime(date("G:i", strtotime($patient_time))) <= strtotime((date("G:i", strtotime(date("G:i", strtotime(date("h:i A"))))))))) {
                                    echo '<button type="button" disable class="btn btn-danger" style="font-size:12px;height:25px;width: 60px;padding: 1px 0px;margin: 1px 1px;text-align: center;"><p ">'.$patient_time.'</p></button>';
                                    } 
                                else {
                                    echo '<button style="font-size:12px;height:25px;width: 60px;padding: 1px 0px;margin: 1px 1px;text-align: center;" id="t_'.$i.'" type="button" class="btn btn-primary" onclick="myBooking('.$i.')">'.$patient_time.'</button>';
                                    }
                           // }

                                
                            
                        }
    
                echo '<input type="hidden" name="sequence" id="serial_no" value="">';
                echo '<input type="hidden" name="schedule_id" value="' . $result1['id'] . '">';
            }
            else{
                echo'
                <div class="col-md-12">
                    <div class="alert alert-danger">'.'No Schedule for this Doctor on the selected Date'.'</div>
                </div>';
            }               
    
        }
        else{
            echo'<div class="col-md-12">
            <div class="alert alert-danger">
                '.lang('No Schedule for this Doctor on the selected Date').'
            </div>
        </div>';
        }
    }
    public function import_online_prescription(){
       BluelimeSystem::import_online_prescription();
    }
    public function getpatientbymobileforapp()
    {
        $mobile = $_POST['mobile'];
        $patientModel = model('Hospital/PatientModel');

        //$mobile = $patientModel->db->escapeString($mobile);

        $result = $patientModel->where('mobile', $mobile)->findAll();
        
        if(!empty($result)){
            echo "<label style='color:#785342; padding-left:35px'>Click on your name if it is displayed in the list.</label>";
            echo "<table class='table table-hover table-condensed' style='border-radius: 50px;'>";
            foreach($result as $rt){
            echo '<tr style="border-radius: 50px;border-color:#ffffff"><td style="padding-left: 40px;border-color:#ffffff"><a href="newappointment?id='.$rt['id'].'"  style="text-decoration:none;color: #785342;"><i class="fa fa-solid fa-user"></i>&nbsp;'.$rt['first_name'] . ' ' . $rt['middle_name'] . ' ' . $rt['last_name'].'</a></td></tr>';
            }
            echo "</table>";
        }
    }


}
