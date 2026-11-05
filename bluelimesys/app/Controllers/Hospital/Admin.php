<?php

namespace App\Controllers\Hospital;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimeSystem;

class Admin extends \App\Controllers\BaseController
{
    public function index()
    {
        $uid = $_GET['uid'] ?? -1;
        $userModel = model('System/UserModel');
        $userLoginfoModel = model('System/UserLoginfoModel');
        $storeModel = model('Inventory/StoreModel');
        
        $users = $userModel->where('id <> ', 0)->orderBy('active DESC')->orderBy('name')->findAll();
        $edituser = $userModel->where('id', $uid)->findAll();
        //\BluelimeSystem::show_and_die($user);
        $userLoginfo = $userLoginfoModel->orderBy('id DESC')->findAll(50);
        foreach($userLoginfo as $index => $user){
            $userLoginfo[$index]['name']= $userModel->where('id',$user['user_id'])->findColumn('name')[0];
        }

        $stores = $storeModel->findAll();
       
        $data['active_menu'] = 'usermanagment';
        $data['users'] = $users;
        $data['edituser'] = $edituser;
        $data['userLoginfo'] = $userLoginfo;
        $data['stores'] = $stores;
        $data['default_store_roles'] = [BluelimeConfiguration::USER_ROLE::COMPOUNDING,BluelimeConfiguration::USER_ROLE::DISPENSARY,BluelimeConfiguration::USER_ROLE::STORE];
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Admin/_left_sidebar_admin')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Admin/new_user')

