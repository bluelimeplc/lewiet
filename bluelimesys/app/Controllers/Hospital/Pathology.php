<?php

namespace App\Controllers\Hospital;

use BluelimeConfiguration;
use BluelimeSystem;
class Pathology extends \App\Controllers\BaseController
{
    public function index()
    {
        
        $orderMainModel = model('Hospital/OrderMainModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
       
        
        $orders = $orderMainModel->where('order_service_type','Pathology')->where('result_status',BluelimeConfiguration::LAB_RESULT_PENDING)->whereIn('order_status',[4,5])->findAll();
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
            $orders[$index]['doctor'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"Reception";
            $orders[$index]['age'] = BluelimeSystem::calculate_age($rt['date_of_birth']);
        }
        //BluelimeSystem::show_and_die($orders);
        $data['active_menu'] = 'pending_order';
        $data['orders'] = $orders;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Pathology/_left_sidebar_pathology')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Pathology/pending_orders')

        .view('Include/_footer');
    }
    public function pathologydashboard()
    {
        $orderMainModel = model('Hospital/OrderMainModel');

        $selected_date = date('Y-m-d').'%';

        $today_orders = $orderMainModel->where('created_at like',$selected_date)->where('order_service_type','Pathology')->where('order_status',BluelimeConfiguration::LAB_ORDER_PAID)->findColumn('count(id)')[0];
        $today_results = $orderMainModel->where('result_approved_date like',$selected_date)->where('order_service_type','Pathology')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->findColumn('count(id)')[0];
        $total_orders = $orderMainModel->where('order_service_type','Pathology')->where('order_status',BluelimeConfiguration::LAB_ORDER_PAID)->findColumn('count(id)')[0];
        $total_results = $orderMainModel->where('order_service_type','Pathology')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->findColumn('count(id)')[0];
        //BluelimeSystem::show_and_die($today_results);
        
        $data = [
            'active_menu' => 'labdashboard',
            'today_orders' => $today_orders,
            'today_results' => $today_results,
            'total_orders' => $total_orders,
            'total_results' => $total_results,
            'loggeduserdata' => BluelimeSystem::$Authentication::get_logged_user_data()
            ] ;
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Pathology/_left_sidebar_pathology')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Pathology/lab_dashboard')

