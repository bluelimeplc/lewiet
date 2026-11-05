<?php

namespace App\Controllers\Hospital;

use BluelimeConfiguration;
use BluelimeSystem;
class Laboratory extends \App\Controllers\BaseController
{
    public function index()
    {
        print_r($_POST);
        $search_key = trim($_POST['search_key'] ?? '');
        $orderMainModel = model('Hospital/OrderMainModel');

        if($search_key != ""){
            $search_key = explode(" ",$search_key);
            foreach($search_key as $key){
                $orderMainModel = $orderMainModel->groupStart()->where('patient_name LIKE', "%$key%")->orWhere('mobile LIKE', "%$key%")->groupEnd();
            }
        }
        else
            $orderMainModel = $orderMainModel->where('hide',0);

        $orderMainModel = $orderMainModel->where('order_service_type','Laboratory')->where('result_status',BluelimeConfiguration::LAB_RESULT_PENDING)->where('order_status',BluelimeConfiguration::LAB_ORDER_PAID);
        $orders = $orderMainModel->findAll();

        $patients = BluelimeSystem::gather_objects_by_reference($orders,'patient_id','Hospital/PatientModel','id',true);
        $doctors = BluelimeSystem::gather_objects_by_reference($orders,'ordered_by','System/UserModel','id',true);
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patients['id_'.$rt['patient_id']]??[];
            $orders[$index]['doctor'] = $doctors['id_'.$rt['ordered_by']]['name'] ?? "Reception";
        }
        //BluelimeSystem::show_and_die($orders);
        $data['active_menu'] = 'pending_order';
        $data['orders'] = $orders;
        $data['search_key'] = trim($_POST['search_key'] ?? '');
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Laboratory/_left_sidebar_laboratory')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Laboratory/pending_orders')

        .view('Include/_footer');
    }

    public function hide_order(){
        $order_id = $_GET['order_id'] ?? -1;
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderMainModel->update($order_id,['hide'=>1]);
        return redirect()->to('/hospital/laboratory');
    }
    public function labdashboard()
    {
        $orderMainModel = model('Hospital/OrderMainModel');

        $serviceModel = model('Hospital/ServiceModel');
        $userModel = model('System/UserModel');
        $selected_date = ($_POST['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $end_date = ($_POST['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');
        $order_result = $laboratoryResultsModel->where('created_at >= ',$selected_date)->where('created_at <= ',$end_date)->findAll();
       
        foreach($order_result as $index => $rt){
            $order_result[$index]['patient_detail'] = $orderMainModel->where('id',@$rt['order_id'])->findAll()[0]??[];
            $order_result[$index]['test_name'] = $serviceModel->where('id',$rt['test_id'])->findColumn('service_name')[0];
            $order_result[$index]['approved_by'] = $userModel->where('id',$rt['registered_by'])->findColumn('name')[0];

            // $row = [
            //     'attribute_name' => $rt['attribute_name'],
            //     'result' => $rt['result']
            // ];

            // array_push($printout_lists, $row);
        }
        //$order_result['testt'] = $printout_lists;
        //BluelimeSystem::show_and_die($order_result);
        

        $today_orders = $orderMainModel->where('created_at like',$selected_date)->where('order_service_type','Laboratory')->where('order_status',BluelimeConfiguration::LAB_ORDER_PAID)->findColumn('count(id)')[0];
        $today_results = $orderMainModel->where('result_approved_date like',$selected_date)->where('order_service_type','Laboratory')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->findColumn('count(id)')[0];
        $total_orders = $orderMainModel->where('order_service_type','Laboratory')->where('order_status',BluelimeConfiguration::LAB_ORDER_PAID)->findColumn('count(id)')[0];
        $total_results = $orderMainModel->where('order_service_type','Laboratory')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->findColumn('count(id)')[0];
        //BluelimeSystem::show_and_die($today_results);
        
        $data = [
            'active_menu' => 'labdashboard',
            'today_orders' => $today_orders,
            'today_results' => $today_results,
            'total_orders' => $total_orders,
            'total_results' => $total_results,
            'order_result' => $order_result,
            'selected_date' => $selected_date,
            'end_date' => $end_date,
            'loggeduserdata' => BluelimeSystem::$Authentication::get_logged_user_data()
            ] ;
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Laboratory/_left_sidebar_laboratory')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Laboratory/lab_dashboard')

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

        $orders = $orderMainModel->where('order_service_type','Laboratory')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->orderBy('result_approved_date DESC')->findAll(20,0);

       
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

        .view('Hospital/Laboratory/_left_sidebar_laboratory')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Laboratory/approved_orders')

        .view('Include/_footer');
    }
    
    public function order_detail()
    {
        $order_main_id = $_GET['order_main_id'];

        $orderDetailModel = model('Hospital/OrderDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
       
        $order_result = $laboratoryResultsModel->where('order_id',$order_main_id)->orderby('test_id asc')->findAll();
        
        $nor = $laboratoryResultsModel->select('count(DISTINCT (test_id)) as nor')->where('order_id',$order_main_id)->orderby('test_id asc')->findColumn('count(DISTINCT (test_id))')[0];
        
        $orders = $orderMainModel->where('id',$order_main_id)->findAll();
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
        }
        
        $services = $serviceModel->where('service_type','Laboratory')->findAll();

        $order_detail = $orderDetailModel->where('order_main_id',$order_main_id)->where('payment_id IS NOT NULL')->findAll();
        $order_detail_lab_only = [];
        foreach($order_detail as $index => $detail){
            foreach($services as $service){
                if($service['id'] == $detail['ordered_service_id']){
                    $order_detail[$index]['test_name'] = $service['service_name'];
                    $order_detail_lab_only[$index] = $order_detail[$index];
                    break;
                }
            }       
        }
        //BluelimeSystem::show_and_die($orders[0]['patient_detail']['sex']);
        $data['active_menu'] = 'pending_order';
        $data['loggeduserdata'] = $loggeduserdata;
        $data['nor'] = $nor;
        $data['order_detail'] = $order_detail_lab_only;
        $data['order_result'] = $order_result;
        $data['order_main'] = $orders;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Laboratory/_left_sidebar_laboratory')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Laboratory/order_detail')

        .view('Include/_footer');
    }

    public function get_test_attribute()
    {
        $test_id = $_POST['test_id']??-1;
        $sex = $_POST['sex']??'';
        $order_detail_id = $_POST['order_detail_id']??-1;
        
        
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');

        
        $attributes = $laboratoryResultAttributeModel->where('test_id',$test_id)->groupStart()->where('reference_range_gender',$sex)->orWhere('reference_range_gender','')->groupEnd()->findAll();
       
        foreach($attributes as $index => $rt){
            $attributes[$index]['saved_result'] = $laboratoryResultsModel->where('test_id',$rt['test_id'])->where('order_detail_id',$order_detail_id)->where('attribute_id',$rt['id'])->findColumn('result')[0]??"";
        }
        //BluelimeSystem::show_and_die($attributes);
        
        return BluelimeSystem::convert_array_to_string($attributes);
       
    }
    public function approveresult()
    {
        $order_main_id = $_POST['order_main_id'];
        $result_remark = $_POST['result_remark'];

        $orderMainModel = model('Hospital/OrderMainModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $notificationModel = model('Hospital/NotificationModel');

        $data = [
            'result_status' => BluelimeConfiguration::LAB_RESULT_APPROVED,
            'result_approved_by' => $loggeduserdata['id'],
            'result_approved_date' => date('Y-m-d H:i:s'),
            'result_remark' => $result_remark
        ];
        $orderMainModel->update(intval($order_main_id),$data);
        $order = $orderMainModel->where('id',$order_main_id)->find()[0];
        //BluelimeSystem::show_and_die($order['ordered_by']);       
        $notfiy_data = [
            'notification_type'=>  "Laboratory",
            'description'=> $order['patient_name'],
            'link_type'=> 'order_main_id',
            'link_id'=> $order_main_id,
            'user_id'=> $order['ordered_by'],
            ];
        $notificationModel->insert($notfiy_data,true);
    }
    
    public function reverseresult()
    {
        $order_main_id = $_POST['order_main_id'];

        $orderMainModel = model('Hospital/OrderMainModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $data = [
            'result_status' => BluelimeConfiguration::LAB_RESULT_PENDING,
            'result_approved_by' => NULL,
            'result_approved_date' => NULL
        ];
        //BluelimeSystem::show_and_die($att_id);
        $orderMainModel->update($order_main_id,$data);
       
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
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');

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
                    $laboratoryResultsModel->where('order_detail_id',intval($rt_detail['order_detail_id']))->where('test_id',intval($rt_detail['test_id']))->delete();
                }
                //BluelimeSystem::show_and_die($rt_detail);
                $laboratoryResultsModel->insertBatch($result_detail_data);
            }
            else{
                foreach($result_detail_data as $rt_detail){
                    $laboratoryResultsModel->where('order_detail_id',$rt_detail['order_detail_id'])->where('test_id',$rt_detail['test_id'])->where('deleted_at IS NULL')->findAll();
                }
                $laboratoryResultsModel->insertBatch($result_detail_data);
            }
        return '{"status":"success", "value":"'.$rt_line['order_id'].'", "test":"'.$rt_detail['test_id'].'", "order":"'.$rt_detail['order_detail_id'].'"}';
        }
        
    }  
  
    private function checkresult($result)
    {
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');  
        $rt = $laboratoryResultsModel->where('order_detail_id',$result[0]['order_detail_id'])->findAll();
        //BluelimeSystem::show_and_die($rt);
        return $rt;

    }
    public function view_lab_printout(): string
    {
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $serviceModel = model('Hospital/ServiceModel');
        $userModel = model('System/UserModel');

        
        $order_result = $laboratoryResultsModel->where('order_id',@$_GET['order_main_id'])->orderby('test_id asc')->findAll();
        
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
                .view('Hospital/Laboratory/_left_sidebar_laboratory')
                .view('Include/_left_sidebar_bottom')
                .view('Hospital/Laboratory/view_lab_result')
                .view('Include/_footer');     
    }
    public function patientslabreportbydate()
    {
       
        $orderMainModel = model('Hospital/OrderMainModel');
        $serviceModel = model('Hospital/ServiceModel');
        $userModel = model('System/UserModel');

         $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');
        $order_result = $laboratoryResultsModel->findAll();
        
        foreach($order_result as $index => $rt){
            $order_result[$index]['patient_detail'] = $orderMainModel->where('id',@$_GET['order_main_id'])->findAll()[0]??[];
            $order_result[$index]['test_name'] = $serviceModel->where('id',$rt['test_id'])->findColumn('service_name')[0];
            $order_result[$index]['approved_by'] = $userModel->where('id',$rt['registered_by'])->findColumn('name')[0];
        }
        BluelimeSystem::show_and_die($order_result);
        $data['order_result'] = $order_result;
        
        $data['active_menu'] = 'labdashboard';

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Hospital/Laboratory/_left_sidebar_laboratory')
                .view('Include/_left_sidebar_bottom')
                .view('Hospital/Laboratory/lab_dashboard')
                .view('Include/_footer'); 
    }
}