        .view('Include/_footer');
                
    }
    public function admindashboard()
    {
        $appointmentModel = model('Hospital/AppointmentModel'); 
        $patientModel = model('Hospital/PatientModel'); 
        $visitModel = model('Hospital/VisitModel'); 
        $no_of_patient = $patientModel->findColumn('count(id)')[0];
        $no_of_appointments = $appointmentModel->findColumn('count(id)')[0];
        $no_of_appointments_visit = $appointmentModel->where('appointment_status',3)->findColumn('count(id)')[0];
        $no_of_visits = $visitModel->findColumn('count(id)')[0];
        $start_date = (!empty($_GET['start_date'])?$_GET['start_date']:date('Y-m-d')).'%';
        $end_date = (!empty($_GET['end_date'])?$_GET['end_date']:date('Y-m-d')).' 23:59:59';
        
        if(!empty($_GET['selected_range'])){
             if($_GET['selected_range']==='this_month'){
                 $appointments = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('MONTH(appointment_date)',date('m'))->where('YEAR(appointment_date)',date('Y'))->where('appointment_status <>',1)->groupBy('appointment_status')->findAll(); 
             }
             else if($_GET['selected_range']==='last_month'){
                 $appointments = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('MONTH(appointment_date)',date('m')-1)->where('YEAR(appointment_date)',date('Y'))->where('appointment_status <>',1)->groupBy('appointment_status')->findAll(); 
             }
             else if($_GET['selected_range']==='this_year'){
                 $appointments = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('YEAR(appointment_date)',date('Y'))->where('appointment_status <>',1)->groupBy('appointment_status')->findAll(); 
             }
             else if($_GET['selected_range']==='all_time'){
                 $appointments = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('appointment_status <>',1)->groupBy('appointment_status')->findAll(); 
             }
         }
         else{           
            $appointments = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('appointment_status <>',1)->where('appointment_date >= ',$start_date)->where('appointment_date <= ',$end_date)->groupBy('appointment_status')->findAll(); 
         }

        $data['appointments'] = $appointments;
        $data['no_of_patient'] = $no_of_patient;
        $data['no_of_visits'] = $no_of_visits;
        $data['no_of_appointments'] = $no_of_appointments;
        $data['no_of_appointments_visit'] = $no_of_appointments_visit;
        $data['active_menu'] = 'admindashboard';
        
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Admin/_left_sidebar_admin')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Admin/admin_dashboard')

            .view('Include/_footer');
    }
    
    
    public function newservice()
    
    {
        $serviceModel = model('Hospital/ServiceModel');

        
        $services = $serviceModel->orderBy('service_type','ASC')->orderBy('service_name','ASC')->findAll();
        $service = $serviceModel->where('id',@$_GET['sid'])->findAll();

        $data['active_menu'] = 'setupdata';
        $data['services'] = $services;
        $data['service'] = $service;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Admin/_left_sidebar_admin')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Admin/new_service')

        .view('Include/_footer');
                
    }

    public function newcompany()
    
    {
        $companyModel = model('Hospital/CompanyModel');

        
        $companies = $companyModel->findAll();
        $company = $companyModel->where('id',@$_GET['cid'])->findAll();

        $data['active_menu'] = 'setupdata';
        $data['companies'] = $companies;
        $data['company'] = $company;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Admin/_left_sidebar_admin')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Admin/new_company')

        .view('Include/_footer');
                
    }
    
    public function pricesetup()
    {   
        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $companyModel = model('Hospital/CompanyModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
                
        $pricelist = $pricelistModel->orderBy('company_name')->findAll();
        $price = $pricelistModel->where('id',@$_GET['plid'])->findAll();
        $companies = $companyModel->where('is_active','1')->orderBy('company_name')->findAll();
        $services = $serviceModel->where('is_active','1')->where('id >',0)->orderBy('service_name','ASC')->orderBy('service_name','ASC')->findAll();
        $pricelist_id = $_GET['plid'] ?? 1;
        $service_id_list = [];

        foreach($services as $service){
            array_push($service_id_list, $service['id']);
        }
        if($services){
        $pricelist_detail = $pricelistDetailModel->where('pricelist_id', $pricelist_id)->whereIn('service_id', $service_id_list)->findAll();

            foreach($services as $index => $service){
                foreach($pricelist_detail as $detail){
                    if($service['id'] === $detail['service_id']){
                        $services[$index]['_detail'] = $detail;
                        break;
                    }
                }
            }
        }
        $pricelist_company_name = $pricelistModel->where('id',$pricelist_id)->findColumn('company_name');

        $companies_with_price_list = $pricelistModel->select('MAX(id) as price_list_id, company_id, MAX(company_name) as company_name')->where('status',1)->groupBy('company_id')->orderBy('company_name')->findAll();
        if(($companies_with_price_list[0]['company_id']??-99) == 1)
            $companies_with_price_list[0]['company_name'] = '- DEFAULT -';

        $companies_with_out_price_list = [];
        //BluelimeSystem::print_associated_array($companies);
        foreach($companies as $company){
            $ignore = false;
            foreach($companies_with_price_list as $company2){
                if($company['id'] == $company2['company_id']){
                    $ignore = true;
                    break;
                }
            }
            if(!$ignore)
                $companies_with_out_price_list[] = $company;
        }

        $data['active_menu'] = 'setupdata';
        $data['pricelist'] = $pricelist;
        $data['pricelist_detail'] = $pricelist_detail??[];
        $data['companies_with_price_list'] = $companies_with_price_list;
        $data['companies_with_out_price_list'] = $companies_with_out_price_list;
        $data['price'] = $price;
        $data['companies'] = $companies;
        $data['services'] = $services;
        $data['pricelist_company_name'] = $pricelist_company_name??"";
        $data['loggeduserdata'] = $loggeduserdata;


        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Admin/_left_sidebar_admin')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Admin/new_price_setup')

            .view('Include/_footer');
    }

    public function clone_price_list()
    {
        $from_price_list_id = $_POST['from_price_list_id'];
        $to_company_id = $_POST['to_company_id'];

        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $companyModel = model('Hospital/CompanyModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $to_company = $companyModel->find($to_company_id);

        $old_price_list_id_list = $pricelistModel->where('company_id', $to_company_id)->findColumn('id')??[-1];
        $pricelistModel->whereIn('id', $old_price_list_id_list)->delete();
        $pricelistDetailModel->whereIn('pricelist_id', $old_price_list_id_list)->delete();

        $new_price_list_data = [
            'company_id' => $to_company_id,
            'registered_by' => $loggeduser['id'],
            'description'   => 'Cloned',
            'registered_date' => date('Y-m-d'),
            'company_name' => $to_company['company_name'],
            'registered_by_name' => $loggeduser['name'],
            'status'    => 1
        ];

        $new_price_list_id = $pricelistModel->insert($new_price_list_data);

        $clone_query = "INSERT INTO `hospital_pricelist_detail` (`pricelist_id`,`service_id`, `service_name`, `service_type`, `service_price`, `updated_date`, `registered_by`, `registered_by_name`, `status`)
        SELECT '$new_price_list_id' as `pricelist_id`, `service_id`, `service_name`, `service_type`, `service_price`, `updated_date`, `registered_by`, `registered_by_name`, `status` FROM `hospital_pricelist_detail` 
        WHERE `pricelist_id` = $from_price_list_id AND `deleted_at` IS NULL";

        $pricelistDetailModel->db->query($clone_query);
        $role = 'hospital/admin';
        if($loggeduser['role']==BluelimeConfiguration::USER_ROLE::FINANCE)
            $role = 'inventory/finance';
        return redirect()->to("/$role/pricesetup?plid=$new_price_list_id");
    }
    
    public function adduser()
    {
        $userModel = model('System/UserModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        $username     = trim($_POST['username']);
        $name         = $_POST['full_name'];
        $password    = $_POST['password']??"0000";
        $active      = 1;
        $role        = $_POST['role'];
        $default_store        = $_POST['default_store'] ?? '';
        $extra_role  = $_POST['extra_role'] ?? [];
        $email       = $_POST['email'];
        $mobile        = $_POST['mobile'];
        $registered_by_name        = $logged_user_name;
        $registered_by         = $logged_user;
        $pin_code       = "0000";

        $data = [
            'username'        => $username,
            'name'           => $name,
            'password'           => md5($password),
            'active'            => $active,
            'role'              => $role,
            'extra'              => BluelimeSystem::convert_array_to_string(['extra_role' => $extra_role, 'default_store' => $default_store]),
            'email'                  => $email,
            'mobile'              => $mobile,
            'pin_code'            => md5($pin_code),
            'registered_by'         => $registered_by, 
            'registered_by_name'    => $registered_by_name
        ];

        if (is_uploaded_file($_FILES['digital_signature']['tmp_name'])) {
            $digital_signature = file_get_contents($_FILES['digital_signature']['tmp_name']);
            $data['digital_signature'] = $digital_signature;
        }

        $id = intval($_POST['id'])?? 0;
        $edit = intval($_POST['edit'])?? 0;
        
        if ($id != 0 && $edit == 1){
            unset($data["password"]);
            unset($data["pin_code"]);
            //BluelimeSystem::show_and_die($data);
            $userModel->update($id,$data);
        }
        else
           
            $id = $userModel->insert($data,true);

            
        return redirect()->to(site_url('/hospital/admin/?uid='.$id.'&edit=1'));
    }

    public function getexistingusername()
    {
        $userModel = model('System/UserModel');
       
        $username = $_POST['username'];
        
        $result = $userModel->where('username', $username)->findColumn('count(id)')[0];
        
        if (intval($result) > 0)
            $result  = true;
        else
            $result = false;
           // BluelimeSystem::show_and_die($result);
        echo $result;
        
    }
    public function addpricemain()
    {
        $pricelistModel = model('Hospital/PricelistModel');
        $companyModel = model('Hospital/CompanyModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        
        $company_id     = $_POST['company_id'];
        $company_name = $companyModel->where('id',$company_id )->findColumn('company_name');
        $registered_by    = $logged_user;
        $description    = $_POST['description'];
        $company_name  = $company_name[0];
        $status            = $_POST['status'];
        $registered_by_name        = $logged_user_name;

        $data = [
            'company_id'        => $company_id,
            'registered_by'           => $registered_by,
            'description'           => $description,
            'company_name'            => $company_name,
            'status'              => $status, 
            'registered_by_name'    => $registered_by_name
        ];
        
        $id = $_POST['id']?? 0;
        if ($id != 0)
            $pricelistModel->update($id,$data);
        else
            $id = $pricelistModel->insert($data,true);

        return redirect()->to(site_url('/hospital/admin/pricesetup?plid='.$id));
    }

    public function updatepricedetail(){
        $price_list_id = $_POST['price_list_id'];
        $price = $_POST['price'];
        $is_active = $_POST['is_active'];
        $service_id = $_POST['service_id'];

        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $pricelistDetailModel->where('pricelist_id', $price_list_id)->where('service_id', $service_id)->delete();

        $serviceModel = model('Hospital/ServiceModel');

        $service = $serviceModel->find($service_id);

        $data_detail= array(
            'service_price' => $price, 
            'service_id' => $service_id,  
            'service_name' => $service['service_name'],
            'service_type' => $service['service_type'],
            'pricelist_id' => $price_list_id, 
            'registered_by' => $loggeduserdata['id'], 
            'registered_by_name' => $loggeduserdata['name'], 
            'status' => $is_active?1:0
        );
        
        $pricelistDetailModel->insert($data_detail,true);    
        
        return BluelimeSystem::get_success_ajax_return('done');
    }

    public function addpricedetail()
    {
        //BluelimeSystem::print_associated_array($_POST);

        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        $pricelistDetailModel->where('pricelist_id',$_POST['pricelist_id'] [0])->delete();
        $data = [];
        for($i=0; $i<count( $_POST['service_id']); $i++) {
            $service_price = $_POST['service_price'] [$i];
            $service_id = $_POST['service_id'] [$i];
            $service_name = $_POST['service_name'] [$i];
            $service_type = $_POST['service_type'] [$i];
            $pricelist_id = $_POST['pricelist_id'] [$i];
            $status = (($_POST['is_active_'.$_POST['service_id'][$i]]??0)=='on')?1:0;
            $registered_by = $logged_user;
            $registered_by_name = $logged_user_name;

            $data_detail= array(
            'service_price' => $service_price, 
            'service_id' => $service_id,  
            'service_name' => $service_name,
            'service_type' => $service_type,
            'pricelist_id' => $pricelist_id, 
            'registered_by' => $registered_by, 
            'registered_by_name' => $registered_by_name, 
            'status' => $status 
            );
            
            if(intval($service_price) > 0){
                array_push($data, $data_detail);
            }
        }
        if(!empty($data))
            $pricelistDetailModel->insertBatch($data,true);
        
        return redirect()->to(site_url('/hospital/admin/pricesetup?plid='.$_POST['pricelist_id'] [0]));
    }

    public function addservice()
    {
        $serviceModel = model('Hospital/ServiceModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        
        $service_name    = $_POST['service_name'];
        $service_type    = $_POST['service_type'];
        $measurement    = $_POST['measurement'];
        $gl_account       = $_POST['gl_account'];
        $is_active            = $_POST['status'];
        $registered_by_name        = $logged_user_name;
        $registered_by        = $logged_user;

        $data = [
            'service_name'        => $service_name,
            'service_type'           => $service_type,
            'measurement'           => $measurement,
            'is_active'            => $is_active,
            'registered_by'              => $registered_by, 
            'registered_by_name'    => $registered_by_name,
            'service_code' => '',
            'gl_account'    => $gl_account
        ];

        if($data['service_type'] == 'Procedure (Nurse)'){
            $data['service_type'] = 'Procedure';
            $data['service_code'] = 'NURSE';
        }
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $serviceModel->update($id,$data);
        else
            $id = $serviceModel->insert($data,true);

        return redirect()->to(site_url('/hospital/admin/newservice?sid='.$id.'&edit=1'));
    }

    public function addcompany()
    {
        $companyModel = model('Hospital/CompanyModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        
        $company_name    = $_POST['company_name'];
        $tin_number    = $_POST['tin_number'];
        $description    = $_POST['description'];
        $unpaid_visit_days    = $_POST['unpaid_visit_days'];
        $is_active            = $_POST['status'];
        $is_cash            = $_POST['is_cash'];
        $registered_by_name        = $logged_user_name;
        $registered_by        = $logged_user;

        $data = [
            'company_name'        => $company_name,
            'tin_number'           => $tin_number,
            'description'           => $description,
            'unpaid_visit_days'           => $unpaid_visit_days,
            'is_active'            => $is_active,
            'is_cash'            => $is_cash,
            'registered_by'              => $registered_by, 
            'registered_by_name'    => $registered_by_name
        ];
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $companyModel->update($id,$data);
        else
            $id = $companyModel->insert($data,true);

        return redirect()->to(site_url('/hospital/admin/newcompany?sid='.'&edit=1'));
    }
    public function changeuserstatus()
    {
        $userModel = model('System/UserModel');
        $scheduleModel = model('Hospital/ScheduleModel');
        
        $user_id = $_POST['user_id'];
        $status = intval($_POST['active'])==0? 1: 0;
        //BluelimeSystem::show_and_die($status);
        $data = [
            'active' => $status
        ];

        $userModel->update($user_id,$data);
        $scheduleModel->where('doctor_id',$user_id)->set('status',$status)->update();
    }
    public function changepricestatus()
    {
        $pricelistModel = model('Hospital/PricelistModel');
        
        $price_list_id = $_GET['plid'];
        $status = $_GET['st'];
        $data = [
            'status' => $status
        ];

        $pricelistModel->update($price_list_id,$data);
       return redirect()->to(site_url('/hospital/admin/pricesetup?plid='.$price_list_id ));
    }
    public function resetpassword()
    {
        $userModel = model('System/UserModel');
        
        //BluelimeSystem::show_and_die($_POST);
        $user_id = $_POST['user_id'];
        $password = '0000';
        $data = [
            'password' => md5($password)
        ];

        $userModel->update($user_id,$data);
        return BluelimeSystem::get_success_ajax_return('ok');
    }
    public function changecompanystatus()
    {
        $companyModel = model('Hospital/CompanyModel');
        $pricelistModel = model('Hospital/PricelistModel');
        
        $company_id = $_GET['cid'];
        $status = $_GET['st'];
        $data = [
            'is_active' => $status
        ];

        $companyModel->update($company_id,$data);
        $pricelistModel->where('company_id',$company_id)->delete();
       return redirect()->to(site_url('/hospital/admin/newcompany?cid='.$company_id ));
    }
    public function changeservicestatus()
    {
        $serviceModel = model('Hospital/ServiceModel');
        
        $service_id = $_GET['sid'];
        $status = $_GET['st'];
        $data = [
            'is_active' => $status
        ];

        $serviceModel->update($service_id,$data);
       return redirect()->to(site_url('/hospital/admin/newservice?sid='.$service_id ));
    }
    public function changepricedetailstatus()
    {
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        
        $price_list_detail_id = $_GET['pldid'];
        $status = $_GET['st'];
        $data = [
            'status' => $status
        ];

        $pricelistDetailModel->update($price_list_detail_id,$data);
       return redirect()->to(site_url('/hospital/admin/pricesetup?plid='.@$_GET['plid'] ));
    }
    
    public function proceduresetup()
    {   
        $procedureResultAttributeModel = model('Hospital/ProcedureResultAttributeModel');
        $serviceModel = model('Hospital/ServiceModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
                
        $procedures = $serviceModel->where('service_type','procedure')->where('is_active','1')->where('service_code','NURSE')->where('id >',0)->orderBy('service_name','ASC')->findAll();
        //$price = $pricelistModel->where('id',@$_GET['plid'])->findAll();
        //$companies = $companyModel->where('is_active','1')->findAll();
        //$services = $serviceModel->where('is_active','1')->where('id >',0)->orderBy('service_name','ASC')->findAll();
        //$pricelist_id = $_GET['plid'] ?? 1;
        //$service_id_list = [];

        $attribute = $procedureResultAttributeModel->where('procedure_id',@$_GET['pid'])->findAll();
        
       
        
        //$pricelist_company_name = $pricelistModel->where('id',$pricelist_id)->findColumn('company_name');

        $data['active_menu'] = 'setupdata';
        
        $data['procedures'] = $procedures;
        $data['attribute'] = $attribute;
        $data['loggeduserdata'] = $loggeduserdata;



        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Admin/_left_sidebar_admin')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Admin/procedure_setup')

            .view('Include/_footer');
    }
    public function addProcedureAttribute()
    {
        $procedureResultAttributeModel = model('Hospital/ProcedureResultAttributeModel');
       

        $procedure_id     = $_POST['procedure_id'];
        $procedure_name    = $_POST['procedure_name'];
        $attribute_name    = $_POST['attribute_name'];
        $input_type        = $_POST['input_type'];
        $is_required        = $_POST['is_required'];
        

        $data = [
            'procedure_id'        => $procedure_id,
            'procedure_name'           => $procedure_name,
            'attribute_name'            => $attribute_name,
            'input_type'              => $input_type,
            'is_required'              => $is_required,
        ];
        //BluelimeSystem::show_and_die($data);
    
        $id = $procedureResultAttributeModel->insert($data,true);

        return redirect()->to(site_url('/hospital/admin/proceduresetup?pid='.$procedure_id.'&pname='.$procedure_name));
    }
    public function deleteprocedureattribute()
    {
        $ProcedureResultAttributeModel = model('Hospital/ProcedureResultAttributeModel');
       
        $procedure_id     = @$_POST['procedure_id'];
        $procedure_name    = @$_POST['procedure_name'];
        
        $ProcedureResultAttributeModel->delete($_GET['attid']);
        return redirect()->to(site_url('/hospital/admin/proceduresetup?pid='.$_GET['pid'].'&pname='.$_GET['pname']));
    }
    public function labsetup()
    {   
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
        $serviceModel = model('Hospital/ServiceModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
                
        $Lab_tests = $serviceModel->where('service_type','Laboratory')->where('is_active','1')->where('id >',0)->orderBy('service_name','ASC')->findAll();
        //$price = $pricelistModel->where('id',@$_GET['plid'])->findAll();
        //$companies = $companyModel->where('is_active','1')->findAll();
        //$services = $serviceModel->where('is_active','1')->where('id >',0)->orderBy('service_name','ASC')->findAll();
        //$pricelist_id = $_GET['plid'] ?? 1;
        //$service_id_list = [];

        $attribute = $laboratoryResultAttributeModel->where('test_id',@$_GET['tid'])->findAll();
        
       
        
        //$pricelist_company_name = $pricelistModel->where('id',$pricelist_id)->findColumn('company_name');

        $data['active_menu'] = 'setupdata';
        
        $data['Lab_tests'] = $Lab_tests;
        $data['attribute'] = $attribute;
        $data['loggeduserdata'] = $loggeduserdata;



        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Hospital/Admin/_left_sidebar_admin')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Admin/lab_setup')
            .view('Include/_footer');
    }
    public function addAttribute()
    {
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
       

        $test_id     = $_POST['test_id'];
        $test_name    = $_POST['test_name'];
        $attribute_name    = $_POST['attribute_name'];
        $reference_range_from  = $_POST['reference_range_from'];
        $reference_range_to            = $_POST['reference_range_to'];
        $reference_range_gender        = $_POST['reference_range_gender'];
        $reference_range_unit        = $_POST['reference_range_unit'];
        $quantifiable         = $_POST['quantifiable']??0;
        

        $data = [
            'test_id'        => $test_id,
            'test_name'           => $test_name,
            'attribute_name'            => $attribute_name,
            'reference_range_from'              => $reference_range_from,
            'reference_range_to'                  => $reference_range_to,
            'reference_range_gender'              => $reference_range_gender,
            'reference_range_unit'          => $reference_range_unit,
            'quantifiable'         => $quantifiable
        ];
        //BluelimeSystem::show_and_die($data);
    
        $id = $laboratoryResultAttributeModel->insert($data,true);

        return redirect()->to(site_url('/hospital/admin/labsetup?tid='.$test_id.'&tname='.$test_name));
    }
    public function deleteattribute()
    {
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
       
        $test_id     = @$_POST['test_id'];
        $test_name    = @$_POST['test_name'];
        
        $laboratoryResultAttributeModel->delete($_GET['attid']);
        return redirect()->to(site_url('/hospital/admin/labsetup?tid='.$_GET['tid'].'&tname='.$_GET['tname']));
    }
    
    public function headerinfo()
    
    {
        $headerInformation = model('System/HeaderInformation');

        
        $header_info = $headerInformation->where('id <> ', 0)->findAll();
        $edit_header_info = $headerInformation->where('id',@$_GET['hid'])->findAll();
       
        $data['active_menu'] = 'headerinfo';
        $data['header_info'] = $header_info;
        $data['edit_header_info'] = $edit_header_info;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Admin/_left_sidebar_admin')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Admin/header_information')

        .view('Include/_footer');
                
    }
    public function addheader()
    {
        $headerInformation = model('System/HeaderInformation');

        $company_name     = $_POST['company_name'];
        $mobile    = $_POST['mobile'];
        $tel    = $_POST['tel'];
        $active      = 0;
        $email            = $_POST['email'];
        $website        = $_POST['website'];
        $tin_no        = $_POST['tin_no'];
        $location        = $_POST['location'];

        $data = [
            'company_name'        => $company_name,
            'mobile'           => $mobile,
            'tel'           => $tel,
            'active'            => $active,
            'email'                  => $email,
            'website'              => $website,
            'tin_no'          => $tin_no,
            'location'         => $location
        ];

        if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
        $logo = file_get_contents($_FILES['logo']['tmp_name']);
        $data = [
            'company_name'        => $company_name,
            'mobile'           => $mobile,
            'tel'           => $tel,
            'active'            => $active,
            'email'                  => $email,
            'website'              => $website,
            'tin_no'          => $tin_no,
            'logo'          => $logo,
            'location'         => $location
        ];
        }
        if (is_uploaded_file($_FILES['logo_small']['tmp_name'])) {
        $logo_small = file_get_contents($_FILES['logo_small']['tmp_name']);
       // BluelimeSystem::show_and_die($logo_small);
        $data = [
            'company_name'        => $company_name,
            'mobile'           => $mobile,
            'tel'           => $tel,
            'active'            => $active,
            'email'                  => $email,
            'website'              => $website,
            'tin_no'          => $tin_no,
            'logo_small'          => $logo_small,
            'location'         => $location
        ];
        }
        if (is_uploaded_file($_FILES['logo_printouts']['tmp_name'])) {
        $logo_printouts = file_get_contents($_FILES['logo_printouts']['tmp_name']);
       // BluelimeSystem::show_and_die($logo_small);
        $data = [
            'company_name'        => $company_name,
            'mobile'           => $mobile,
            'tel'           => $tel,
            'active'            => $active,
            'email'                  => $email,
            'website'              => $website,
            'tin_no'          => $tin_no,
            'logo_printouts'          => $logo_printouts,
            'location'         => $location
        ];
        }

        
        
        
        $id = intval($_POST['id'])?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $headerInformation->update($id,$data);
        else
            $id = $headerInformation->insert($data,true);

        return redirect()->to(site_url('/hospital/admin/headerinfo?id='.'&edit=1'));
    }

    function pos_machine(){
        $posMachineModel = model('POS/POSMachineModel');
        $pos_machines = $posMachineModel->findAll();
        $data = [
            'pos_machines' => $pos_machines,
            'active_menu' => 'posmachine'
        ];
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Admin/_left_sidebar_admin')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Admin/pos_machine')
        .view('Include/_footer');
    }
 public function changeheaderstatus()
    {
        $headerInformation = model('System/HeaderInformation');
        $headerId = (int) ($_POST['header_id'] ?? 0);

        if ($headerId > 0) {
            $status = (int) ($_POST['active'] ?? 0) === 1 ? 0 : 1;
            $headerInformation->update($headerId, ['active' => $status]);
        }

        return redirect()->to(site_url('/hospital/admin/headerinfo'));
    }
    function active_inactive_pos_machine(){
        $id = $_POST['id'];
        $active = $_POST['active'];
        $pOSMachineModel = model('POS/POSMachineModel');
        $pOSMachineModel->update($id, ['active'=>$active]);
        echo $pOSMachineModel->db->lastQuery;
    }

    public function pendingbilldeletion()
    {
        $billModel = model('Hospital/BillModel');
        $pending_bills = $billModel->where('bill_status', BluelimeConfiguration::BILL_REMOVE_REQUESTED)->findAll();
        $data = ['pending_bills' => $pending_bills];
        $data['active_menu'] = 'pending_bill';
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Admin/_left_sidebar_admin')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Admin/pending_bills')
            .view('Include/_footer');
    }

    public function pendingbilldetail()
    {
        $bill_id = $_GET['id'] ?? '-1';
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');

        $bill = $billModel->find($bill_id);
        $billDetail = $billDetailModel->where('bill_id', $bill_id)->findAll();

        $data['active_menu'] = 'pending_bill';
        $data['bill'] = $bill;
        $data['bill_detail'] = $billDetail;

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Admin/_left_sidebar_admin')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Admin/bill_detail')
            .view('Include/_footer');

    }

    public function deletependingbill()
    {
        $bill_id = $_GET['id'] ?? '-1';
        $billModel = model('Hospital/BillModel');
        $bill = $billModel->find($bill_id);
        if(!empty($bill))
        {
            $visit_id = $bill['visit_id'];
            $visitModel = model('Hospital/VisitModel');
            $visit = $visitModel->find($visit_id);
            $visit_status = $visit['visit_status'] ?? -1;
            if($visit_status == 0)
                $visitModel->delete($visit_id);
        }

        $update_data = ['bill_status' => BluelimeConfiguration::BILL_REMOVED];
        $billModel->update($bill_id, $update_data);
        $billModel->delete($bill_id);
        return redirect()->to('/hospital/admin/pendingbilldeletion');
    }

    public function api_setting(){
                
        $api_folder_path = APPPATH . 'Controllers/API';
        $api_folder = scandir($api_folder_path);

        $api_folders = [];
        foreach($api_folder as $childe){
            if(is_dir($api_folder_path.'/'.$childe)){
                if(str_starts_with($childe, 'API')){
                    $api_folders[] = $childe;
                }
            }
        }

        $api_list = [];
        foreach($api_folders as $api_folder){
            $api_folder_path = APPPATH . 'Controllers/API/' . $api_folder;
            $apis = scandir($api_folder_path);
            $api_list[$api_folder] = [];
            foreach($apis as $api_file){
                if(is_dir($api_folder_path.'/'.$api_file)){
                    $api_versions = scandir($api_folder_path.'/'.$api_file);
                    foreach($api_versions as $api_version){
                        if(is_file("$api_folder_path/$api_file/$api_version") && str_ends_with($api_version, '.php') && str_starts_with($api_version,'V')){
                            $api_v = str_replace('.php', '', $api_version);
                            $api_list[$api_folder][$api_file][] = $api_v;
                        }
                    }
                }
            }
        }

        $selected_api = explode(",",($_POST['api_id'] ?? ''));
        $api_group = $selected_api[0] ?? '';
        $api_name = $selected_api[1] ?? '';
        $api_version = $selected_api[2] ?? '';

        $apiModel = model('System/APIModel');
        $api = $apiModel->where('api_group', $api_group)->where('name', $api_name)->where('api_version', $api_version)->findAll();
        $api = $api[0] ?? [];
        //BluelimeSystem::print_associated_array($api_list);

        $data = [
            'api' => $api,
            'api_group' => $api_group,
            'api_name' => $api_name,
            'api_version' => $api_version,
            'api_list' => $api_list,
            'active_menu' => 'api_setting'
        ];
        
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Admin/_left_sidebar_admin')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Admin/api_setting')
            .view('Include/_footer');
    }

    public function api_setting_save(){
        $apiModel = model('System/APIModel');
        $api_group = $_POST['api_group'];
        $api_name = $_POST['api_name'];
        $api_version = $_POST['api_version'];
        $active = $_POST['api_enabled'] ?? 0;
        $api_data = $_POST['api_data'];
        $data = [
            'api_group' => $api_group,
            'name' => $api_name,
            'api_version' => $api_version,
            'active' => $active,
            'data' => $api_data
        ];
        
        $apiModel->where('api_group', $api_group)
            ->where('name', $api_name)
            ->where('api_version', $api_version)
            ->delete();
            
        $apiModel->insert($data);
        
        return BluelimeSystem::get_success_ajax_return('API settings saved successfully.');
    }
}