            .view('Include/_footer');
    }
    public function approved_results()
    {
        
        $orderMainModel = model('Hospital/OrderMainModel');
        //$patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $search_key = $_POST['search_key'] ?? '';
       //BluelimeSystem::show_and_die($search_key);

        /*if(trim($search_key) != ''){
            $orderMainModel = $orderMainModel->join('hospital_patients','hospital_patients.id = patient_id')->groupStart();
            $orderMainModel = $orderMainModel->Where('first_name like',"$search_key%");
            if(intval($search_key) > 0)
                $orderMainModel = $orderMainModel->orWhere('patient_id', intval($search_key));

            $orderMainModel = $orderMainModel->groupEnd();
        }*/
        $orderMainModel = $orderMainModel->groupStart()->Where('patient_name like',"$search_key%")->orWhere('patient_id like',"$search_key%")->groupEnd();

        $orders = $orderMainModel->where('order_service_type','Pathology')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->orderBy('result_approved_date DESC')->findAll(20,0);

       
        $user_id_list = BluelimeSystem::collect_field_value_from_object($orders, 'result_approved_by');
        if(empty($user_id_list))
            $user_id_list = [-1];
        
        $user_list = $userModel->whereIn('id', $user_id_list)->findAll();

        foreach($orders as $index => $rt){
            foreach($user_list as $user){
                if($user['id'] == $rt['result_approved_by'])
                $orders[$index]['approved_by'] = $user['name'];  
            }
            //$orders[$index]['approved_by'] = $userModel->where('id',$rt['result_approved_by'])->findColumn('name')[0];
        }
        //BluelimeSystem::show_and_die($orders);
        $data['active_menu'] = 'approved_order';
        $data['orders'] = $orders;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Pathology/_left_sidebar_pathology')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Pathology/approved_orders')

        .view('Include/_footer');
    }
    
    public function order_detail()
    {
        $order_main_id = $_GET['order_main_id'];
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $templateModel = model('Hospital/TemplateModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
       
        
        $biopsy_results = $biopsyResultModel->where('order_id',$order_main_id)->findAll();
        $orders = $orderMainModel->where('id',$order_main_id)->findAll();
        $templates = $templateModel->findAll();
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
            $orders[$index]['order_request_document'] = $externalDocumentsModel->where('visit_id',$rt['visit_id'])->findAll()[0]??[];
        }
        //BluelimeSystem::show_and_die($orders);
        $services = $serviceModel->where('service_type','Pathology')->findAll();

        $order_detail = $orderDetailModel->where('order_main_id',$order_main_id)->findAll();
        foreach($order_detail as $index => $detail){
            foreach($services as $service){
                if($service['id'] == $detail['ordered_service_id']){
                    $order_detail[$index]['test_name'] = $service['service_name'];
                    break;
                }
            }       
        }
        $template=$templateModel->where('template_type','PATHOLOGY')->findAll(1)[0];
        $patient = $patientModel->find($rt['patient_id']);

        $external_biopsy_request = $externalDocumentsModel->where('visit_id',$rt['visit_id']??-1)->where('request_type','BIOPSY_REQUEST')->orderBy('id','DESC')-> findAll();
        
        
        $data['biopsy_results'] = $biopsy_results;
        $data['patient'] = $patient;
        $data['template'] = $template;
        
        $data['external_biopsy_request'] = $external_biopsy_request;
       
        $data['active_menu'] = 'pending_order';
        $data['templates'] = $templates;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['order_detail'] = $order_detail;
        $data['order_main'] = $orders;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Pathology/_left_sidebar_pathology')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Pathology/order_detail')

        .view('Include/_footer');
    }

    public function savebiopsyresult()
    {
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $notificationModel = model('Hospital/NotificationModel');
        $orderMainModel = model('Hospital/OrderMainModel');

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];


        $patient_id     = $_POST['patient_id'];
        $mid     = $_POST['mid'];
        $sex     = $_POST['sex'];
        $age     = $_POST['age'];
       // $visit_id     = $_POST['visit_id'];
        $patient_name    = ucwords($_POST['patient_name']);
        $biopsy_result      = $_POST['template_content'];
        
        $mobile = $_POST['mobile'];
       //BluelimeSystem::show_and_die($patient_id);
        $data = [
            'patient_id'    => $patient_id,
            'order_id'    => $mid,
            'patient_name'   => $patient_name,
            'sex'   => $sex,
            'age'   => $age,
            'biopsy_result'   => $biopsy_result,
            'doctor_id' => $logged_user,
            'doctor_name'           => $logged_user_name,
            'mobile'      => $mobile , 
        ];
          
           $brid = $biopsyResultModel->where('order_id',$mid)->findColumn('id');
          // BluelimeSystem::show_and_die($brid);
           if(!empty($brid)){
                $biopsyResultModel->update($brid,$data);
            }
            else{
            $id=$biopsyResultModel->insert($data,true);
            $orderMainModel->where('id',$mid)->set('order_status',5)->update();
            }
        
            return redirect()->to(site_url('/hospital/pathology/viewbiopsyresult?brid='.$id.'&order_id='.$mid));
        
    }

    public function viewbiopsyresult()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['brid']??-1)->where('notification_type','Medical Report')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $userModel = model('System/UserModel');

        //$patient = $patientModel->find(@$_GET['id']);

        $biopsy_result = $biopsyResultModel->where('id like',$_GET['brid']??'%')->where('doctor_id',$loggeduserdata['id'])->orderBy('id','DESC')-> findAll();
        //BluelimeSystem::show_and_die($biopsy_result);  
        $doc_sign = $userModel->where('id',$biopsy_result[0]['doctor_id']??0)->findColumn('digital_signature'); 
            
        $doctor = $userModel->find($biopsy_result[0]['doctor_id']??0);

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        //$data['patient'] = $patient;
        $data['doctor'] = $doctor;
        $data['biopsy_result'] = $biopsy_result;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Pathology/_left_sidebar_pathology')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Pathology/view_biopsy_result')

            .view('Include/_footer');
    }

    public function get_test_attribute()
    {
        $test_id = $_POST['test_id']??-1;
        $sex = $_POST['sex']??'';
        $order_detail_id = $_POST['order_detail_id']??-1;
       
        
        $pathologyResultAttributeModel = model('Hospital/PathologyResultAttributeModel');
        $pathologyResultsModel = model('Hospital/PathologyResultsModel');
        
        
        $attributes = $pathologyResultAttributeModel->where('test_id',$test_id)->groupStart()->where('reference_range_gender',$sex)->orWhere('reference_range_gender','')->groupEnd()->findAll();
       
        foreach($attributes as $index => $rt){
            $attributes[$index]['saved_result'] = $pathologyResultsModel->where('test_id',$rt['test_id'])->where('order_detail_id',$order_detail_id)->where('attribute_id',$rt['id'])->findColumn('result')[0]??"";
        }
        //BluelimeSystem::show_and_die($attributes);
        
        return BluelimeSystem::convert_array_to_string($attributes);
       
    }
    public function approveresult()
    {
        
        $order_main_id = $_GET['order_id'];

        $orderMainModel = model('Hospital/OrderMainModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $notificationModel = model('Hospital/NotificationModel');

        $data = [
            'order_status' => 6,
            'result_status' => 1,
            'result_approved_by' => $loggeduserdata['id'],
            'result_approved_date' => date('Y-m-d H:i:s')
        ];
        $orderMainModel->update(intval($order_main_id),$data);
        $order = $orderMainModel->where('id',$order_main_id)->find()[0];
        //BluelimeSystem::show_and_die($order['ordered_by']);       
            $notfiy_data = [
                'notification_type'=>  "Pathology",
                'description'=> $order['patient_name'],
                'link_type'=> 'order_main_id',
                'link_id'=> $order_main_id,
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
        
            return redirect()->to('/hospital/pathology');
            
       
    }
    
    public function reverseresult()
    {
        $order_main_id = $_POST['order_main_id'];

        $orderMainModel = model('Hospital/OrderMainModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        if (($loggeduserdata['role'] ?? '') !== 'SUPER_ADMIN') {
            return json_encode(['status' => 'error', 'message' => 'Permission denied. Only SUPER_ADMIN can reverse approved results.']);
        }

        $data = [
            'result_status' => BluelimeConfiguration::LAB_RESULT_PENDING,
            'order_status' => 5,
            'result_approved_by' => NULL,
            'result_approved_date' => NULL
        ];
        $orderMainModel->update($order_main_id, $data);

        return json_encode(['status' => 'success', 'message' => 'Result has been reversed to Result Saved state.']);
    }
    public function collectsample()
    {
        $att_id = $_POST['id'];
        $sample_type = $_POST['sample_type'];

        $orderDetailModel = model('Hospital/OrderDetailModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $data = [
            'sample_taken' => 1,
            'sample_type' => $sample_type,
            'sample_taken_by' => $loggeduserdata['id'],
            'sample_taken_date' => date('Y-m-d H:i:s')
        ];
        //BluelimeSystem::show_and_die($att_id);
        $orderDetailModel->update($att_id,$data);
       
    }
    public function saveresult()
    {
        $result = $_POST['result_detail'] ?? [];
        $pathologyResultsModel = model('Hospital/PathologyResultsModel');

        $result_detail_data = [];

        foreach($result as $rt_line)
        {
            $rt_line_temp = [
                'order_detail_id' => $rt_line['order_detail_id'],
                'order_id' => $rt_line['order_id'],
                'test_id' => $rt_line['test_id'],
                'attribute_id' => $rt_line['attribute_id'],
                'attribute_name' => $rt_line['attribute_name'],
                'result' => $rt_line['result'],
                'reference_range' => $rt_line['reference_range'],
                'result_flag' => $rt_line['result_flag'],
                'registered_by' => $rt_line['registered_by']
            ];
            

            array_push($result_detail_data, $rt_line_temp);
        }

        //BluelimeSystem::show_and_die($result_detail_data);
        if(!empty($result_detail_data)){

            if(!empty($this->checkresult($result_detail_data))){
                foreach($result_detail_data as $rt_detail){
                    $pathologyResultsModel->where('order_detail_id',intval($rt_detail['order_detail_id']))->where('test_id',intval($rt_detail['test_id']))->delete();
                }
                //BluelimeSystem::show_and_die($rt_detail);
                $pathologyResultsModel->insertBatch($result_detail_data);
            }
            else{
                foreach($result_detail_data as $rt_detail){
                    $pathologyResultsModel->where('order_detail_id',$rt_detail['order_detail_id'])->where('test_id',$rt_detail['test_id'])->where('deleted_at IS NULL')->findAll();
                }
                $pathologyResultsModel->insertBatch($result_detail_data);
            }
        return '{"status":"success", "value":"'.$rt_line['order_id'].'", "test":"'.$rt_detail['test_id'].'", "order":"'.$rt_detail['order_detail_id'].'"}';
        }
        
    }  
  
    private function checkresult($result)
    {
        $pathologyResultsModel = model('Hospital/PathologyResultsModel');  
        $rt = $pathologyResultsModel->where('order_detail_id',$result[0]['order_detail_id'])->findAll();
        //BluelimeSystem::show_and_die($rt);
        return $rt;

    }
    public function view_lab_printout(): string
    {
        $pathologyResultsModel = model('Hospital/PathologyResultsModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $serviceModel = model('Hospital/ServiceModel');
        $userModel = model('System/UserModel');

        
        $order_result = $pathologyResultsModel->where('order_id',@$_GET['order_main_id'])->orderby('test_id asc')->findAll();
        
        foreach($order_result as $index => $rt){
            $order_result[$index]['patient_detail'] = $orderMainModel->where('id',@$_GET['order_main_id'])->findAll()[0]??[];
            $order_result[$index]['test_name'] = $serviceModel->where('id',$rt['test_id'])->findColumn('service_name')[0];
            $order_result[$index]['approved_by'] = $userModel->where('id',$rt['registered_by'])->findColumn('name')[0];
        }
        //BluelimeSystem::show_and_die($order_result);
        $data['order_result'] = $order_result;
        
        $data['active_menu'] = 'pending_order';

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Hospital/Pathology/_left_sidebar_pathology')
                .view('Include/_left_sidebar_bottom')
                .view('Hospital/Pathology/view_lab_result')
                .view('Include/_footer');     
    }
    public function viewtemplate()
    {
        
        $templateModel = model('Hospital/TemplateModel');
        
        $template_id = $_GET['tid'];
        $template=$templateModel->find($template_id);
        
        
        $data['template'] = $template;
        $data['active_menu'] = 'pending_order';

        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Pathology/_left_sidebar_pathology')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Pathology/view_template')

            .view('Include/_footer');
    }
    public function deletebiopsyresult(){
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        //BluelimeSystem::show_and_die($_GET);
        $biopsyResultModel->where('id',$_GET['brid'])->delete();

        return redirect()->to('/hospital/pathology/order_detail?order_main_id='.$_GET['oid']);
    }
    
}