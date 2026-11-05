<?php

namespace App\Controllers\Inventory;

use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;

class Compounding extends \App\Controllers\BaseController
{
    public function index()
    {

    }

    public function list()
    {
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        
        $compounding_prescriptions = $compoundingPrescriptionMainModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'compounding_prescriptions' => $compounding_prescriptions,
            'active_menu' => 'compounding'
        ];
        return view('Inventory/Compounding/list', $data);
    }

    public function prepare(){
        $store_id = 2; //$loggeduser['extra']['default_store'] ?? -1; //Get from logged user default store

        $com_pre_id = $_GET['id'] ?? -1;

        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $compoundingPrescriptionDetailModel = model('Hospital/CompoundingPrescriptionDetailModel');
        $itemModel = model('Inventory/ItemModel');

        $com_pre_main = $compoundingPrescriptionMainModel->find($com_pre_id);
        $com_pre_detail = $compoundingPrescriptionDetailModel->where('main_id', $com_pre_id)->findAll();
        
        $prescription_group_id = $com_pre_main['prescription_group_id'] ?? -1;
        $com_pre_friends = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->findAll();

        $compounding_drugs = $itemModel->where('compounding_volume <>', '')->orderBy('item_generic_name')->orderBy('item_brand_name')->findAll();
        $item_with_price = BluelimeSystem::get_all_items_with_price_and_quantity($store_id);
        $compounding_items_qty = BluelimeSystem::get_all_items_quantity(3);
        // BluelimeSystem::print_associated_array($compounding_items_qty);
        // $compounding_items_qty = BluelimeSystem::index_object_list_array_by_unique_field($compounding_items_qty,'item_id');

        //BluelimeSystem::show_and_die($item_with_price);
        $compounding_drugs_with_price = [];
        foreach($compounding_drugs as $index => $drug){
            foreach($item_with_price as $index2 => $item){
                if($item['id'] == $drug['id']){
                    //$compounding_drugs[$index] = $item;
                    $compounding_drugs_with_price[$index2] = $item;
                    $compounding_drugs_with_price[$index2]['current_balance'] = $compounding_items_qty['item_id_'.$item['id']]['current_balance']??0;
                    unset($item_with_price[$index2]);
                    break;
                }
            }
        }

        $number_of_sales = BluelimeSystem::count_my_unsent_inventory_sales();
        $data = [
            'active_menu' => 'compounding',
            'items' => $compounding_drugs_with_price,
            'number_of_sales' => $number_of_sales,
            'com_pre_main' => $com_pre_main,
            'com_pre_detail' => $com_pre_detail,
            'com_pre_friends' => $com_pre_friends,
            'com_pre_id' => $com_pre_id
        ];

        return view('Inventory/Compounding/prepare', $data);
    }

    public function save_in_cart(){
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
          
        $compounding_list = $_POST['compounding_list'];
        $sales_id = $_POST['sales_id'] ?? 0;
        $prescription_id = $_POST['prescription_id'] ?? 0;

        $store_id = 3;
        $logged_user = BluelimeAuthentication::get_logged_user_data();

        $sales_main = [
            'prescription_id' => $prescription_id,
            'sales_type' => BluelimeConfiguration::SALES_TYPE::COMPOUNDING,
            'store_id' => $store_id,
            'registered_by' => $logged_user['id'],
            'visit_id' => 0,
            'is_recieved' => 0
        ];

        $this_comp_pre = $compoundingPrescriptionMainModel->find($prescription_id);

        if(!empty($this_comp_pre)){
            $sales_main['patient_id'] = $this_comp_pre['patient_detail']['id'] ?? 0;
            $sales_main['patient_name'] = $this_comp_pre['patient_detail']['name'] ?? 0;
        }

        if($sales_id <= 0){
            $sales_id = $salesMainModel->insert($sales_main);
            $salesDetailModel->where('sales_id', $sales_id)->delete();
        }

        $items = BluelimeSystem::gather_objects_by_reference($compounding_list, 'item_id', 'Inventory/ItemModel','id');

        $sales_detail = [];

        foreach($compounding_list as $line){
            $item_id = intval($line['item_id']);
            $item_detail = $items['item_id_'.$line['item_id']] ?? $line['item_detail'];
            $temp = [
                'sales_id' => $sales_id,
                'item_id' => $item_id,
                'item_detail' => BluelimeSystem::convert_array_to_string($item_detail),
                'quantity' => $line['amount'],
                'unit_price' => $item_detail['compounding_unit_price'],
                'vat' => ($item_detail['is_taxable'])?$item_detail['compounding_unit_price']*0.15:0,
                'measurement' => $item_detail['compounding_measurement']
            ];

            array_push($sales_detail, $temp);
        }

        $salesDetailModel->insertBatch($sales_detail);

        $compoundingPrescriptionMainModel->update($prescription_id, ['is_sold' => 1]);
        $prescription_group_id = $this_comp_pre['prescription_group_id'] ?? -1;
        $next_comp = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->findAll();
        $next_comp_id = $next_comp[0]['id'] ?? 0;

        return BluelimeSystem::get_success_ajax_return($next_comp_id);
    }

    public function prepare_OLD()
    {
        $id = $_GET['id'] ?? -1;

        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $compoundingPrescriptionDetailModel = model('Hospital/CompoundingPrescriptionDetailModel');

        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $store_id = 3; //$loggeduser['extra']['default_store'] ?? -1; //Get from logged user default store

        $items = BluelimeSystem::get_all_items_with_price_and_quantity(2);

        $prescription_main = $compoundingPrescriptionMainModel->find($id);
        $prescription_detail = $compoundingPrescriptionDetailModel->where('main_id', $id)->findAll();

        $patient_id = (!$prescription_main['is_external']) ? $prescription_main['patient_detail']['patient_id'] : 0;
        $patient_name = (!$prescription_main['is_external']) ? $prescription_main['patient_detail']['patient_name'] : $prescription_main['external_detail']['patient_name'];
        $mobile = (!$prescription_main['is_external']) ? $prescription_main['patient_detail']['patient_mobile'] : $prescription_main['external_detail']['patient_phone'];
        $doctor_name = (!$prescription_main['is_external']) ? $prescription_main['created_by_name'] : $prescription_main['external_detail']['doctor_name'].' ('.$prescription_main['external_detail']['hospital_name'].')';

        $compounding_prescription = [
            'id' => $id,
            'patient_name' => $patient_name,
            'mobile' => $mobile,
            'patient_id' => $patient_id,
            'doctor_name' => $doctor_name,
            'prescriptions' => $prescription_detail,
        ];

        $prescriptions = $compounding_prescription['prescriptions'];
        
        if(empty($prescriptions))
            $prescriptions = [];

        $total_amount = 0;
        foreach($prescriptions as $row){
            if(floatval($row['in_percent']) == 0)
                $total_amount += floatval($row['amount']);
        }

        foreach($prescriptions as $index => $row){
            if(floatval($row['in_percent']) > 0)
                $prescriptions[$index]['amount'] = ($total_amount * floatval($row['in_percent']) / 100);
        }

        $compounding_prescription['prescriptions'] = $prescriptions;

        $data = [
            'compounding_prescription' => $compounding_prescription,
            'items' => $items,
            'active_menu' => 'compounding'
        ];
        return view('Inventory/Compounding/prepare', $data);
    }

    public function sale_compounding(){
        $sales_item_list = $_POST['sales_item_list'] ?? '';
        $compounding_prescription_id = $_POST['compounding_prescription_id'] ?? -1;
        $patient_detail = $_POST['patient_detail'] ?? '{"patient_id":"-1", "name":"", "mobile":""}';
        
        //BluelimeSystem::write_log('abiy_test','compounding',$sales_item_list);

        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        $store_id = 3; //$loggeduser['extra']['default_store'];

        $sales_item_list = BluelimeSystem::convert_to_array($sales_item_list);
        $patient_detail = BluelimeSystem::convert_to_array($patient_detail);
        //$item_id_list = BluelimeSystem::collect_field_value_from_object($sales_item_list,'item_id');

        if(empty($item_id_list))
            $item_id_list = [-1];

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $transactionModel = model('Inventory/TransactionModel');

        $sales_main = [
            'patient_id' => $patient_detail['patient_id'],
            'patient_name' => $patient_detail['name'],
            'prescription_id' => $compounding_prescription_id,
            'sales_type' => BluelimeConfiguration::SALES_TYPE::COMPOUNDING,
            'store_id' => $store_id,
            'registered_by' => $loggeduser['id'],
        ];

        $items = BluelimeSystem::gather_objects_by_reference($sales_item_list, 'item_id', 'Inventory/ItemModel','id',true);

        $sales_id = $salesMainModel->insert($sales_main);

        $transaction_data = [];
        $sales_detail = [];
        $none_vat_items_price = 0;
        $vat_items_price = 0;
        foreach($sales_item_list as $row){
            if($row['amount'] <= 0)
                continue;

            $sales_detail_line = [
                'sales_id' => $sales_id,
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'],
                'quantity' => $row['amount'],
                'unit_price' => $row['price'] / $row['amount'],
                'vat' => $row['vat'],
            ];

            if($row['vat'] > 0)
                $vat_items_price += $row['price'];
            else
                $none_vat_items_price += $row['price'];

            if(substr(strval($row['item_id']),0,6) != 'GRADE_'){
                $item_detail = BluelimeSystem::convert_array_to_string([  'item_category'=> $items['id_'.$row['item_id']]['item_category'],
                                                                            'item_generic_name'=> $items['id_'.$row['item_id']]['item_generic_name'],
                                                                            'item_brand_name'=> $items['id_'.$row['item_id']]['item_brand_name'],
                                                                            'measurement'=> $items['id_'.$row['item_id']]['measurement']]);
                                                                
                $sales_detail_line['item_detail'] = $item_detail;
                array_push($sales_detail, $sales_detail_line);
                

                $transaction_row = [
                    'store_id' => $store_id,
                    'item_id' => $row['item_id'],
                    'transaction_type' => BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::COMPOUNDING_SALES,
                    'transaction_reference' => $compounding_prescription_id,
                    'quantity' => -1 * $row['amount']
                ];
                array_push($transaction_data, $transaction_row);
            }
        }

        $pos_bill_salses_line = [];
        $pos_bill_salses_line_temp = [
            'Item_ID' => 1,
            'Item_Description' => '',
            'Item_Quantity' => 1,
            'Item_UOM' => 'PCS',
            'Item_Unit_Price' => 0,
            'Item_Tax_Percent' => 0,
            'Item_DiscOrAdd_Amount' => 0,
            'Store' => '',
            'Line_Item_Type' => 'DRUG'
        ];
        if($none_vat_items_price > 0){
            $pos_bill_salses_line_temp['Item_ID'] = 1;
            $pos_bill_salses_line_temp['Item_Description'] = 'None taxable compounding';
            $pos_bill_salses_line_temp['Item_Unit_Price'] = $none_vat_items_price;
            $pos_bill_salses_line_temp['Item_Tax_Percent'] = 0;
            array_push($pos_bill_salses_line, $pos_bill_salses_line_temp);
        }

        if($vat_items_price > 0){
            $pos_bill_salses_line_temp['Item_ID'] = 2;
            $pos_bill_salses_line_temp['Item_Description'] = 'Taxable compounding';
            $pos_bill_salses_line_temp['Item_Unit_Price'] = $vat_items_price;
            $pos_bill_salses_line_temp['Item_Tax_Percent'] = 15;
            array_push($pos_bill_salses_line, $pos_bill_salses_line_temp);
        }

        $pos_bill_id = BluelimePOS::create_pos_bill('Inventory', $pos_bill_salses_line, $patient_detail['patient_id'], $patient_detail['name']);

        $update_data = ['pos_bill_id' => $pos_bill_id];

        $salesMainModel->update($sales_id, $update_data);
        if(!empty($sales_detail))
            $salesDetailModel->insertBatch($sales_detail);

        if(!empty($transaction_data))
            $transactionModel->insertBatch($transaction_data);
        else
            return BluelimeSystem::get_error_ajax_return('There is no data to save.');
            
        
        $customer_name = $sales_main['patient_name'];
        $tin = '';//$sales_main['tin_number'];
        $paymnet_method = 'Cash';
        $success = BluelimePOS::send_to_pos_automatic_inventory($customer_name, $tin, $paymnet_method, $sales_id);
        if($success)
            return BluelimeSystem::get_success_ajax_return($sales_id);
        else
            return BluelimeSystem::get_error_ajax_return('Cannot send to POS');
    }

    /*
    public function send_to_pos(){
        $sales_id = $_GET['sales_id'] ?? -1;

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');

        $sales_main = $salesMainModel->find($sales_id);
        $sales_detail = $salesDetailModel->where('sales_id', $sales_id)->findAll();

        $payment_detail = [
            'no_of_items' => count($sales_detail),
            'sub_total' => 0,
            'taxable_total' => 0,
            'vat' => 0,
            'total' => 0
        ];

        foreach($sales_detail as $index => $line){
            $sales_detail[$index]['price'] = ($line['unit_price'] * $line['quantity']);
            $line['price'] = $sales_detail[$index]['price'];

            if($line['vat'] == 0)
                $payment_detail['sub_total'] += $line['price'];
            else
                $payment_detail['taxable_total'] += $line['price'];

            $payment_detail['vat'] += $line['vat'];
            $payment_detail['total'] += $line['price'];
        }

        $data['sales_main'] = $sales_main;
        $data['sales_detail'] = $sales_detail;
        $data['payment_detail'] = $payment_detail;

        return  view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Compounding/_left_sidebar_compounding')
            .view('Include/_left_sidebar_bottom')
            .view('Include/_send_to_pos')
            .view('Include/_footer');
    }

    public function save_compounding_prescription_external(){
        $compounding_list = $_POST['compounding_prescription'];
        $patient_name = $_POST['patient_name'];
        $patient_age = $_POST['patient_age'];
        $patient_phone = $_POST['patient_phone'];
        $doctor_name = $_POST['doctor_name'];
        $hospital_name = $_POST['hospital_name'];
        $external_detail = [
            'patient_name' => $patient_name,
            'patient_age' => $patient_age,
            'patient_phone' => $patient_phone,
            'doctor_name' => $doctor_name,
            'hospital_name' => $hospital_name
        ];

        $prescription_id = \BluelmeHospital::save_compounding_prescription(compounding_prescription:$compounding_list,is_external:true,external_detail:$external_detail);

        return  BluelimeSystem::get_success_ajax_return($prescription_id);
    }
    */

    public function srv_form(): string
    {
        $srvMainModel = model('Inventory/SRVMainModel');
        $itemModel = model('/Inventory/ItemModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        $transactionModel = model('Inventory/TransactionModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        
        $store_id = 1;

        $search_key = $_GET['search_key'] ?? '';
        
        if(trim($search_key) != '')
            $srvMainModel = $srvMainModel->where('id LIKE', "%$search_key%")->orWhere('srv_code LIKE', "%$search_key%");
        
        $srv_main = $srvMainModel->where('is_issued' ,0)->where('requesting_store' ,3)->orderBy('id','DESC')->findAll(10,0);
        
        foreach($srv_main as $index => $store){
            $srv_main[$index]['requesting_store'] = $storeModel->where('id',$store['requesting_store'])->findColumn('store_name')[0];
            $srv_main[$index]['requested_store'] = $storeModel->where('id',$store['requested_store'])->findColumn('store_name')[0];
        }
        foreach($srv_main as $index => $user){
            $srv_main[$index]['requested_by__name'] = $userModel->where('id',$user['requested_by'])->findColumn('name')[0];
        }
        
        //$transactions = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('store_id', $store_id)->groupBy('item_id')->findAll();
        
        $items_temp = BluelimeSystem::get_all_items_with_price_and_quantity($store_id);
        $items = [];

        foreach($items_temp as $index => $row){
            if(intval($row['compounding_volume']) > 0)
                $items[$index] = $row;
        }


        /*
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
        */

        $data['active_menu'] = 'srv_form';
        $data['srv_main'] = $srv_main;
        $data['items'] = $items;
        $data['logged_user'] = $logged_user;

        return  view('Include/_header',$data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Compounding/_left_sidebar_compounding')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Compounding/srv_form')
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
        $userModel = model('System/UserModel');
        

        $stores = $storeModel->findAll();

        $srv_main = $srvMainModel->find($srv_id);
        $srv_detail = $srvDetailModel->where('srv_id', $srv_id)->findAll();
        $requested_by = $userModel->where('id',$srv_main['requested_by'])->findColumn('name');

        foreach($stores as $store){
            if($store['id'] == $srv_main['requesting_store'])
                $srv_main['requesting_store'] = $store['store_name'];

            if($store['id'] == $srv_main['requested_store'])
                $srv_main['requested_store'] = $store['store_name'];
        }

        $srv_main['requested_by'] = $requested_by[0];

        $data = [
            'srv_main' => $srv_main,
            'srv_detail' => $srv_detail,
        ];
        $data['active_menu'] = 'srv_form';
       
        
        return view('Inventory/Compounding/srv_print', $data);
    }

    public function stock_card()
    {
        $transactionModel = model('Inventory/TransactionModel');
        $storeModel = model('Inventory/StoreModel');
        $itemModel = model('Inventory/ItemModel');

        $stock_item =  $_POST['stock_item_id']??"";
        $store_id =  2;

        $stores = $storeModel->findAll();
        $items = $itemModel->findAll();
        $transactions = $transactionModel->where('store_id', $store_id)->where('item_id',$stock_item)->findAll();
        foreach($transactions as $index => $transaction){
            foreach($items as $item){
                if($item['id'] == $transaction['item_id']){
                    $transactions[$index]['item_generic_name'] = $item['item_generic_name'];
                    $transactions[$index]['item_brand_name'] = $item['item_brand_name'];
                    $transactions[$index]['item_code'] = $item['item_code'];
                    break;
                }
            }
            foreach($stores as $store){
                if($store['id'] == $transaction['store_id']){
                    $transactions[$index]['store_name'] = $store['store_name'];
                    break;
                }
            }
          
        }
        

        $sum = $transactionModel->select('sum(quantity) as sum')->where('store_id', $store_id)->where('item_id',$stock_item)->findAll()[0];
      
    
        
        $data['active_menu'] = 'stock_card';
        $data['transactions'] = $transactions;
        $data['sum'] = $sum;
        $data['stores'] = $stores;
        $data['items'] = $items;
        //BluelimeSystem::show_and_die($transactions);
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Compounding/_left_sidebar_compounding')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Compounding/stock_card')

        .view('Include/_footer');   

    }

}