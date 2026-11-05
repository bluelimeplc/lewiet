<?php

namespace App\Controllers\Inventory;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;
class Store extends \App\Controllers\BaseController
{
    public function index()
    {
        $srvMainModel = model('Inventory/SRVMainModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        //$store_id = $loggeduser['extra']['default_store'] ?? -1;
        $store_id = 1;

        $search_key = $_GET['search_key'] ?? '';
        $filter_date = $_GET['filter_date'] ?? date('Y-m-d');
        $position = $_GET['position'] ?? '';

        if(trim($search_key) != '')
            $srvMainModel = $srvMainModel->where('id LIKE', "%$search_key%");

        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $srvMainModel = $srvMainModel->where('request_date <=', $filter_date);
            else
                $srvMainModel = $srvMainModel->where('request_date >=', $filter_date);
        }
        
        $srv_main = $srvMainModel->where('is_issued' ,0)->where('requested_store', $store_id)->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        $pager = $srvMainModel->pager;

        foreach($srv_main as $index => $store){
            $srv_main[$index]['requesting_store'] = $storeModel->where('id',$store['requesting_store'])->findColumn('store_name')[0];
            $srv_main[$index]['requested_store'] = $storeModel->where('id',$store['requested_store'])->findColumn('store_name')[0];
        }
        foreach($srv_main as $index => $user){
            $srv_main[$index]['requested_by__name'] = $userModel->where('id',$user['requested_by'])->findColumn('name')[0];
        }
        
        $data['srv_main'] = $srv_main;
        $data['active_menu'] = 'pendingstorerequest';
        $data['search_key'] = $search_key;
        $data['filter_date'] = $filter_date;
        $data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();

        return  view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/pending_store_request')
                .view('Include/_footer');    
    }
    
    public function storedashboard()
    {
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        $itemModel = model('Inventory/ItemModel');
        $supplierModel = model('Inventory/SupplierModel');
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        
        $store_id = 1;

        $transactions = $transactionModel->select('item_id, batch_number, expiry_date, SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id',$store_id)->groupBy('item_id, batch_number, expiry_date')->having('SUM(quantity) >',0)->findAll();
        $items = $itemModel->findAll();
        $items = BluelimeSystem::index_object_list_array_by_unique_field($items, 'id');
        $near_expiry_items = [];
        foreach($transactions as $transaction){
            if(empty($transaction['expiry_date']) || ($transaction['expiry_date']=='0000-00-00'))
                continue;

            $expiry_in_days = BluelimeSystem::date_difference($transaction['expiry_date'], date('Y-m-d'));
            if((intval($expiry_in_days) <= $items['id_'.$transaction['item_id']]['notify_expiry']) || ($transaction['expiry_date'] <= date('Y-m-d'))){
                $items['id_'.$transaction['item_id']]['batch_number'] = $transaction['batch_number'];
                $items['id_'.$transaction['item_id']]['expiry_date'] = $transaction['expiry_date'];
                $items['id_'.$transaction['item_id']]['quantity'] = $transaction['quantity'];
                $items['id_'.$transaction['item_id']]['remaining_days'] = $transaction['expiry_date'] < date('Y-m-d') ? -1 * $expiry_in_days: $expiry_in_days;
                $near_expiry_items[] = $items['id_'.$transaction['item_id']];
            }
        }

        //$item_quantities = BluelimeSystem::get_all_items_quantity($store_id);
        $item_quantities = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id',$store_id)->groupBy('item_id')->findAll();

        $low_balance_items = [];
        foreach($item_quantities as $item_quantity){
            if($item_quantity['quantity'] <= $items['id_'.$item_quantity['item_id']]['min_balance'])
                $low_balance_items[] = ['quantity'=>$item_quantity['quantity'], ...$items['id_'.$item_quantity['item_id']]];
        }

        $low_balance_items = BluelimeSystem::sort_array($low_balance_items, ['item_brand_name','item_generic_name']);
        
        $data['near_expiry_items'] = $near_expiry_items;
        $data['low_balance_items'] = $low_balance_items;
        $data['active_menu'] = 'storedashboard';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Inventory/Store/_left_sidebar_store')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Store/store_dashboard')

