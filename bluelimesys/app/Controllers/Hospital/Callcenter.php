<?php

namespace App\Controllers\Hospital;

use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;
class Callcenter extends \App\Controllers\BaseController
{
    
    public function callcenterdashboard()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $skipDateModel = model('Hospital/SkipDateModel');
        $billModel = model('Hospital/BillModel'); 
        $billPayementModel = model('Hospital/BillPaymentModel'); 
        $userModel = model('System/UserModel'); 
        $visitModel = model('Hospital/VisitModel'); 
        $appointmentModel = model('Hospital/AppointmentModel');

        $selected_date = date('Y-m-d').'%';

        $skips=$skipDateModel->where('skip_date >=',$selected_date)->findAll();
        foreach($skips as $index => $doctor){
            $skips[$index]['doctor_name'] = $userModel->where('id',$doctor['doctor_id'])->findColumn('name')[0];
        }

        $doctors=$scheduleModel->distinct()->findColumn('doctor_name');
        
        $schedules=$scheduleModel->findAll();
        $billPayements = $billPayementModel->where('payed_date LIKE', $selected_date)->orderBy('patient_name','ASC')->orderBy('id','ASC')->findAll();
        $total_payment = $billPayementModel->where('payed_date LIKE', $selected_date)->findColumn('sum(payed_amount)');
        foreach($billPayements as $index => $billPayement){
            $visit_id = $billModel->where('id',$billPayement['bill_id'])->findColumn('visit_id');
            $billPayements[$index]['doctor_name'] = $visitModel->where('id',$visit_id)->findColumn('doctor_name');
        }
        $cancelled_appointments = $appointmentModel->where('appointment_date >= ',$selected_date)->where('appointment_status',-1)->where('Cancelled_by like', 'Dr.%')->orderBy('appointment_date DESC')->findAll();