            .view('Include/_footer');
    }
    public function new_store()
    {
        $storeModel = model('Inventory/StoreModel');

        
        $stores = $storeModel->orderBy('store_name','ASC')->findAll();
        $store = $storeModel->where('id',@$_GET['sid'])->findAll();
        

        $data['active_menu'] = 'setupdata';
        $data['stores'] = $stores;
        $data['store'] = $store;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Store/_left_sidebar_store')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Store/new_store')

        .view('Include/_footer');      
    }

    public function change_store(){
        $store_id = $_POST['store_id'] ?? -1;
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        
        $user_extra = $logged_user['extra'];
        $user_extra['default_store'] = $store_id;
        $update_data = [
            'extra' => BluelimeSystem::convert_array_to_string($user_extra)
        ];

        $userModel->update($logged_user['id'],$update_data);
        $session = \Config\Services::session();
        $_SESSION['loggeduserdata']['extra'] = $user_extra;
        $referer = $_SERVER['HTTP_REFERER'];

        return redirect()->to($referer);
    }
    
    public function new_supplier()
    {
        $supplier_id = $_GET['supplier_id'] ?? -1;

        $supplierModel = model('Inventory/SupplierModel');

        $suppliers = $supplierModel->findAll();
        $supplier = [];
        foreach($suppliers as $row){
            if($row['id'] == $supplier_id){
                $supplier = $row;
                break;
            }
        }

        $data = [
            'suppliers' => $suppliers,
            'supplier' => $supplier,
        ];
        $data['active_menu'] = 'setupdata';
        
        return view('Include/_header',$data)
            .view('Include/_left_sidebar_top')

            .view('Inventory/Store/_left_sidebar_store')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Store/new_supplier')

            .view('Include/_footer');
                
    }

    public function save_supplier()
    {
        $supplierModel = model('Inventory/SupplierModel');
        
        $company_name   = ucwords($_POST['company_name']);
        $tin_number     = $_POST['tin_number'];
        $mobile         = $_POST['mobile'];
        $phone          = $_POST['phone'];
        $email          = $_POST['email'];
        $web            = $_POST['web'];

        $id             = $_POST['id'] ?? -1;

        $address = [
            'mobile' => $mobile,
            'phone'  => $phone,
            'email'  => $email,
            'web'    => $web
        ];

        $data = [
            'company_name'   => $company_name,
            'tin_number'     => $tin_number,
            'address'        => BluelimeSystem::convert_array_to_string($address),
        ];
        
        if($id > 0)
            $supplierModel->update($id, $data);
        else
            $id = $supplierModel->insert($data);

        return redirect()->to(site_url('/inventory/store/new_supplier?supplier_id='.$id));
    }

    public function save_store()
    {
        $storeModel = model('Inventory/StoreModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];
        // BluelimeSystem::show_and_die($_POST);
        
        
        $store_name    = ucwords($_POST['store_name']);
        $location            = $_POST['location'];
        $registered_by            = $logged_user;
        

        $data = [
            'store_name'        => $store_name,
            'location'           => $location,
            'registered_by'           => $registered_by,
            
        ];
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $storeModel->update($id,$data);
        else
            $id = $storeModel->insert($data,true);

        return redirect()->to(site_url('/inventory/store/new_store'));
    }
    public function new_item_category()
    {
        $itemCategoryModel = model('Inventory/ItemCategoryModel');

        
        $categories = $itemCategoryModel->orderBy('category_name','ASC')->findAll();
        $category = $itemCategoryModel->where('id',@$_GET['cid'])->findAll();
        

        $data['active_menu'] = 'setupdata';
        $data['categories'] = $categories;
        $data['category'] = $category;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Store/_left_sidebar_store')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Store/new_item_category')

        .view('Include/_footer');      
    }
    public function save_item_category()
    {
        $itemCategoryModel = model('Inventory/ItemCategoryModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        //BluelimeSystem::show_and_die($_POST);
        
        
        $category_name    = ucwords($_POST['category_name']);
        $description            = $_POST['description'];
        $registered_by            = $logged_user;
        

        $data = [
            'category_name'        => $category_name,
            'description'           => $description,
            'registered_by'           => $registered_by,
            
        ];
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $itemCategoryModel->update($id,$data);
        else
            $id = $itemCategoryModel->insert($data,true);

        return redirect()->to(site_url('/inventory/store/new_item_category'));
    }public function new_measurement()
    {
        $MeasurementModel = model('System/MeasurementModel');

        
        $measurements = $MeasurementModel->orderBy('measurement_name','ASC')->findAll();
        $measurement = $MeasurementModel->where('id',@$_GET['sid'])->findAll();
        

        $data['active_menu'] = 'setupdata';
        $data['measurements'] = $measurements;
        $data['measurement'] = $measurement;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Store/_left_sidebar_store')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Store/new_measurement')

        .view('Include/_footer');      
    }
    public function save_measurement()
    {
        $measurementModel = model('System/MeasurementModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];
        //BluelimeSystem::show_and_die($_POST);
        
        
        $measurement_name    = ucwords($_POST['measurement_name']);
        $description            = $_POST['description'];
        $registered_by            = $logged_user;
        

        $data = [
            'measurement_name'        => $measurement_name,
            'description'           => $description,
            'registered_by'           => $registered_by,
            
        ];
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $measurementModel->update($id,$data);
        else
            $id = $measurementModel->insert($data,true);

        return redirect()->to(site_url('/inventory/store/new_measurement'));
    }

    public function new_item(): string
    {
        $search_key = $_GET['search_key'] ?? '';

       
        $itemModel = model('Inventory/ItemModel');
        $measurementModel = model('System/MeasurementModel');
        $itemCategoryModel = model('Inventory/ItemCategoryModel');

        if(trim($search_key) != '')
            $itemModel = $itemModel->where('item_code LIKE', "%$search_key%")->orWhere('item_generic_name LIKE', "%$search_key%")->orWhere('item_brand_name LIKE', "%$search_key%")->orWhere('item_category LIKE', "%$search_key%");

        $items = $itemModel->orderBy('id','DESC')->paginate(15);
        $pager = $itemModel->pager;
        $measurements = $measurementModel->findAll();
        $categories = $itemCategoryModel->findAll();
        $item = $itemModel->where('id',@$_GET['iid'])->findAll();
        //BluelimeSystem::show_and_die($measurements[0]);
        $data['active_menu'] = 'new_item';
        $items_j = [];
        foreach($items as $row){
            $items_j["item_id_".$row['id']] = $row;
        }


        $data['items'] = $items_j;
        $data['measurements'] = $measurements;
        $data['categories'] = $categories;
        $data['item'] = $item;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view(($loggeduser['role'] == BluelimeConfiguration::USER_ROLE::DISPENSARY) ? 'Inventory/Dispensary/_left_sidebar_dispensary':'Inventory/Store/_left_sidebar_store')
         
        .view('Include/_left_sidebar_bottom')

               
        .view('Inventory/Store/new_item')

        .view('Include/_footer');

             
    }
    public function save_item()
    {
        $itemModel = model('Inventory/ItemModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];
        // BluelimeSystem::show_and_die($_POST);
        
        $item_generic_name    = ucwords(trim($_POST['item_generic_name']));
        $item_brand_name    = ucwords(trim($_POST['item_brand_name']));
        $item_code    = strtoupper(trim($_POST['item_code']));
        $preparation    = trim($_POST['preparation']);
        $strength    = trim($_POST['strength']);
        $item_category    = trim($_POST['item_category']);
        $item_type    = trim($_POST['item_type']);
        $measurement            = trim($_POST['measurement']);
        $min_balance            = $_POST['min_balance'];
        $notify_expiry            = $_POST['notify_expiry'];
        $is_active            = @$_POST['is_active']=="on" ? 1 : 0;
        $is_taxable            = @$_POST['is_taxable']=="on" ? 1 : 0;
        $is_perishable            = @$_POST['is_perishable']=="on" ? 1 : 0;
        $compounding_measurement  = $_POST['compounding_measurement'];
        $compounding_volume       = $_POST['compounding_volume'];
        $item_registered_by       = $logged_user;
        $item_registered_by_name  = $logged_user_name;
        

        $data = [
            'item_generic_name'        => $item_generic_name,
            'item_brand_name'           => $item_brand_name,
            'item_code'           => $item_code,
            'preparation'           => $preparation,
            'strength'           => $strength,
            'item_category'           => $item_category,
            'item_type'           => $item_type,
            'is_active'            => $is_active,
            'measurement'              => $measurement, 
            'min_balance'              => $min_balance, 
            'notify_expiry'              => $notify_expiry, 
            'is_taxable'              => $is_taxable,
            'is_perishable'              => $is_perishable,
            'compounding_measurement' => $compounding_measurement,
            'compounding_volume'      => $compounding_volume,
            'item_registered_by'              => $item_registered_by, 
            'item_registered_by_name'    => $item_registered_by_name
        ];
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $itemModel->update($id,$data);
        else{
            
            $id = $itemModel->insert($data,true);
            $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
            $peachtree_id = substr(trim(preg_replace('/[^A-Za-z0-9 -]/u', ' ', $item_brand_name.' '.$item_generic_name)),0,8).'_2'.$id;
            $peachtree_description = substr(trim($item_brand_name.' '.$strength. ' '.$preparation ),0,30);
            $peachtreeItemModel->insert([
                'item_id' => $id,
                'peachtree_id' => $peachtree_id,
                'peachtree_description' => $peachtree_description,
            ]);
        }

        return redirect()->to(site_url('/inventory/store/new_item'));
    }
    public function grv_form()
    {
        $logged_user_data = BluelimeAuthentication::get_logged_user_data();
        $itemModel = model('Inventory/ItemModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');
        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');

        $mode = $_GET['mode'] ?? 'new';
        $grv_id = $_GET['grv_id'] ?? -1;
        
        $grv_main = [];
        $grv_detail = [];
        if($mode == 'edit'){
            $last_grv_id = $grv_id - 1; //One will be added on view
            $grv_main = $grvMainModel->find($grv_id);
            if($grv_main['status'] != BluelimeConfiguration::VOUCHER_SATUS::PENDING)
                return redirect()->to('/inventory/store/grv_print?grv_id='.$grv_id);
            
            $grv_detail = $grvDetailModel->where('grv_id', $grv_id)->findAll();
        }
        else
            $last_grv_id = $grvMainModel->select('MAX(id) as id')->findAll()[0]['id'] ?? 0;

        $items = $itemModel->findAll();
        $suppliers = $supplierModel->findAll();
        $suppliers_temp = [];
        foreach($suppliers as $row)
            $suppliers_temp['id_'.$row['id']] = $row;
        $suppliers = $suppliers_temp;

        $store = $storeModel->find($logged_user_data['extra']['default_store'] ?? 0);
        $default_store = $store ?? ['id' => '', 'store_name' => '', 'location'=>''];
        $item_j = [];
        foreach($items as $item)
            $item_j['item_id_'.$item['id']] = $item;

        if($mode == 'edit'){
            $grv_main['grv_supplier_name'] = $suppliers['id_'.$grv_main['supplier_id']]['company_name'];
            $grv_main['fs_no'] = str_replace("M","", $grv_main['fs_no']);
            $grv_main['fs_no'] = str_replace("FS","", $grv_main['fs_no']);
            $receipt_date_ = explode("/",$grv_main['receipt_date']);
            $grv_main['receipt_date_']['date'] = $receipt_date_[0]??'';
            $grv_main['receipt_date_']['month'] = $receipt_date_[1]??'';
            $grv_main['receipt_date_']['year'] = $receipt_date_[2]??'';
        }
        
        $data['active_menu'] = 'grv_form';
        $data['items'] = $item_j;
        $data['suppliers'] = $suppliers;
        $data['last_grv_id'] = $last_grv_id;        
        $data['logged_user_data'] = $logged_user_data;
        $data['default_store'] = $default_store;
        $data['grv_main'] = $grv_main;
        $data['grv_detail'] = $grv_detail;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/grv_form')
                .view('Include/_footer');     
    }

    public function grvsave()
    {
        $grv_main = $_POST['grv_main'] ?? [];
        $grv_detail = $_POST['grv_detail'] ?? '[]';
        $grv_detail = BluelimeSystem::convert_to_array($grv_detail);

        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');

        $store_id = 1;
        $grv_main['store_id'] = $store_id;

        if($grv_main['is_manual_receipt']==1)
            $grv_main['fs_no'] = 'M'.$grv_main['fs_no'];
        else
            $grv_main['fs_no'] = 'FS'.$grv_main['fs_no'];

        $receipt_date = explode("/",$grv_main['receipt_date']);
        
        $grv_main['receipt_date_gc'] = $receipt_date[2]."-".$receipt_date[1]."-".$receipt_date[0];

        if($grv_main['is_ethiopian_calander']){
            $grv_main['receipt_date_gc'] = BluelimeSystem::convert_ec_to_gc($receipt_date[2],$receipt_date[1],$receipt_date[0]);
        }

        $grv_main_id = -1;
        $mode = $_GET['mode'] ?? 'new';
        if($mode == 'update'){
            $grv_main_id = $_POST['grv_id'] ?? -1;
            $grvMainModel->update($grv_main_id, $grv_main);
            $grvDetailModel->where('grv_id',$grv_main_id)->delete();
            $transactionModel->where('transaction_reference', $grv_main_id)->where('transaction_type',BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE)->where('store_id',$store_id)->delete();
        }
        else
            $grv_main_id = $grvMainModel->insert($grv_main);

        $grv_detail_data = [];
        $transaction_data = [];
        $items = BluelimeSystem::gather_objects_by_reference($grv_detail, 'id', 'Inventory/ItemModel','id', true);

        $average_cost_list = $this->calculate_average_cost($grv_detail);

        foreach($grv_detail as $grv_line)
        {
            $grv_line_temp = [
                'grv_id' => $grv_main_id,
                'item_id' => $grv_line['id'],
                'item_detail' => BluelimeSystem::convert_array_to_string($items['id_'.$grv_line['id']]),
                'unit_price' => $grv_line['unit_price'],
                'quantity' => $grv_line['quantity'],
                'tax' => $grv_line['tax'],
                'manufactured_date' => $grv_line['manufactured_date'] ?? date('Y-m-d'),
                'expiry_date' => $grv_line['expiry_date'],
                'batch_number' => $grv_line['batch_no'],
                'average_cost' => $average_cost_list['item_id_'.$grv_line['id']]
            ];

            $transaction_data_temp = [
                'store_id' => $store_id,
                'item_id' => $grv_line['id'],
                'transaction_type' => BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE,
                'transaction_reference' => $grv_main_id,
                'quantity' => $grv_line['quantity'],
                'batch_number' => $grv_line['batch_no'],
                'expiry_date' => $grv_line['expiry_date'],
                'unit_cost' => $average_cost_list['item_id_'.$grv_line['id']]
            ];

            array_push($grv_detail_data, $grv_line_temp);
            array_push($transaction_data, $transaction_data_temp);
        }

        if(!empty($grv_detail_data))
            $grvDetailModel->insertBatch($grv_detail_data);

        if(!empty($transaction_data))
            $transactionModel->insertBatch($transaction_data);

        /*BluelimeSystem::write_log('abiy_test','grvsave_post',print_r($_POST, true),'POST');
        BluelimeSystem::write_log('abiy_test','grvsave_post',print_r($grv_detail_data, true),'grv_detail_data');
        */

        return '{"status":"success", "value":"'.$grv_main_id.'"}';
    } 

    private function calculate_average_cost($grv_detail):array{
        $grv_detail = BluelimeSystem::index_object_list_array_by_unique_field($grv_detail, 'id');
        $item_id_list = BluelimeSystem::collect_field_value_from_object($grv_detail,'id');

        $transactionModel = model('Inventory/TransactionModel');
        $latest_transaction = $transactionModel->select('MAX(id) AS id, item_id')->whereIn('item_id', $item_id_list)->orderBy('id', 'DESC')->groupBy('item_id')->findAll();
        $latest_transaction_id_list = BluelimeSystem::collect_field_value_from_object($latest_transaction,'id');
        $latest_transaction_id_list = [...$latest_transaction_id_list, -999];
        $latest_transaction_data = $transactionModel->whereIn('id', $latest_transaction_id_list)->findAll();
        $latest_transaction_data = BluelimeSystem::index_object_list_array_by_unique_field($latest_transaction_data,'item_id');
        
        [$begining_date, $ending_date] = BluelimeSystem::get_fiscal_year_range();
        $latest_quantity_list = $transactionModel->select('SUM(quantity) AS quantity, item_id')->where('created_at >=',$begining_date)->where('store_id', 1)->whereIn('item_id', $item_id_list)->groupBy('item_id')->findAll();
        $latest_quantity_list = BluelimeSystem::index_object_list_array_by_unique_field($latest_quantity_list,'item_id');
        
        $average_cost_list = [];
        foreach($grv_detail as $grv_line){
            $item_id = $grv_line['id'];
            $latest_quantity = $latest_quantity_list['item_id_'.$item_id]['quantity'] ?? 0;
            $latest_cost = $latest_transaction_data['item_id_'.$item_id]['unit_cost'] ?? 0;
            $current_cost = $grv_line['unit_price'];
            $current_quantity = $grv_line['quantity'];
            //There should be no negative value. Make every thing postive
            $latest_total_value = abs($latest_quantity * $latest_cost);
            $current_total_value = abs($current_quantity * $current_cost);
            $total_quantity = abs($latest_quantity) + abs($current_quantity);
            $average_cost_list["item_id_$item_id"]  = ($latest_total_value + $current_total_value) / $total_quantity;
            //$average_cost_list["item_id_$item_id"]  = (($latest_quantity * $latest_cost) + ($current_quantity * $current_cost)) / ($latest_quantity + $current_quantity);
        }

        return $average_cost_list;
    }
    
    public function approve_grv(){
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();

        $grv = $grvMainModel->find($grv_id);
        if($grv['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING)
            $grvMainModel->update($grv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST, 'recieved_by' => $loggeduserdata['id']]);
        return redirect()->to('/inventory/store/grv_print?grv_id='.$grv_id);
    }

    public function reverse_approved_grv(){
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');

        $grv = $grvMainModel->find($grv_id);

        if($grv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)
            $grvMainModel->update($grv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::PENDING]);
        return redirect()->to('/inventory/store/grv_print?grv_id='.$grv_id);
    }

    public function delete_grv(){
        $grv_id = $_GET['grv_id'] ?? -1;
        $store_id = 1;
        
        $grvMainModel = model('Inventory/GRVMainModel');

        $grv = $grvMainModel->find($grv_id);

        if($grv['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING){
            $grvMainModel->delete($grv_id); // Will delete related detail and also transaction
        }
        return redirect()->to('/inventory/store/grv_list');
    }

    public function grv_list(): string
    {
        $search_key = $_GET['search_key'] ?? '';
        $filter_date = $_GET['filter_date'] ?? date('Y-m-d');
        $position = $_GET['position'] ?? '';
        
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        //$store_id = $loggeduser['extra']['default_store'] ?? -1;
        $store_id = 1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grvMainModel = $grvMainModel->where('store_id', $store_id);

        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $grvMainModel = $grvMainModel->where('recieved_date <=', $filter_date);
            else
                $grvMainModel = $grvMainModel->where('recieved_date >=', $filter_date);
        }
        
        if(trim($search_key) != '')
            $grvMainModel = $grvMainModel->where('grv_code LIKE', "%$search_key%")->orWhere('invoice_no LIKE', "%$search_key%")->orWhere('fs_no LIKE', "%$search_key%");
        else
            $grvMainModel = $grvMainModel->where('status !=',0);

        $grv_main = $grvMainModel->orderBy('id','DESC')->paginate(perPage:BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        $pager = $grvMainModel->pager;

        $page = $_GET['page'] ?? 1;
        $staus_0_grv_main = [];
        if(($page == 1) && (trim($search_key) == '')){
            $staus_0_grv_main = $grvMainModel->where('status',0)->orderBy('id','DESC')->findAll();
        }

        $grv_main = [...$staus_0_grv_main, ...$grv_main];

        $suppliers = BluelimeSystem::gather_objects_by_reference($grv_main, 'supplier_id', 'Inventory/SupplierModel', 'id', true);
    
        foreach($grv_main as $index => $row){
            $grv_main[$index]['supplier_detail'] = $suppliers['id_'.$row['supplier_id']];
            // $grv_main[$index]['sorter'] = 0;
            // if($grv_main[$index]['status'] == 0)
            //     $grv_main[$index]['sorter'] = 1;
        }

        //$grv_main = BluelimeSystem::sort_array($grv_main, ['sorter','id'],true);

        $data['active_menu'] = 'grv_form';
        $data['grv_main'] = $grv_main;
        $data['search_key'] = $search_key;
        $data['filter_date'] = $filter_date;
        $data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/grv_list')
                .view('Include/_footer');     
    }

    public function grv_print(): string
    {
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');

        $grv_main = $grvMainModel->find($grv_id);
       //BluelimeSystem::show_and_die($grv_main);
        $recieved_by = $userModel->where('id',$grv_main['recieved_by']??-1)->findColumn('name');
        $store_name = $storeModel->where('id',$grv_main['store_id']??-1)->findColumn('store_name');
        
        $grv_detail = $grvDetailModel->where('grv_id', $grv_id)->findAll();

        $items = BluelimeSystem::gather_objects_by_reference($grv_detail,'item_id','Inventory/ItemModel','id',true);

        $sub_total = 0;
        $tax = 0;
        $total = 0;
        foreach($grv_detail as $index => $grv_line){
            $total_price = ($grv_line['quantity'] * $grv_line['unit_price']);
            $grv_detail[$index]['total_price'] = $total_price;
            $grv_detail[$index]['item_detail'] = $items['id_'.$grv_line['item_id']] ?? $grv_line['item_detail'];
            $sub_total += $total_price;
            $tax += $grv_line['tax'];
            $total += $total_price;
        }

        $grv_main['recieved_by'] = $recieved_by;
        $grv_main['store_name'] = $store_name;
        $grv_main['sub_total'] = $sub_total;
        $grv_main['tax'] = $tax;
        $grv_main['total'] = $total+$tax;

        $supplier = $supplierModel->find($grv_main['supplier_id']??-1);
        $grv_main['supplier_detail'] = $supplier;

        $data = [
            'grv_main' => $grv_main,
            'grv_detail' => $grv_detail,
        ];
        $data['active_menu'] = 'grv_form';
        
        return view('Inventory/Store/grv_print', $data);
    }

    public function srv_form(): string
    {
        $srvMainModel = model('Inventory/SRVMainModel');
        $itemModel = model('/Inventory/ItemModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        $transactionModel = model('Inventory/TransactionModel');
        
        $srv_main = $srvMainModel->where('is_issued' ,0)->orderBy('id','DESC')->findAll(20,0);
        foreach($srv_main as $index => $store){
            $srv_main[$index]['requesting_store'] = $storeModel->where('id',$store['requesting_store'])->findColumn('store_name')[0];
            $srv_main[$index]['requested_store'] = $storeModel->where('id',$store['requested_store'])->findColumn('store_name')[0];
        }
        foreach($srv_main as $index => $user){
            $srv_main[$index]['requested_by__name'] = $userModel->where('id',$user['requested_by'])->findColumn('name')[0];
        }
        
        $transactions = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('store_id', 1)->groupBy('item_id')->findAll();
        $items = $itemModel->findAll();
        foreach($items as $index => $item){
            $items[$index]['current_balance'] = 0;
            foreach($transactions as $transaction){
                if($transaction['item_id'] == $item['id']){
                    $items[$index]['current_balance'] = $transaction['quantity'];
                    break;
                }
            }
          
        }

        $data['active_menu'] = 'newitem';
        $data['srv_main'] = $srv_main;
        $data['items'] = $items;

        return  view('Include/_header',$data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Store/_left_sidebar_store')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Store/srv_form')
            .view('Include/_footer');     
    }

    public function srvsave()
    {
        $srv_main = $_POST['srv_main'] ?? [];
        
        $srv_detail = $_POST['srv_detail'] ?? [];
        
        $srvMainModel = model('Inventory/SRVMainModel');
        $srvDetailModel = model('Inventory/SRVDetailModel');

        $srv_main_id = $srvMainModel->insert($srv_main);
        
        $srv_detail_data = [];

        foreach($srv_detail as $srv_line)
        {
            $srv_line_temp = [
                'srv_id' => $srv_main_id,
                'item_id' => $srv_line['id'],
                'item_detail' => BluelimeSystem::convert_array_to_string(['item_code'=> $srv_line['item_code'],'item_category'=> $srv_line['item_category'],'item_generic_name'=> $srv_line['item_generic_name'],'item_brand_name'=> $srv_line['item_brand_name'],'measurement'=> $srv_line['measurement']]),
                'quantity' => $srv_line['quantity']
            ];

            array_push($srv_detail_data, $srv_line_temp);
        }

        if(!empty($srv_detail_data))
            $srvDetailModel->insertBatch($srv_detail_data);

        return '{"status":"success", "value":"'.$srv_main_id.'"}';
        
    }

    public function srv_print(): string
    {
        $srv_id = $_GET['srv_id'] ?? -1;
        $srvMainModel = model('Inventory/SRVMainModel');
        $srvDetailModel = model('Inventory/SRVDetailModel');
        $storeModel = model('Inventory/StoreModel');

        $stores = $storeModel->findAll();

        $srv_main = $srvMainModel->find($srv_id);
        $srv_detail = $srvDetailModel->where('srv_id', $srv_id)->findAll();

        foreach($stores as $store){
            if($store['id'] == $srv_main['requesting_store'])
                $srv_main['requesting_store'] = $store['store_name'];

            if($store['id'] == $srv_main['requested_store'])
                $srv_main['requested_store'] = $store['store_name'];
        }

        $data = [
            'srv_main' => $srv_main,
            'srv_detail' => $srv_detail,
            'active_menu' => 'pendingstorerequest',
        ];
        
        return view('Inventory/Store/srv_print', $data);
    }

    public function siv_form(): string
    {
        $srv_id = $_GET['srv_id'] ?? -1;
        
        $srvMainModel = model('Inventory/SRVMainModel');
        $srvDetailModel = model('Inventory/SRVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $storeModel = model('Inventory/StoreModel');

        $stores = $storeModel->findAll();

        $logged_user = BluelimeAuthentication::get_logged_user_data();

        $srv_main = $srvMainModel->find($srv_id);
        $srv_detail = $srvDetailModel->where('srv_id',$srv_id)->findAll();

        //$store_id = $logged_user['extra']['default_store'];
        $store_id = 1; 
        $default_store = [];

        foreach($stores as $store){
            if($store['id'] == $srv_main['requesting_store'])
                $srv_main['requesting_store'] = $store['store_name'];

            if($store['id'] == $srv_main['requested_store'])
                $srv_main['requested_store'] = $store['store_name'];

            if($store['id'] == $store_id)
                $default_store = $store;
        }

        $item_id_list = [-1];
        foreach($srv_detail as $row){
            array_push($item_id_list, $row['item_id']);
        }
        
        $batch_number_list = [-1];
        $item_quantity = [];
        $transaction = $transactionModel->select('item_id, batch_number,MIN(expiry_date) as expiry_date, SUM(quantity) as quantity')->where('created_at >=', FISCAL_YEAR_BEGINIG)->where('created_at <=', FISCAL_YEAR_ENDING)->where('store_id', $store_id)->whereIn('item_id', $item_id_list)->groupBy('item_id, batch_number')->having('SUM(quantity)>',0)->findAll();
        foreach($transaction as $row){
            $item_quantity['item_id_'.$row['item_id']]['batch_'.$row['batch_number']] = ['batch_number' => $row['batch_number'], 'quantity' => $row['quantity'],'expiry_date'=>(trim($row['expiry_date'])==''?'1896-03-01':$row['expiry_date'])];
            array_push($batch_number_list, $row['batch_number']);
        }

        // $grv_detail = $grvDetailModel->select('item_id, batch_number, MIN(manufactured_date) as manufactured_date, MIN(expiry_date) as expiry_date')->whereIn('item_id', $item_id_list)->whereIn('batch_number', $batch_number_list)->groupBy('item_id, batch_number')->findAll();

        // foreach($item_quantity as $index => $row){
        //     $item_id = substr($index,8);
        //     foreach($row as $index2 => $row2){
        //         $item_quantity[$index][$index2]['expiry_date'] = '1896-03-01'; //Adwa victory. Sample date
        //         $item_quantity[$index][$index2]['manufactured_date'] = '1896-03-01'; //Adwa victory. Sample date
        //         foreach($grv_detail as $grv_line){
        //             if(($grv_line['item_id'] == $item_id) && ($grv_line['batch_number'] == $row2['batch_number'])){
        //                 $item_quantity[$index][$index2]['expiry_date'] = $grv_line['expiry_date'];
        //                 $item_quantity[$index][$index2]['manufactured_date'] = $grv_line['manufactured_date'];
        //             }
        //         }
        //     }
        // }

        $item_detail_temp = BluelimeSystem::gather_objects_by_reference($srv_detail,'item_id','Inventory/ItemModel','id');

        $item_detail = [];
        foreach($item_detail_temp as $row){
            $item_detail['item_id_'.$row['id']] = $row;
        }

        $data = [
            'item_quantity' => $item_quantity,
            'item_detail'   => $item_detail,
            'srv_main' => $srv_main,
            'srv_detail' => $srv_detail,
            'default_store' => $default_store,
        ];
        $data['active_menu'] = 'pendingstorerequest';
        
        return view('Inventory/Store/siv_form', $data);
    }

    public function siv_save()
    {
        $srv_id = $_POST['srv_id'] ?? -1;
        $siv_code = $_POST['siv_code'] ?? '';
        $issue_date = $_POST['issue_date'] ?? '';
        $issue_data = $_POST['issue_data'] ?? [];

        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $srvMainModel = model('Inventory/SRVMainModel');

        // $transactionModel = model('Inventory/TransactionModel');
        // $itemModel = model('Inventory/ItemModel');
        // $grvDetailModel = model('Inventory/GRVDetailModel');
        
        $srv_main = $srvMainModel->find($srv_id);

        if(empty($srv_main))
            return BluelimeSystem::get_error_ajax_return("SRV notfound");

        $items = BluelimeSystem::gather_objects_by_reference($issue_data, 'item_id', 'Inventory/ItemModel', 'id', true);
        
        //$item_id_list = [-99,...BluelimeSystem::collect_field_value_from_object($issue_data, 'item_id')];
        //$grv_details = $grvDetailModel->select('MIN(expiry_date) as expiry_date, item_id, batch_number')->whereIn('item_id', $item_id_list)->groupBy('item_id, batch_number')->findAll();

        $siv_code = ($sivMainModel->orderBy('id','DESC')->findColumn('id')[0] ?? 0)+1;
        $siv_main_data = [
            'srv_id' => $srv_id,
            'siv_code' => $siv_code,
            'issue_date' => $issue_date,
            'issued_by' => $logged_user['id'],
            'issuing_store' => $srv_main['requested_store'],
            'requesting_store' => $srv_main['requesting_store']
        ];


        $siv_detail_data = [];
        //$transaction_data = [];

        foreach($issue_data as $row)
        {
            $item_detail = $items['id_'.$row['item_id']] ?? [];
            $batch_quantity = $row['batch_quantity'] ?? [];
            
            foreach($batch_quantity as $batch_number => $qty){
                [$unit_price, $unit_cost, $grv_id, $expiry_date] = $this->get_unit_cost_selling_price($row['item_id'], $batch_number);

                $siv_detail_data_temp = [
                    'siv_id' => -1, //This will be updated later
                    'item_id' => $row['item_id'],
                    'item_detail' => BluelimeSystem::convert_array_to_string($item_detail),
                    'quantity' => $qty,
                    'batch_no' => $batch_number,
                    'grv_id' => $grv_id,
                    'unit_cost' => $unit_cost,
                    'unit_price' => $unit_price,
                    'expiry_date' => $expiry_date,
                ];
                array_push($siv_detail_data, $siv_detail_data_temp);
            }
        }
        
        if(empty($siv_detail_data))
            return BluelimeSystem::get_error_ajax_return("SIV detail cannot be empty");

        $average_cost_list = $this->get_siv_issued_items_latest_average_cost($siv_detail_data);

        //TODO : Check if there is zero average cost and return error to store to create average cost in transaction

        $siv_id = $sivMainModel->insert($siv_main_data);
        foreach($siv_detail_data as $index => $row){
            $siv_detail_data[$index]['siv_id'] = $siv_id; //Update the siv_id
            $siv_detail_data[$index]['average_cost'] = $average_cost_list['item_id_'.$row['item_id']];
        }

        $sivDetailModel->insertBatch($siv_detail_data);
       
        //$transactionModel->insertBatch($transaction_data);
        $srv_main_data = ['is_issued'=>1];
        $srvMainModel->update($srv_id, $srv_main_data);
        
        return BluelimeSystem::get_success_ajax_return(['siv_id' => $siv_id]);
    }

    private function get_siv_issued_items_latest_average_cost($siv_details){
        $siv_details = BluelimeSystem::index_object_list_array_by_unique_field($siv_details,'item_id');
        $item_id_list = BluelimeSystem::collect_field_value_from_object($siv_details,'item_id');
        $transactionModel = model('Inventory/TransactionModel');
        $latest_transaction = $transactionModel->select('MAX(id) AS id, item_id')->whereIn('item_id', $item_id_list)->where('unit_cost >',0)->groupBy('item_id')->findAll();
        $latest_transaction_id_list = BluelimeSystem::collect_field_value_from_object($latest_transaction,'id');
        $latest_transaction_data = $transactionModel->whereIn('id', $latest_transaction_id_list)->findAll();
        $latest_transaction_data = BluelimeSystem::index_object_list_array_by_unique_field($latest_transaction_data,'item_id');

        $average_cost_list = [];
        foreach($siv_details as $siv_line){
            $item_id = $siv_line['item_id'];
            $average_cost_list["item_id_$item_id"]  = $latest_transaction_data['item_id_'.$item_id]['unit_cost'] ?? 0;
        }

        $grvDetailModel = model('Inventory/GRVDetailModel');
        foreach($average_cost_list as $index => &$average_cost){
            if($average_cost == 0){
                $item_id = str_replace('item_id_','',$index);
                $grv_detail = $grvDetailModel->where('id', $siv_details['item_id_'.$item_id]['grv_id'])->where('item_id', $item_id)->first();
                if(empty($grv_detail)){
                    $grv_detail = $grvDetailModel->where('item_id', $item_id)->orderBy('id', 'DESC')->first();
                }
                
                $average_cost = $grv_detail['unit_price'] ?? 0;
            }
        }

        return $average_cost_list;
    }

    public function approve_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();

        $siv = $sivMainModel->find($siv_id);
        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING)
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST, 'issued_by' => $loggeduserdata['id']]);
        return redirect()->to('/inventory/store/siv_print?siv_id='.$siv_id);
    }

    public function reverse_approved_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');

        $siv = $sivMainModel->find($siv_id);

        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::PENDING]);
        return redirect()->to('/inventory/store/siv_print?siv_id='.$siv_id);
    }

    public function delete_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        
        $sivMainModel = model('Inventory/SIVMainModel');

        $siv = $sivMainModel->find($siv_id);

        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING){
            $sivMainModel->delete($siv_id); //This will delete all related details transaction also
        }
        return redirect()->to('/inventory/store/');
    }

    private function get_unit_cost_selling_price(int $item_id, string $batch_number): array{
        $gRVDetailModel = model('Inventory/GRVDetailModel');
        $priceListModel = model('Inventory/PriceListModel');
        //$transactionModel = model('Inventory/TransactionModel');

        $price_line = $priceListModel->where('item_id', $item_id)->where('effective_date <=', date('Y-m-d'))->orderBy('effective_date','DESC')->first();
        $unit_price = $price_line['unit_price'] ?? 0;

        $grv_detail = $gRVDetailModel->where('item_id', $item_id)->where('batch_number', $batch_number)->orderBy('created_at', 'DESC')->first();
        
        // Avagare cost will not be used for SIV as adjustment.
        /*$last_transaction = $transactionModel->where('item_id', $item_id)->orderBy('created_at', 'DESC')->first();
            $unit_cost = $last_transaction['unit_cost'] ?? 0;

            if($unit_cost == 0){
                $unit_cost = $grv_detail['unit_price'] ?? 0;
            }
        */

        $unit_cost = $grv_detail['unit_price'] ?? 0;
        if($unit_cost == 0){
            $grv_detail = $gRVDetailModel->where('item_id', $item_id)->orderBy('created_at', 'DESC')->first();
            $unit_cost = $grv_detail['unit_price'] ?? 0;
            if(!empty($grv_detail)){
                $grv_detail['grv_id'] .= ' (Last GRV)';
            }
        }
        $grv_id = $grv_detail['grv_id'] ?? '';
        $expiry_date = $grv_detail['expiry_date'] ?? '';
        return [$unit_price, $unit_cost, $grv_id, $expiry_date];
    }

    public function siv_list(): string
    {
        $search_key = $_GET['search_key'] ?? '';
        $filter_date = $_GET['filter_date'] ?? date('Y-m-d');
        $position = $_GET['position'] ?? '';

        $sivMainModel = model('Inventory/SIVMainModel');
        $storeModel = model('Inventory/StoreModel');
        
        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $sivMainModel = $sivMainModel->where('created_at <=', $filter_date);
            else
                $sivMainModel = $sivMainModel->where('created_at >=', $filter_date);
        }

        if(trim($search_key) != '')
            $sivMainModel = $sivMainModel->groupStart()->where('srv_id LIKE', "%$search_key%")->orWhere('id LIKE', "%$search_key%")->orWhere('description LIKE', "%$search_key%")->groupEnd();

        $siv_main = $sivMainModel->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        $pager = $sivMainModel->pager;

        $stores = $storeModel->findAll();
        
        foreach($siv_main as $index => $siv){
            foreach($stores as $store){
                if($store['id'] == $siv['requesting_store']){
                    $siv_main[$index]['store_name'] = $store['store_name'];
                    break;
                }
            }
            foreach($stores as $store){
                if($store['id'] == $siv['issuing_store']){
                    $siv_main[$index]['issuing_store'] = $store['store_name'];
                    break;
                }
            }
        }

        $data['active_menu'] = 'pendingsiv';
        $data['siv_main'] = $siv_main;
        $data['search_key'] = $search_key;
        $data['filter_date'] = $filter_date;
        $data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/siv_list')
                .view('Include/_footer');     
    }

    public function siv_print(): string
    {
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');

        $siv_main = $sivMainModel->find($siv_id);

        $issuing_store_name = $storeModel->where('id',$siv_main['issuing_store'])->findColumn('store_name');
        $requesting_store_name = $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name');
        
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();

        $issued_by = $userModel->where('id',$siv_main['issued_by'])->findColumn('name');
        $received_by = $userModel->where('id',$siv_main['recieved_by'])->findColumn('name');
        $siv_main['issued_by_name'] = $issued_by[0] ?? '';
        $siv_main['received_by_name'] = $received_by[0] ?? '';
        
        $siv_main['issuing_store_name'] = $issuing_store_name[0];
        $siv_main['requesting_store_name'] = $requesting_store_name[0];
        $data = [
            'siv_main' => $siv_main,
            'siv_detail' => $siv_detail,
        ];

        $data['active_menu'] = 'pendingstorerequest';
        
        return view('Inventory/Store/siv_print', $data);
    }

    public function addstorerequest()
    {
        $srvMainModel = model('Inventory/SRVMainModel');
        $srvDetailModel = model('Inventory/SRVDetailModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];
        // BluelimeSystem::show_and_die($_POST);
        
        $srv_code    = $_POST['srv_code'];
        $request_date    = $_POST['request_date'];
        $description    = $_POST['description'];
        $requested_by    = $logged_user;
        $requesting_store            = $_POST['requesting_store'];
        $requested_store            = $_POST['requested_store'];
        $item_id            = $_POST['item_id'];
        $average_cost            = $_POST['average_cost'];
        $quantity            = $_POST['quantity'];

                $data = [
                'srv_code' => $srv_code,
                'request_date' => $request_date,
                'description' => $description,
                'requested_by' => $requested_by,  
                'requesting_store' => $requesting_store, 
                'requested_store' => $requested_store
                ];

                $id=$srvMainModel->insert($data,true);
                for($i=0; $i<count( $_POST['srv_code']); $i++) { 
                    

                $data_detail= array(
                'srv_id' => $id, 
                'item_id' => $item_id,  
                'average_cost' => $average_cost,
                'quantity' => $quantity
                );

                $srvDetailModel->insert($data_detail,true);
                
                }

            return redirect()->to(site_url('/inventory/store/storerequest'));
    }

    
    public function stock_card()
    {
        $item_id = $_GET['stock_item_id'];
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        
        $store_id = 1;
        $stock_cards = BluelimeSystem::get_stock_cards($item_id, $store_id);
        $selected_item = $itemModel->find($item_id);
        $peachtree_items = $peachtreeItemModel->where('item_id',$item_id)->findAll(1);
        $peachtree_id = $peachtree_items[0]['peachtree_id'];

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_ingegratoin = $finance_maintain[0]['data'];
        $peachtree_id .= '_'.$peachtree_ingegratoin['code_suffix']['store'];
        $selected_item['peachtree_id'] = $peachtree_id;

        $store_title = 'Main Store #'.$peachtree_ingegratoin['branch']['number'].' ('.$peachtree_ingegratoin['branch']['name'].')';

        $data['stock_cards'] = $stock_cards;
        $data['selected_item'] = $selected_item;
        $data['store_title'] = $store_title;
        $data['active_menu'] = 'inventory';
       
        return view('Inventory/Store/stock_card', $data);
    }

    public function purchase_form(): string
    {
        $logged_user_data = BluelimeAuthentication::get_logged_user_data();
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $itemModel = model('Inventory/ItemModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');

        $last_purchase_id = $purchaseMainModel->select('MAX(id) as id')->findAll()[0]['id'] ?? 0;
        $items = $itemModel->findAll();
        $suppliers = $supplierModel->findAll();
        $store = $storeModel->find($logged_user_data['extra']['default_store'] ?? 0);
        $default_store = $store ?? ['store_name' => '', 'location'=>''];
        $item_j = [];
        foreach($items as $item)
            $item_j['item_id_'.$item['id']] = $item;

        $data['active_menu'] = 'purchase_form';
        $data['items'] = $item_j;
        $data['suppliers'] = $suppliers;
        $data['last_purchase_id'] = $last_purchase_id;
        $data['logged_user_data'] = $logged_user_data;
        $data['default_store'] = $default_store;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/purchase_form')
                .view('Include/_footer');     
    }

    public function purchasesave()
    {
        $purchase_main = $_POST['purchase_main'] ?? [];
        $purchase_detail = $_POST['purchase_detail'] ?? [];

        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $purchaseDetailModel = model('Inventory/PurchaseDetailModel');

        $purchase_main_id = $purchaseMainModel->insert($purchase_main);

        $purchase_detail_data = [];

        foreach($purchase_detail as $purchase_line)
        {
            $purchase_line_temp = [
                'purchase_id' => $purchase_main_id,
                'item_id' => $purchase_line['id'],
                'item_detail' => BluelimeSystem::convert_array_to_string(['item_code'=> $purchase_line['item_code'],'item_category'=> $purchase_line['item_category'],'item_generic_name'=> $purchase_line['item_generic_name'],'item_brand_name'=> $purchase_line['item_brand_name'],'measurement'=> $purchase_line['measurement']]),
                'quantity' => $purchase_line['quantity'],
                
            ];

            

            array_push($purchase_detail_data, $purchase_line_temp);
        }

        if(!empty($purchase_detail_data))
            $purchaseDetailModel->insertBatch($purchase_detail_data);


        return '{"status":"success", "value":"'.$purchase_main_id.'"}';
    } 
    
    public function purchase_list(): string
    {
        $search_key = $_GET['search_key'] ?? '';

        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        
        if(trim($search_key) != '')
            $purchaseMainModel = $purchaseMainModel->where('purchase_code LIKE', "%$search_key%")->orWhere('invoice_no LIKE', "%$search_key%")->orWhere('fs_no LIKE', "%$search_key%");

        $purchase_main = $purchaseMainModel->orderBy('id','DESC')->findAll(20,0);

        $data['active_menu'] = 'purchase_form';
        $data['purchase_main'] = $purchase_main;
        $data['search_key'] = $search_key;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/purchase_list')
                .view('Include/_footer');     
    }

    public function purchase_print(): string
    {
        $purchase_id = $_GET['purchase_id'] ?? -1;
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $purchaseDetailModel = model('Inventory/PurchaseDetailModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');


        $purchase_main = $purchaseMainModel->find($purchase_id);
        $purchase_detail = $purchaseDetailModel->where('purchase_id', $purchase_id)->findAll();
        $items = BluelimeSystem::gather_objects_by_reference($purchase_detail,'item_id','Inventory/ItemModel','id',true);

        $store_name = $storeModel->where('id',$purchase_main['store_id'])->findColumn('store_name')[0];
        $requested_by = $userModel->where('id',$purchase_main['requested_by'])->findColumn('name');

        

        $supplier = $supplierModel->find($purchase_main['supplier_id']);

        foreach($purchase_detail as $index => $purchase_line){
            $purchase_detail[$index]['item_detail'] = $items['id_'.$purchase_line['item_id']] ?? $purchase_line['item_detail'];
        }
        $purchase_main['supplier_detail'] = $supplier;
        $purchase_main['store_name'] = $store_name;
        $purchase_main['requested_by'] = $requested_by;

        $data = [
            'purchase_main' => $purchase_main,
            'purchase_detail' => $purchase_detail,
        ];
        $data['active_menu'] = 'purchase_form';
        
        return view('Inventory/Store/purchase_print', $data);
    }

    public function pricesetup_form()
    {
        $priceListModel = model('/Inventory/PriceListModel');
        $itemModel = model('Inventory/ItemModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        //$store_id = $loggeduser['extra']['default_store'] ?? -1;
        $store_id = 2;

        $search_item = $_REQUEST['search_key'] ?? '';
        $search_item = trim($search_item);

        // $today = date('Y-m-d').'%';
        // if(trim($search_item) != '')
        //     $itemModel = $itemModel->where('item_code LIKE', "%$search_item%")->orWhere('item_generic_name LIKE', "%$search_item%")->orWhere('item_brand_name LIKE', "%$search_item%");

        $items = $itemModel->orderBy('id','DESC')->findAll();
        //$price_lists = $priceListModel->where('effective_date <= ',$today)->orderBy('id','DESC')->findAll();
        $grv_list = $grvDetailModel->orderBy('created_at','DESC')->findAll();

        $items = BluelimeSystem::get_all_items_with_price($store_id);
        //$pager = $itemModel->pager;
        
        $cnt = 0;
        $collected_items = [];
        foreach($items as $index => $item){

            if(!empty($search_item)){
                $search_item = strtolower($search_item);
                if(!BluelimeSystem::search_in_fields($search_item, $item, ['item_code', 'item_generic_name', 'item_brand_name'])){
                    unset($items[$index]);
                    continue;
                }
            }

            $items[$index]['last_unit_cost'] = 0;
            foreach($grv_list as $grv){
                if($grv['item_id'] == $item['id']){
                    $items[$index]['last_unit_cost'] = $grv['unit_price'];
                    break;
                }
            }

            $collected_items[$index] =  $items[$index];

            if($cnt++ > BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST)
               break;
            
            // $items[$index]['unit_price'] = 0;
            // foreach($price_lists as $price_list){
            //     if($price_list['item_id'] == $item['id']){
            //         $items[$index]['unit_price'] = $price_list['unit_price'];
            //         break;
            //     }
            // }
            
            
        }
        
        /*$items_j = [];
        foreach($items as $index => $item){
            $items_j['item_id_'.$item['id']] = $item;
        }*/

        //$data['pager'] = $pager;
        //$data['page'] = $pager->getCurrentPage()??1;
        
        $data['active_menu'] = 'pricesetup';
        $data['items'] = $collected_items;
        $data['search_key'] = $search_item;
        
        return  view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view(($loggeduser['role'] == BluelimeConfiguration::USER_ROLE::DISPENSARY) ? 'Inventory/Dispensary/_left_sidebar_dispensary':'Inventory/Store/_left_sidebar_store')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Store/pricesetup_form')
                .view('Include/_footer');    
    }

    public function pricelistsave()
    {
        $item_price_list = $_POST['item_price_list'] ?? [];
        $effective_date = $_POST['effective_date'] ?? date('Y-m-d');
    
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        $store_id = 2;
        //BluelimeSystem::print_associated_array($item_price_list,false);

        $priceListModel = model('Inventory/PriceListModel');

        $item_price_list = BluelimeSystem::convert_to_array($item_price_list);
        if(empty($item_price_list))
            return BluelimeSystem::get_success_ajax_return('Price List Not Saved!');

        foreach($item_price_list as $index => $row){
            $row['store_id'] = $store_id;
            $row['registered_by'] = $loggeduser['id'];
            $row['effective_date'] = $effective_date;
            $item_price_list[$index] = $row;
        }

        $priceListModel->insertBatch($item_price_list);

        //BluelimeSystem::write_log('neba','price_list',BluelimeSystem::convert_array_to_string($item_price_list),'pricelistsave');
        
        return BluelimeSystem::get_success_ajax_return('Price List Saved successfully');
    }

    public function inventory(){
        $show_zero = $_GET['show_zero'] ?? 0;
        $items = BluelimeSystem::get_all_items_quantity(1);
        //$items = BluelimeSystem::get_all_items_with_price_and_quantity(1,true);

        $inventory_data = [];
        foreach($items as $item){
            if(($item['current_balance'] == 0) && !$show_zero)
                continue;

            $temp = [
                'id' => $item['id'],
                'item_code' => $item['item_code'],
                'item_generic_name' => $item['item_generic_name'],
                'item_brand_name' => $item['item_brand_name'],
                'preparation' => $item['preparation'],
                'strength' => $item['strength'],
                //'unit_price' => $item['unit_price'],
                'current_balance' => $item['current_balance']
            ];
            array_push($inventory_data, $temp);
        }

        //$inventory_data = BluelimeSystem::sort_array($inventory_data, ['item_brand_name','item_generic_name']);

        $data = [
            'inventory_data'   => $inventory_data,
            'active_menu' => 'inventory'
        ];
        return view('Inventory/Store/inventory', $data);
    }

    public function inventory_location(){
        $itemModel = model('Inventory/ItemModel');
        $items = $itemModel->findAll();
        $data = [
            'items' => $items,
            'active_menu' => 'inventory_count',
            'active_sub_menu' => 'inventory_location',
        ];
        return view('Inventory/Store/inventory_location', $data);
    }

    public function save_inventory_location(){
        $item_id = $_POST['item_id'];
        $inventory_location = $_POST['inventory_location'];
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $store_id = '1'; //$logged_user['extra']['default_store'];

        $itemLocationModel = model('Inventory/ItemLocationModel');

        $inventory_location_data = [
            'item_id' => $item_id,
            'location' => $inventory_location,
            'store_id' => $store_id,
            'registered_by' => $logged_user['id'],
            'registered_by_name' => $logged_user['name']
        ];

        $id = $itemLocationModel->insert($inventory_location_data);
        if($id > 0){
            $itemLocationModel->where('item_id',$item_id)->where('id <', $id)->where('store_id',$store_id)->delete();
            return BluelimeSystem::get_success_ajax_return('Inventory Location Saved Successfully');
        }

        return BluelimeSystem::get_error_ajax_return('Inventory Location Not Saved!');
    }

    public function inventory_count_print(){
        $store_id = 1; //$_GET['store_id'] ?? 1;
        $itemLocationModel = model('Inventory/ItemLocationModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $gRVDetailModel = model('Inventory/GRVDetailModel');

        $peachtree_integration = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findColumn('data');
        $peachtree_integration = $peachtree_integration[0];

        $item_locations = $itemLocationModel->where('store_id',$store_id)->findAll();
        
        $items_with_price_and_qty = BluelimeSystem::get_all_items_with_price_and_quantity($store_id,true);
        $items_with_price_and_qty = BluelimeSystem::index_object_list_array_by_unique_field($items_with_price_and_qty,'id');

        $item_expirys = BluelimeSystem::get_available_item_batch_expiry_date($store_id);
        $grv_detail_max_grv_id = $gRVDetailModel->select('item_id, MAX(grv_id) as grv_id')->join('inventory_grv_main','inventory_grv_main.id = inventory_grv_detail.grv_id')->where('inventory_grv_main.status >', 0)->groupBy('item_id')->findAll();
        $grv_details = $gRVDetailModel->select('item_id, MAX(unit_price) as unit_cost, MAX(tax) as vat')->whereIn('grv_id', array_column($grv_detail_max_grv_id, 'grv_id'))->groupBy('item_id')->findAll();
        $grv_details = BluelimeSystem::index_object_list_array_by_unique_field($grv_details, 'item_id');

        $items= [];
        foreach($item_locations as $item_location){
            $items_with_price_and_qty['id_'.$item_location['item_id']]['location'] = $item_location['location'];
            $items_with_price_and_qty['id_'.$item_location['item_id']]['expiry_date'] = '';
            foreach($item_expirys as $item_expiry){
                if($item_expiry['item_id'] == $item_location['item_id']){
                    $items_with_price_and_qty['id_'.$item_location['item_id']]['expiry_date'] = $item_expiry['expiry_date'];
                    break;
                }
            }
            $items_with_price_and_qty['id_'.$item_location['item_id']]['unit_cost'] = $grv_details['item_id_'.$item_location['item_id']]['unit_cost'] ?? 0;
            if(($grv_details['item_id_'.$item_location['item_id']]['vat']??0) >0 )
                $items_with_price_and_qty['id_'.$item_location['item_id']]['unit_cost'] *= 1.15;
            
            $items[] = $items_with_price_and_qty['id_'.$item_location['item_id']];
        }

        $items = BluelimeSystem::sort_array($items, ['location']);
        $data = [
            'item_expirys' => $item_expirys,
            'items_with_price_and_qty' => $items_with_price_and_qty,
            'branch_number' => $peachtree_integration['branch']['number'],
            'store_name' => 'Store',
            'items' => $items,
            'active_menu' => 'inventory_count',
            'active_sub_menu' => 'inventory_count_print',
        ];

        return view('Inventory/Store/inventory_count_print', $data);
    }

    public function inventory_item_with_no_location(){
        $store_id = 1; //$_GET['store_id'] ?? 1;
        $itemLocationModel = model('Inventory/ItemLocationModel');
        $itemModel = model('Inventory/ItemModel');
        $transactionModel = model('Inventory/TransactionModel');

        [$begining, $ending] = BluelimeSystem::get_fiscal_year_range(date('Y-m-d'));

        $available_items = $transactionModel->select('item_id, SUM(quantity) as quantity')
                                ->where('store_id', $store_id)
                                ->where('created_at >=', $begining)->where('created_at <=', $ending)->groupBy('item_id')->having('SUM(quantity) !=',0)->findAll();
        
        $item_locations = $itemLocationModel->where('store_id',$store_id)->findAll();

        $no_location_item_id_list = [];
        foreach($available_items as $index => $avalable_item){
            $found = false;
            foreach($item_locations as $item_location){
                if($item_location['item_id'] == $avalable_item['item_id']){
                    $found = true;
                    break;
                }
            }

            if($found === false)
                $no_location_item_id_list[] = $avalable_item['item_id'];
        }

        //$item_id_list = BluelimeSystem::collect_field_value_from_object($available_items, 'item_id');

        $items = $itemModel->whereIn('id', [...$no_location_item_id_list,-99])->findAll();
        $available_items = BluelimeSystem::index_object_list_array_by_unique_field($available_items, 'item_id');
        foreach($items as &$item){
            $item['quantity'] = $available_items['item_id_'.$item['id']]['quantity'];
        }

        $data = [
            'items' => $items,
            'active_menu' => 'inventory_count',
            'active_sub_menu' => 'inventory_item_with_no_location'
            ];

        return view('Inventory/Store/inventory_item_with_no_location', $data);
    }


    public function get_last_mrc_number(){
        $supplier_id = $_GET['supplier_id'] ?? -1;
        $gRVMainModel = model('Inventory/GRVMainModel');

        $grv_main = $gRVMainModel->where('supplier_id',$supplier_id)->where('machine_no IS NOT NULL')->orderBy('id','DESC')->findAll(1);
        $machine_no = $grv_main[0]['machine_no'] ?? '';

        if(empty($machine_no))
            return BluelimeSystem::get_error_ajax_return('Machine No Not Found!');
        return BluelimeSystem::get_success_ajax_return($machine_no);
    }
}