        $data['cancelled_appointments'] = $cancelled_appointments;
        $data['billPayements'] = $billPayements;
        $data['total_payment'] = $total_payment[0];
        $data['schedules'] = $schedules;
        $data['doctors'] = $doctors??[];
        $data['skips'] = $skips;
        $data['active_menu'] = 'callcenterdashboard';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Callcenter/_left_sidebar_callcenter')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Callcenter/callcenter_dashboard')

            .view('Include/_footer');
    }
    public function newappointment()
    {
        $searchkey= $_POST['searchkey'] ?? "";
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $visitModel = model('Hospital/VisitModel');

        $patient_id = $_GET['id'] ?? -1;
        $patients = $patientModel->search_patient($searchkey);
        $nop = $patientModel->findColumn('count(id)');

        $patient_appointment=$appointmentModel->where('patient_id',@$_GET['id'])->findAll();
        $appointment=$appointmentModel->where('id',@$_GET['app_id'])->findAll();
        $data['patient_appointment'] = $patient_appointment;
        //BluelimeSystem::show_and_die($appointment);
        $data['appointment'] = $appointment;
        $data['active_menu'] = 'appointment';
      
       $patient = $patientModel->find(@$_GET['id']);
       
       $role = array('DOCTOR');
       $doctors = $userModel->whereIn('role',$role)->where('active', 1)->findAll();

       $visits = $visitModel->where('patient_id', $patient_id)->orderBy('id', 'DESC')->findAll();

       $last_visit = $visits[0] ?? [];

       $next_appointment_reason = $last_visit['extra']['next_appointment_reason'] ?? '';
        
        //$data['patient'] = $patients;
        $data['doctors'] = $doctors;
        $data['patient'] = $patient;
        $data['searchkey'] = $searchkey;
        $data['patients'] = $patients;
        $data['nop'] = $nop[0];
        $data['next_appointment_reason'] = $next_appointment_reason;

        /*
        $aa= $appointmentModel->where('appointment_date >= CURRENT_DATE()')->where('appointment_date <' ,'2023-09-17%')->where('doctor_id',31)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        //$aa= $appointmentModel->where('appointment_date >= ','2023-09-17%')->where('doctor_id',31)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        
        foreach($aa as $y){
            BluelimeSystem::create_google_calendar_events(date('Y-m-d', strtotime($y['appointment_date'])),$y['time'],$y['appointment_reason'],$y['full_name'],30);

        }*/

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Callcenter/_left_sidebar_callcenter')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Callcenter/new_appointment')

            .view('Include/_footer');
    } 
    public function addappointment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $scheduleModel = model('Hospital/ScheduleModel');
        $userModel = model('System/UserModel');
        
        $patient_id     = $_POST['patient_id'];
        $doctor_id    = $_POST['doctor_id'];
        $appointment_date      = $_POST['date'];
        $appointment_reason  = $_POST['appointment_reason'];
        $appointment_status            = \BluelimeConfiguration::APPOINTMENT_ACTIVE;
        $full_name        = ucwords(strtolower($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']));
        $registered_by         = $_POST['registered_by'];
        $registered_by_name       = $_POST['registered_by_name'];
        $doctor = $userModel->where('id', $doctor_id)->findColumn('name');
        $doctor_name         = $doctor[0];
        $paid_appointment = $_POST['paid_appointment'];
        $visit_id = $_POST['visit_id'];
        $reminder_sent     = $_POST['reminder_sent'];
        $schedule_id     = $_POST['schedule_id'];
        
        //$mobile     = (intval($_POST['additional']==1))?'':$_POST['mobile'];
        $mobile     = $_POST['mobile'];
       // BluelimeSystem::show_and_die($mobile);
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
            'time'    => $time
        ];
        
        $schedule_min = $scheduleModel->where('id',$schedule_id)->findColumn('per_patient_time');
        //BluelimeSystem::show_and_die(intval($schedule_min[0]));
        $patient_appointment_exist = $appointmentModel->patientappointmentexist($patient_id);
        $appointment_time_exist = $appointmentModel->appointmentchecker($doctor_id,$appointment_date,$time);
       // BluelimeSystem::show_and_die($patient_appointment_exist);
       
        if($appointment_time_exist){  
            return redirect()->to(site_url('/hospital/callcenter/newappointment?id='.$patient_id.'&rt='.'1'));
        }
        $id = $_POST['app_id']?? 0;
        
        if (intval($app_id) != 0)
            $appointmentModel->update($id,$data);
        else
            $id = $appointmentModel->insert($data,true);

        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        
        $time_from = BluelimeSystem::roundToNearestThirty(($time));
        
        $time_range = BluelimeSystem::formatThirtyMinuteRange($time_from);
        //BluelimeSystem::show_and_die($time_range);
        $appointment_date_w = date('Y-m-d (l)', strtotime($appointment_date));
        $appointment_message = "Appointment date: $appointment_date_w \nTime: $time_range\nDoctor name: $doctor_name\n\nRANK Specialized Dermatology Clinic\n";
        //$telegram_bot = "Join Telegram: https://t.me/RankDerma_bot";
       
        if(BluelimeSystem::check_sms_service()){
            BluelimeSystem::send_sms_message($mobile, $appointment_message.$branch);
        }
        BluelimeSystem::send_telegram_message($mobile, $appointment_message.$branch);


            /*if($doctor_id==31){
            BluelimeSystem::create_google_calendar_events($appointment_date,$time,$appointment_reason,$full_name,intval($schedule_min[0]));
            }
            */
        //BluelimeSystem::export_prescription($mobile, 'Test Prescription');
            
        return redirect()->to(site_url('/hospital/callcenter/newappointment?additional=1&app_id=&id='.$patient_id));
    }
    public function index()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $appointmentModel->updatemissed();
        //\BluelimeSystem::import_appointment_new();
        
        $selected_date = isset($_POST['appointment_date'])?($_POST['appointment_date'].'%'):date('Y-m-d').'%';
        $searchkey= $_POST['search_appointment'] ?? "";
        //\BluelimeSystem::show_and_die($_POST);
        if(!empty($_POST['search_appointment'])){
            $appointments = $appointmentModel->search_by_patient($searchkey);
        }
        else
        
            if(!empty($_POST['app_doctor'])){
                $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_name',$_POST['app_doctor'])->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findAll();
                $doctors = $appointmentModel->distinct()->where('appointment_date like ',$selected_date)->findColumn('doctor_name');
                //\BluelimeSystem::show_and_die($doctors);
                $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_name',$_POST['app_doctor'])->findColumn('count(id)')[0]; 
            }
            else{
                $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findAll();
                $doctors = $appointmentModel->distinct()->where('appointment_date like ',$selected_date)->findColumn('doctor_name');
                //\BluelimeSystem::show_and_die($doctors);
                $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->findColumn('count(id)')[0];
            }
            if(@$_GET['online']==1){
                $appointments = $appointmentModel->where('appointment_date >= ',$selected_date)->where('paid_appointment >',0)->orderBy('appointment_date','desc')->findAll();
            
            }
        $data['active_menu'] = 'appointment';
        $data['selected_date'] = $selected_date;
        $data['appointments'] = $appointments;
        $data['no_of_appointment'] = @$no_of_appointment;
        $data['doctors'] = @$doctors;
        $data['searchkey'] = $searchkey;
        
                            

        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Callcenter/_left_sidebar_callcenter')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Callcenter/appointment_list')

            .view('Include/_footer');
    }
    public function doctorschedule()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $scheduleModel = model('Hospital/ScheduleModel');
        $skipDateModel = model('Hospital/SkipDateModel');
        $userModel = model('System/UserModel');
        
        $selected_doctor = $_REQUEST['did'] ?? 0;
        $skips=$skipDateModel->where('doctor_id',$selected_doctor)->findAll();
        $doctors=$userModel->where('role','DOCTOR')->where('active',1)->findAll();

        $data['skips'] = $skips;

        $schedules=$scheduleModel->where('doctor_id',$selected_doctor)->findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['schedules'] = $schedules;
        $data['doctors'] = $doctors;
        $data['active_menu'] = 'patient_waiting';

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Callcenter/_left_sidebar_callcenter')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Callcenter/doctor_schedule')
        .view('Include/_footer');
    }

    public function addschedule()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $userModel = model('System/UserModel');

        $schedule_id=$scheduleModel->where('doctor_id',$_POST['doctor_id'])->findColumn('id');
        //if(empty($schedule_id))
           // $schedule_id=-1;
        //$scheduleModel->delete($schedule_id);
       //BluelimeSystem::show_and_die($_POST['doctor_id']);
        for($i=0; $i<count( $_POST['day']); $i++) {
            $doctor_name = $userModel->where('id', $_POST['doctor_id'])->findColumn('name')[0];
            $data = array(
             'doctor_id' => $_POST['doctor_id'] , 
             'doctor_name' => $doctor_name , 
             'start_time' => $_POST['s_time'] , 
             'end_time' => $_POST['e_time'] , 
             'day' => $_POST['day'] [$i] , 
             'per_patient_time' => $_POST['p_time'] , 
             'schedule_start_date' => $_POST['start_date']  
             );
        $scheduleModel->insert($data,true);
        //BluelimeSystem::export_schedule($data);
        }   
        return redirect()->to(site_url('/hospital/callcenter/doctorschedule?did='.$_POST['doctor_id']));
        
    }
    public function removescheduleday()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $today = date('Y-m-d').'%';
        $active_appointments = $appointmentModel->where('appointment_date >= ',$today)->where('schedule_id',$_POST['schedule_id'])->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findColumn('count(id)')[0];
        
        $appointmentModel->where('schedule_id',$_POST['schedule_id'])->set('cancelled_by' , $loggeduserdata['name'])->set('cancelation_reason' , "Schedule Removed")->set('appointment_status',\BluelimeConfiguration::APPOINTMENT_CANCELED)->update();
        //BluelimeSystem::show_and_die($active_appointments);
        $scheduleModel->delete($_POST['schedule_id']);
        
        echo "Schedule Day Deleted. All Appointments on the date are Cancelled";
        if($active_appointments >= 1){
        $notfiy_data = [
            'notification_type'=>  "Cancelled Appointments",
            'description'=> "Schedule Day Deleted.",
            'link_type'=> 'schedule_id',
            'link_id'=> $_POST['schedule_id'],
         ];
        $notificationModel->insert($notfiy_data,true);
        
        
        }
        BluelimeSystem::delete_schedule($_POST['schedule_id']);
        
    }
    public function cleardoctorsschedule()
    {
        $scheduleModel = model('Hospital/ScheduleModel');

        $scheduleModel->delete($_GET['sid']);
       
        return redirect()->to(site_url('/hospital/callcenter/doctorschedule'));
        
    }

    public function addskipdate()
    {
        $skipDateModel = model('Hospital/SkipDateModel');
        $notificationModel = model('Hospital/NotificationModel');
        //$loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $selected_doctor = $_REQUEST['did']??0;
        //BluelimeSystem::show_and_die($selected_doctor);
        $skip_date = $_POST['skip_date'];
        $data = [
            'skip_date'    => $skip_date,
            'doctor_id'   => $selected_doctor
        ];
        if(!empty($selected_doctor)){
        $id = $skipDateModel->insert($data,true);
        $notfiy_data = [
            'notification_type'=>  "Skip Date",
            'description'=> $skip_date,
            'link_type'=> 'skip_id',
            'user_id'=> $selected_doctor,
            'link_id'=> $id,
         ];
        $notificationModel->insert($notfiy_data,true);
        }
        return redirect()->to(site_url('/hospital/callcenter/doctorschedule?did='.$selected_doctor));
        
    }
    public function deleteskipdate()
    {
        $skipDateModel = model('Hospital/SkipDateModel');
        
        $skipDateModel->where('id',$_GET['id'])->delete($_GET['id']);
          
        return redirect()->to(site_url('/hospital/callcenter/doctorschedule?did='.$_GET['did']));
        
    }
    public function save_comment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $cancel_reason = $_POST['cancel_reason'] ?? '';
        $custom_reason = $_POST['custom_reason'] ?? '';
        $app_id = $_POST['app_id'] ?? '';

        $appointmentModel->update($app_id, ['cancelled_by' => $loggeduserdata['name'], 'cancelation_reason' => $cancel_reason, 'custom_note' => $custom_reason]);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }
    public function cancelappointment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $cancel_reason = $_POST['cancel_reason'] ?? '';
        $custom_reason = $_POST['custom_reason'] ?? '';

        $app_id = $_POST['app_id'] ?? '';
        $data = [
                    'appointment_status'=> \BluelimeConfiguration::APPOINTMENT_CANCELED,
                    'cancelled_by'=> $loggeduserdata['name'],
                    'cancelation_reason'=> $cancel_reason,
                    'custom_note'=> $custom_reason,
                ];
        $app = $appointmentModel->where('id',$app_id)->findAll()[0];
        $appointmentModel->update($app_id,$data);

        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        $appointment_message = "Your Appointment on ".date('l d-m-Y', strtotime($app['appointment_date']))." " .$app['time']." is Canceled\n\nRANK Specialized Dermatology Clinic";
        if(BluelimeSystem::check_sms_service()){
            BluelimeSystem::send_sms_message($app['mobile'], $appointment_message.$branch);
        }
            BluelimeSystem::send_telegram_message($app['mobile'], $appointment_message.$branch);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }


}