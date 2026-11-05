<?php

namespace App\Controllers\Inventory;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;
class Dispensary extends \App\Controllers\BaseController
{
    public function index()
    {
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $onlinePrescriptionModel = model('Hospital/OnlinePrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        //$prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $itemModel = model('Inventory/ItemModel');
        //$transactionModel = model('Inventory/TransactionModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        //$default_store_id = $loggeduserdata['extra']['default_store'] ?? -1;
        $default_store_id = 2;
        $number_of_sales = BluelimeSystem::count_my_unsent_inventory_sales();
        $number_of_op = $onlinePrescriptionModel->where('status',0)->where('branch_to',$_ENV['BRANCH'])->findColumn('count(id)')[0];
        //BluelimeSystem::show_and_die($number_of_op);
        
        $searchkey= $_REQUEST['search_prescription'] ?? "";
        $searchkey = trim($searchkey);

        $items = $itemModel->findAll();

        $prescriptions = $prescriptionModel->search_prescription_for_dispensary($searchkey);
        // if(!empty($prescriptions)){
        //     $patients = BluelimeSystem::gather_objects_by_reference($prescriptions,"patient_id","Hospital/PatientModel","id");
        //     foreach($prescriptions as $index => $prescription){
        //         foreach($patients as $patient){
        //             if($prescription["patient_id"] == $patient["id"]){
        //                 //$prescription[$index]['date_of_birth'] = $patient["date_of_birth"];
        //                 //$prescription[$index]['sex'] = $patient["sex"];
        //                 break;
        //             }
        //         }
        //     }
        // }

        $prescription_group_id_list = [...BluelimeSystem::collect_field_value_from_object($prescriptions, 'prescription_group_id'), -1];

        $compoundingPrescriptionMainModel = $compoundingPrescriptionMainModel->select('MIN(id) as id, prescription_group_id, MIN(patient_detail) as patient_detail, MIN(created_by_name) as created_by_name, MIN(updated_at) as updated_at');

        if($searchkey != ''){
            if(is_numeric($searchkey))
                $compoundingPrescriptionMainModel = $compoundingPrescriptionMainModel->where('CAST(JSON_UNQUOTE(JSON_EXTRACT(patient_detail, "$.mobile")) as CHAR) like', "%$searchkey%");
            else
                $compoundingPrescriptionMainModel = $compoundingPrescriptionMainModel->where('CAST(JSON_UNQUOTE(JSON_EXTRACT(patient_detail, "$.name")) as CHAR) like', "%$searchkey%");
        }
        else
            $compoundingPrescriptionMainModel = $compoundingPrescriptionMainModel->where('is_sold', 0)->where('hide', 0);

        $compounding_prescription = $compoundingPrescriptionMainModel->whereNotIn('prescription_group_id', $prescription_group_id_list)->groupBy('prescription_group_id')->findAll();

        $comp_prescriptions_temp = [];
        foreach($compounding_prescription as $row){
            $temp = [
                'prescription_group_id' => $row['prescription_group_id'],
                'patient_id' => $row['patient_detail']['id'],
                'patient_name' => $row['patient_detail']['name'],
                'mobile' => $row['patient_detail']['mobile'],
                'registered_by_name' => $row['created_by_name'],
                'registered_date' => $row['updated_at'],
                'type' => 'Prescription'
            ];
            array_push($comp_prescriptions_temp, $temp);
        }

        $prescriptions = [...$prescriptions, ...$comp_prescriptions_temp];
        
        $prescriptions = BluelimeSystem::sort_array($prescriptions,['prescription_group_id'],true);

        $items = BluelimeSystem::get_all_items_with_price_and_quantity($default_store_id);
        //$items =[];
        
        $items_j = [];
        foreach($items as $row){
            $items_j["item_id_".$row['id']] = $row;
        }

        $data['active_menu'] = 'sales_form';
        $data['searchkey'] = $searchkey;
        $data['logged_user'] = $logged_user;
        $data['logged_user_data'] = $loggeduserdata;
        $data['prescriptions'] = $prescriptions??[];
        //$data['prescription'] = $prescription??[];
        //$data['prescription_detail'] = $prescription_detail??[];
        $data['items'] = $items_j;
        $data['number_of_sales'] = $number_of_sales;
        $data['number_of_op'] =  $number_of_op;

        
        return  view('Include/_header',$data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Dispensary/_left_sidebar_dispensary')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Dispensary/sales_form')
            .view('Include/_footer');
    }

    public function sales_dashboard(): string
    {
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        $itemModel = model('Inventory/ItemModel');
        $supplierModel = model('Inventory/SupplierModel');
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        
        $store_id = 2;

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
        

        $store_items = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id',1)->groupBy('item_id')->having('SUM(quantity) >', 0)->findAll();
        $zero_balance_items = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id',2)->groupBy('item_id')->having('SUM(quantity) =', 0)->findAll();

        $store_items = BluelimeSystem::index_object_list_array_by_unique_field($store_items, 'item_id');
        $zero_balance_items = BluelimeSystem::index_object_list_array_by_unique_field($zero_balance_items, 'item_id');

        $only_in_store_items = [];
        foreach($zero_balance_items as $item){
            $store_val = $store_items['item_id_'.$item['item_id']]['quantity'] ?? 0;

            if($store_val > 0){
                $items['id_'.$item['item_id']]['store_quantity'] = $store_val;
                $only_in_store_items[] = $items['id_'.$item['item_id']];
            }
        }

        $data['near_expiry_items'] = $near_expiry_items;
        $data['low_balance_items'] = $low_balance_items;
        $data['only_in_store_items'] = $only_in_store_items;
        $data['active_menu'] = 'dispensarydashboard';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Dispensary/_left_sidebar_dispensary')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Dispensary/dispensary_dashboard')

        .view('Include/_footer'); 
    }

    public function salessave(){
        $send_to_pos = $_POST['send_to_pos'] ?? false;

        [$sales_main, $sales_detail_data] = $this->sales_save_only();

        $success = true;
        if($send_to_pos){
            $pos_bill_id = $this->send_to_pos($sales_main, $sales_detail_data);
            $salesMainModel = model('Inventory/SalesMainModel');
            if($pos_bill_id == -1){
                $success = false;
            }
            else{
                $update_data = ['pos_bill_id' => $pos_bill_id, 'sent_to_pos' => 1];
                $salesMainModel->update($sales_main['id'], $update_data);
                $success = true;
            }
        }

        if(!$success)
            return BluelimeSystem::get_error_ajax_return('Cannot send to pos automatically.');

        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescription_group_id = $sales_main['prescription_group_id'] ?? -1;
        $prescriptionModel->where('prescription_group_id', $prescription_group_id)->update(row:['is_sold'=>1]);

        return BluelimeSystem::get_success_ajax_return($sales_main['id']);
    }

    private function sales_save_only()
    {
        $sales_main = $_POST['sales_main'] ?? [];
        $sales_detail = $_POST['sales_detail'] ?? [];

        //BluelimeSystem::show_and_die($sales_detail);
        
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');

        $items = BluelimeSystem::gather_objects_by_reference($sales_detail, 'id', 'Inventory/ItemModel','id',true);

        $sales_main['sales_type'] = BluelimeConfiguration::SALES_TYPE::DISPENSARY;

        $sales_main_id = $salesMainModel->insert($sales_main);
        
        $sales_detail_data = [];
        foreach($sales_detail as $sales_line)
        {
            $sales_line_temp = [
                'sales_id' => $sales_main_id,
                'item_id' => $sales_line['id'],
                'item_detail' => BluelimeSystem::convert_array_to_string($items['id_'.$sales_line['id']]),
                'quantity' => $sales_line['quantity'],
                'vat' => $sales_line['unit_price'] * $sales_line['tax'],
                'unit_price' => $sales_line['unit_price'],
            ];

            $is_vat = ($sales_line['tax'] > 0) ? true : false;
            $sales_line_temp['is_vat'] = $is_vat;

            array_push($sales_detail_data, $sales_line_temp);
        }

        if(!empty($sales_detail_data)){
            $salesDetailModel->insertBatch($sales_detail_data);
        }

        $sales_main['id'] = $sales_main_id;
        return [$sales_main, $sales_detail_data];

    } 

    private function send_to_pos($sales_main, $sales_detail_data){
        //BluelimeSystem::print_associated_array($sales_detail_data);

        $sales_collector = [];
        foreach($sales_detail_data as $sales_detail){
            $sales_detail['item_detail'] = BluelimeSystem::convert_to_array($sales_detail['item_detail']);
            if(!isset($sales_collector['item_id_'.$sales_detail['item_id']]))
                $sales_collector['item_id_'.$sales_detail['item_id']] = ['item_id'=>$sales_detail['item_id'],'item_description'=>$sales_detail['item_detail']['item_brand_name'].' '.$sales_detail['item_detail']['strength'].' '.$sales_detail['item_detail']['preparation'],'item_type'=>$sales_detail['item_detail']['item_type'],'item_quantity'=>0,'unit_of_measurment'=>$sales_detail['item_detail']['measurement'],'unit_price'=>$sales_detail['unit_price'], 'is_vat'=>$sales_detail['item_detail']['is_taxable']];
            $sales_collector['item_id_'.$sales_detail['item_id']]['item_quantity'] += $sales_detail['quantity'];
        }

        $pos_bill = BluelimePOS::create_pos_bill(type: 'Inventory', sales_line: $sales_collector, customer_code: '', customer_name: '',customer_tin:'',company_name:'',sales_id_list:[$sales_main['id']??-1]);
        
        $success = BluelimePOS::send_to_pos($pos_bill);
        if(!$success){
            $pOSBillModel = model('POS/POSBillModel');
            $pOSBillModel->delete($pos_bill['id']);
            return -1;
        }
        return $pos_bill['id'] ?? -1;
    }

    /*
    private function send_to_pos($sales_main, $sales_detail_data){
        $sales_main_id = $sales_main['id'];
        $salesMainModel = model('Inventory/SalesMainModel');

        $pos_bill_data = [];
        
        foreach($sales_detail_data as $sales_line){
            $sales_line['item_detail'] = BluelimeSystem::convert_to_array($sales_line['item_detail']);
            $pos_bill_temp = BluelimePOS::create_sales_line($sales_line['item_id'],$sales_line['item_detail']['item_brand_name'],$sales_line['item_detail']['item_type'],$sales_line['quantity'],$sales_line['item_detail']['measurement'],$sales_line['unit_price'], $sales_line['is_vat']);
            array_push($pos_bill_data, $pos_bill_temp);
        }

        $pos_bill_id = BluelimePOS::create_pos_bill('Inventory', $pos_bill_data, $sales_main['patient_id'], $sales_main['patient_name'], $sales_main['tin_number']);
        if($sales_main['patient_name'] === 'Walk In')
            $sales_main['patient_name'] = 'Walk In - '.$pos_bill_id;
        
        $update_data = ['pos_bill_id' => $pos_bill_id, 'patient_name' => $sales_main['patient_name']];
        $salesMainModel->update($sales_main_id, $update_data);
        
        $customer_name = $sales_main['patient_name'];
        $tin = $sales_main['tin_number'];
        $paymnet_method = 'Cash';
        $sales_id = $sales_main_id;
        $success = BluelimePOS::send_to_pos_automatic_inventory($customer_name, $tin, $paymnet_method, $sales_id);
        return $success;   
    }
    */

    
    public function combine_and_send_to_pos(){
        $sales_list = $_POST['sales_list'];
        $customer_name = $_POST['customer_name'];
        $company_name = $_POST['company_name'];
        $company_id = $_POST['company_id'];
        $tin = $_POST['tin'];
        $is_credit = boolval($_POST['is_credit']);

        //BluelimeSystem::show_and_die($company_name);
        if(trim($customer_name) == '')
            $customer_name = 'Walk in';
        
        $sales_mains = BluelimeSystem::gather_objects_by_reference($sales_list, 'id','Inventory/SalesMainModel','id',true);
        $sales_details = BluelimeSystem::gather_objects_by_reference($sales_mains,'id','Inventory/SalesDetailModel','sales_id');

        foreach($sales_details as  $row){
            if(!isset($sales_mains['id_'.$row['sales_id']]['sales_detail']))
                $sales_mains['id_'.$row['sales_id']]['sales_detail'] = [];
            array_push($sales_mains['id_'.$row['sales_id']]['sales_detail'], $row);
        }

        $sales_collector = [];
        $compounding_group = 65; //A
        foreach($sales_mains as $sales_main){
            if($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::DISPENSARY){
                foreach($sales_main['sales_detail'] as $sales_detail){
                    if(!isset($sales_collector['item_id_'.$sales_detail['item_id']]))
                        $sales_collector['item_id_'.$sales_detail['item_id']] = ['item_id'=>$sales_detail['item_id'],'item_description'=>$sales_detail['item_detail']['item_brand_name'].' '.$sales_detail['item_detail']['strength'],'item_type'=>$sales_detail['item_detail']['item_type'],'item_quantity'=>0,'unit_of_measurment'=>$sales_detail['item_detail']['measurement'],'unit_price'=>$sales_detail['unit_price'], 'is_vat'=>$sales_detail['item_detail']['is_taxable']];
                    
                    $sales_collector['item_id_'.$sales_detail['item_id']]['item_quantity'] += $sales_detail['quantity'];
                }
            }
            elseif($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::COMPOUNDING){
                $sales_collector['compounding_t_'.chr($compounding_group)] = ['item_id'=>'c_t_'.chr($compounding_group),'item_description'=>'Compounding t '.chr($compounding_group),'item_type'=>'DRUG','item_quantity'=>1,'unit_of_measurment'=>'Pcs','unit_price'=>0, 'is_vat'=>1];
                $sales_collector['compounding_'.chr($compounding_group)] = ['item_id'=>'c_'.chr($compounding_group),'item_description'=>'Compounding '.chr($compounding_group),'item_type'=>'DRUG','item_quantity'=>1,'unit_of_measurment'=>'Pcs','unit_price'=>0, 'is_vat'=>0];
                
                foreach($sales_main['sales_detail'] as $sales_detail){
                    if($sales_detail['item_detail']['is_taxable'])
                        $sales_collector['compounding_t_'.chr($compounding_group)]['unit_price'] += $sales_detail['quantity'] * $sales_detail['unit_price'];
                    else
                        $sales_collector['compounding_'.chr($compounding_group)]['unit_price'] += $sales_detail['quantity'] * $sales_detail['unit_price'];
                }
                if($sales_collector['compounding_t_'.chr($compounding_group)]['unit_price'] == 0)
                    unset($sales_collector['compounding_t_'.chr($compounding_group)]);
                
                if($sales_collector['compounding_'.chr($compounding_group)]['unit_price'] == 0)
                    unset($sales_collector['compounding_'.chr($compounding_group)]);

                $compounding_group++;
            }
        }
        $sales_id_list = BluelimeSystem::collect_field_value_from_object($sales_list,'id');

        $pos_bill = BluelimePOS::create_pos_bill(type: 'Inventory', sales_line: $sales_collector,  customer_code:'',customer_name: $customer_name, company_name: $company_name, customer_tin: $tin, is_credit: $is_credit, sales_id_list:$sales_id_list);

        $success = BluelimePOS::send_to_pos($pos_bill);
        if($success){
            $update_data_sales_main = ['pos_bill_id'=>$pos_bill['id'], 'patient_name'=>$customer_name,'company_name'=>$company_name,'company_id'=>$company_id,'tin_number'=>$tin, 'sent_to_pos' => 1];
            $salesMainModel = model('Inventory/SalesMainModel');
            $salesMainModel->whereIn('id', $sales_id_list)->update(row:$update_data_sales_main);
            
            return BluelimeSystem::get_success_ajax_return($pos_bill['id']);
        }
        else{
            $pOSBillModel = model('POS/POSBillModel');
            $pOSBillModel->delete(id:$pos_bill['id']);
            
            return BluelimeSystem::get_error_ajax_return("Invoice cannot be sent to POS.\\nServer error. File cannot be created on POS machine.");
        }
    }

public function combine_and_send_to_pos_past_posting(){
        $sales_list = $_POST['sales_list'];
        $customer_name = $_POST['customer_name'];
        $company_name = $_POST['company_name'];
        $tin = $_POST['tin'];
        $is_credit = boolval($_POST['is_credit']);
        $receipt_no = $_POST['receipt_no'];
        $paid_date = $_POST['paid_date'];

        //BluelimeSystem::show_and_die($company_name);
        if(trim($customer_name) == '')
            $customer_name = 'Walk in';
        
        $sales_mains = BluelimeSystem::gather_objects_by_reference($sales_list, 'id','Inventory/SalesMainModel','id',true);
        $sales_details = BluelimeSystem::gather_objects_by_reference($sales_mains,'id','Inventory/SalesDetailModel','sales_id');

        foreach($sales_details as  $row){
            if(!isset($sales_mains['id_'.$row['sales_id']]['sales_detail']))
                $sales_mains['id_'.$row['sales_id']]['sales_detail'] = [];
            array_push($sales_mains['id_'.$row['sales_id']]['sales_detail'], $row);
        }

        $sales_collector = [];
        $compounding_group = 65; //A
        foreach($sales_mains as $sales_main){
            if($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::DISPENSARY){
                foreach($sales_main['sales_detail'] as $sales_detail){
                    if(!isset($sales_collector['item_id_'.$sales_detail['item_id']]))
                        $sales_collector['item_id_'.$sales_detail['item_id']] = ['item_id'=>$sales_detail['item_id'],'item_description'=>$sales_detail['item_detail']['item_brand_name'].' '.$sales_detail['item_detail']['strength'],'item_type'=>$sales_detail['item_detail']['item_type'],'item_quantity'=>0,'unit_of_measurment'=>$sales_detail['item_detail']['measurement'],'unit_price'=>$sales_detail['unit_price'], 'is_vat'=>$sales_detail['item_detail']['is_taxable']];
                    
                    $sales_collector['item_id_'.$sales_detail['item_id']]['item_quantity'] += $sales_detail['quantity'];
                }
            }
            elseif($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::COMPOUNDING){
                $sales_collector['compounding_t_'.chr($compounding_group)] = ['item_id'=>'c_t_'.chr($compounding_group),'item_description'=>'Compounding t '.chr($compounding_group),'item_type'=>'DRUG','item_quantity'=>1,'unit_of_measurment'=>'Pcs','unit_price'=>0, 'is_vat'=>1];
                $sales_collector['compounding_'.chr($compounding_group)] = ['item_id'=>'c_'.chr($compounding_group),'item_description'=>'Compounding '.chr($compounding_group),'item_type'=>'DRUG','item_quantity'=>1,'unit_of_measurment'=>'Pcs','unit_price'=>0, 'is_vat'=>0];
                
                foreach($sales_main['sales_detail'] as $sales_detail){
                    if($sales_detail['item_detail']['is_taxable'])
                        $sales_collector['compounding_t_'.chr($compounding_group)]['unit_price'] += $sales_detail['quantity'] * $sales_detail['unit_price'];
                    else
                        $sales_collector['compounding_'.chr($compounding_group)]['unit_price'] += $sales_detail['quantity'] * $sales_detail['unit_price'];
                }
                if($sales_collector['compounding_t_'.chr($compounding_group)]['unit_price'] == 0)
                    unset($sales_collector['compounding_t_'.chr($compounding_group)]);
                
                if($sales_collector['compounding_'.chr($compounding_group)]['unit_price'] == 0)
                    unset($sales_collector['compounding_'.chr($compounding_group)]);

                $compounding_group++;
            }
        }
        $sales_id_list = BluelimeSystem::collect_field_value_from_object($sales_list,'id');

        $pos_bill = BluelimePOS::create_pos_bill(type: 'Inventory', sales_line: $sales_collector,  customer_code:'',customer_name: $customer_name, company_name: $company_name, customer_tin: $tin, is_credit: $is_credit, sales_id_list:$sales_id_list);

        $update_data_sales_main = ['pos_bill_id'=>$pos_bill['id'], 'patient_name'=>$customer_name,'company_name'=>$company_name, 'tin_number'=>$tin, 'sent_to_pos' => 1, 'fs_number' => 'M '.$receipt_no, 'is_recieved'=>1, 'recieved_date'=>$paid_date];
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesMainModel->whereIn('id', $sales_id_list)->update(row:$update_data_sales_main);
        $posBillModel = model('POS/POSBillModel');
        $posBillModel->update($pos_bill['id'], ['response_data'=>'Manual Posting', 'fs_number'=>'M '.$receipt_no, 'response_date'=>$paid_date]);
        return BluelimeSystem::get_success_ajax_return($pos_bill['id']);
    }
    /*
    private function send_to_pos($customer_name, $tin, $paymnet_method, $sales_id){
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');

        $sales_main = $salesMainModel->find($sales_id);
        $pos_bill_id = $sales_main['pos_bill_id'];
        $posBill = $pOSBillModel->find($pos_bill_id);

        $pos_bill = $posBill['pos_bill_json'];
        $pos_bill['Customer_Name'] = $customer_name;
        $pos_bill['Customer_TIN'] = $tin;
        $pos_bill['Payment_Type'] = $paymnet_method;

        $success = BluelimePOS::send_to_pos($pos_bill);
        
        if($success){
            $pos_bill_json = BluelimeSystem::convert_array_to_string($pos_bill);
            $pOSBillModel->update($pos_bill_id, ['pos_bill_json' => $pos_bill_json]);
            $salesMainModel->update($sales_id, ['sent_to_pos' => 1]);
            return true;
        }
        return false;
    }
    */



    public function checkout(){
        $salesMainModel = model('Inventory/SalesMainModel');
        $companyModel = model('Hospital/CompanyModel');
        //$salesDetailModel = model('Inventory/SalesDetailModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        if(array_search(BluelimeConfiguration::USER_ROLE_EXTRA::POST_PAST_SALES,($loggeduser['extra']['extra_role']??[]))!==false)
            return redirect()->to('/inventory/dispensary/checkout_already_paid');

        $sales_main = $salesMainModel->where('registered_by', $loggeduser['id'])->where('sent_to_pos', 0)->orderBy('id', 'DESC')->findAll();
        $sales_detail = BluelimeSystem::gather_objects_by_reference($sales_main,'id', 'Inventory/SalesDetailModel','sales_id');
        $customers = $salesMainModel->distinct()->select('patient_name, tin_number')->where('patient_name not like','%Walk In%')->orderBy('patient_name')->findAll();
        $company = $companyModel->select('id, company_name, tin_number')->orderBy('company_name', 'ASC')->where('is_active',1)->findAll();
        
        
        /*
        $customers = [];
        foreach($customersName as $name)
            array_push($customers, ['patient_name'=>$name]);
        */
        //BluelimeSystem::print_associated_array($customers);
        
        $sales_main_temp = [];
        foreach($sales_main as $row){
            $sales_main_temp['sales_id_'.$row['id']] = $row;
            $sales_main_temp['sales_id_'.$row['id']]['sales_detail'] = [];
            $sales_main_temp['sales_id_'.$row['id']]['sub_total'] = 0;
        }
        $sales_main = $sales_main_temp;

        foreach($sales_detail as $row){
            $sales_main['sales_id_'.$row['sales_id']]['sales_detail']['sales_detail_id_'.$row['id']] = $row;
           // array_push($sales_main['sales_id_'.$row['sales_id']]['sales_detail'],$row); 
            $total = $sales_main['sales_id_'.$row['sales_id']]['total'] ?? 0;
            $total += $row['quantity'] * $row['unit_price'];
            $total +=  $row['quantity'] * $row['vat'];
            $sales_main['sales_id_'.$row['sales_id']]['total'] = $total;
            $sales_main['sales_id_'.$row['sales_id']]['sub_total'] = $total;
        }

        //BluelimeSystem::print_associated_array($sales_main);
        $data = [
            'active_menu' => 'checkout',
            'sales_main' => $sales_main,
            'customers' => $customers,
            'company' => $company,
        ];
        return view('Inventory/Dispensary/checkout', $data);
    }

    public function checkout_already_paid(){
        $salesMainModel = model('Inventory/SalesMainModel');
        $companyModel = model('Hospital/CompanyModel');
        //$salesDetailModel = model('Inventory/SalesDetailModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $sales_main = $salesMainModel->where('registered_by', $loggeduser['id'])->where('sent_to_pos', 0)->orderBy('id', 'DESC')->findAll();
        $sales_detail = BluelimeSystem::gather_objects_by_reference($sales_main,'id', 'Inventory/SalesDetailModel','sales_id');
        $customers = $salesMainModel->distinct()->select('patient_name, tin_number')->where('patient_name not like','%Walk In%')->orderBy('patient_name')->findAll();
        $company = $companyModel->select('company_name, tin_number')->orderBy('company_name', 'ASC')->where('is_active',1)->findAll();
        /*
        $customers = [];
        foreach($customersName as $name)
            array_push($customers, ['patient_name'=>$name]);
        */
        //BluelimeSystem::print_associated_array($customers);
        
        $sales_main_temp = [];
        foreach($sales_main as $row){
            $sales_main_temp['sales_id_'.$row['id']] = $row;
            $sales_main_temp['sales_id_'.$row['id']]['sales_detail'] = [];
            $sales_main_temp['sales_id_'.$row['id']]['sub_total'] = 0;
        }
        $sales_main = $sales_main_temp;

        foreach($sales_detail as $row){
            $sales_main['sales_id_'.$row['sales_id']]['sales_detail']['sales_detail_id_'.$row['id']] = $row;
           // array_push($sales_main['sales_id_'.$row['sales_id']]['sales_detail'],$row); 
            $total = $sales_main['sales_id_'.$row['sales_id']]['total'] ?? 0;
            $total += $row['quantity'] * $row['unit_price'];
            $total +=  $row['quantity'] * $row['vat'];
            $sales_main['sales_id_'.$row['sales_id']]['total'] = $total;
            $sales_main['sales_id_'.$row['sales_id']]['sub_total'] = $total;
        }

        //BluelimeSystem::print_associated_array($sales_main);
        $data = [
            'active_menu' => 'checkout',
            'sales_main' => $sales_main,
            'customers' => $customers,
            'company' => $company,
        ];
        return view('Inventory/Dispensary/checkout_already_paid', $data);
    }
    public function remove_sales_line(){
        $sales_id = $_POST['sales_id'];
        $sales_detail_id = $_POST['sales_detail_id'] ?? -1;

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');

        if($sales_detail_id == BluelimeConfiguration::SALES_TYPE::COMPOUNDING){
            $salesDetailModel->where('sales_id', $sales_id)->delete();
            $sales_main = $salesMainModel->find($sales_id);
            $prescription_id = $sales_main['prescription_id'] ?? -1;
            $compoundingPrescriptionMainModel->update($prescription_id, ['is_sold' => 0]);
        }
        else
            $salesDetailModel->delete(id:$sales_detail_id);

        $sales_details = $salesDetailModel->where('sales_id', $sales_id)->findAll();
        if(empty($sales_details))
            $salesMainModel->delete(id:$sales_id);
        return BluelimeSystem::get_success_ajax_return('deleted');
    }

    public function on_pos(){
        $posBillModel = model('POS/POSBillModel');

        $pos_bills = $posBillModel->where('type', 'Inventory')->orderBy('id', 'DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);

        foreach($pos_bills as $index => $pos_bill){
            $pos_bills[$index]['total'] = $this->calculate_total($pos_bill);
        }

        $data = [
            'pos_bills' => $pos_bills,
            'pager'=>$posBillModel->pager,
            'active_menu'=>'checkout',
        ];

        return view('Inventory/Dispensary/on_pos', $data);
    }

    private function calculate_total($pos_bill):float{
        $total = 0.0;

        foreach($pos_bill['pos_bill_json']['sales_item_list'] as $line)
            $total += $line['quantity'] * ($line['unit_price'] + ($line['vat'] * ($line['unit_price'] * .15)));
        
        return $total;
    }

    public function reverse_invoice():string {
        $pos_bill_id = $_POST['pos_bill_id'];
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $sales_id_list = $salesMainModel->where('pos_bill_id', $pos_bill_id)->findColumn('id');

        //sleep(3);
        $pos_bill = $pOSBillModel->find($pos_bill_id);
        if(empty($sales_id_list))
            return BluelimeSystem::get_error_ajax_return('No sales found.');

        if(trim($pos_bill['response_date']) != '')
            return BluelimeSystem::get_error_ajax_return('Cannot reverse. The invoice is already paid.');

        $sales_id_list_str = '';
        foreach($sales_id_list as $id)
            $sales_id_list_str .= ','.$id;

        $sales_id_list_str = substr($sales_id_list_str, 1);

        if(!BluelimePOS::delete_invoice($pos_bill_id))
            return BluelimeSystem::get_error_ajax_return('Cannot void from POS machine.');

        $salesMainModel->whereIn('id', $sales_id_list)->update(row:['pos_bill_id'=>NULL, 'sent_to_pos'=>0]);
        return BluelimeSystem::get_success_ajax_return($sales_id_list_str);
    }

    public function delete_invoice():string {
        $pos_bill_id = $_POST['pos_bill_id'];
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $sales_id_list = $salesMainModel->where('pos_bill_id', $pos_bill_id)->findColumn('id');

        //sleep(3);
        $pos_bill = $pOSBillModel->find($pos_bill_id);
        if(empty($sales_id_list))
            return BluelimeSystem::get_error_ajax_return('No sales found.');
        if(trim($pos_bill['response_date']) != '')
            return BluelimeSystem::get_error_ajax_return('Cannot delete. The invoice is already paid.');

        
        if(!BluelimePOS::delete_invoice($pos_bill_id))
            return BluelimeSystem::get_error_ajax_return('Cannot void from POS machine.');
        
        $salesMainModel->whereIn('id', $sales_id_list)->delete();
        return BluelimeSystem::get_success_ajax_return('deleted');
    }
    public function prescriptionprinted() {
        $prescription_group_id = $_POST['prescription_group_id'];
        return \BluelmeHospital::set_prescription_is_sold($prescription_group_id);
    }
    public function prescriptionhide() {
        $prescription_group_id = $_POST['prescription_group_id'];
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $prescriptionModel->where('prescription_group_id',$prescription_group_id)->set('hide',1)->update();
        $compoundingPrescriptionMainModel->where('prescription_group_id',$prescription_group_id)->set('hide',1)->update();
        return BluelimeSystem::get_success_ajax_return('ok');
    }

    public function compounding_list(): string
    {
        
        $search_key = $_GET['search_key'] ?? '';
        $filter_date = $_GET['filter_date'] ?? date('Y-m-d');
        $position = $_GET['position'] ?? '';
        
        $salesMainModel = model('Inventory/SalesMainModel');
        
        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $salesMainModel = $salesMainModel->where('recieved_date <=', $filter_date);
            else
                $salesMainModel = $salesMainModel->where('recieved_date >=', $filter_date);
        }
        $status = 0;
        if(trim($search_key) != ''){
            $salesMainModel = $salesMainModel->groupStart()->where('patient_name LIKE', "%$search_key%")->orWhere('id LIKE', "%$search_key")->groupEnd();
            $status = 1;
        }
       
        $sales_main = $salesMainModel->where('compounding_status',$status)->where('sales_type',BluelimeConfiguration::SALES_TYPE::COMPOUNDING)->where('is_recieved',1)->orderBy('id','DESC')->paginate(perPage:BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
       
        $pager = $salesMainModel->pager;
       
        $data['active_menu'] = 'compounding_list';
        $data['sales_main'] = $sales_main;
        $data['search_key'] = $search_key;
        $data['filter_date'] = $filter_date;
        $data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();


        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Dispensary/_left_sidebar_dispensary')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Dispensary/compounding_list')
                .view('Include/_footer');     
    }public function sales_list(): string
    {
        
        $search_key = $_GET['search_key'] ?? '';
        $filter_date = $_GET['filter_date'] ?? date('Y-m-d');
        $position = $_GET['position'] ?? '';
        
        $salesMainModel = model('Inventory/SalesMainModel');

        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $salesMainModel = $salesMainModel->where('recieved_date <=', $filter_date);
            else
                $salesMainModel = $salesMainModel->where('recieved_date >=', $filter_date);
        }
        
        if(trim($search_key) != '')
            $salesMainModel = $salesMainModel->where('patient_name LIKE', "%$search_key%");
       
        $sales_main = $salesMainModel->orderBy('id','DESC')->paginate(perPage:BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        $pager = $salesMainModel->pager;

        $data['active_menu'] = 'sales_list';
        $data['sales_main'] = $sales_main;
        $data['search_key'] = $search_key;
        $data['filter_date'] = $filter_date;
        $data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();


        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Dispensary/_left_sidebar_dispensary')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Dispensary/sales_list')
                .view('Include/_footer');     
    }
    public function compoundingdone()
    {
        $sales_id = $_POST['sales_id'] ?? -1;
        $salesMainModel = model('Inventory/SalesMainModel');

        //BluelimeSystem::show_and_die($_POST);
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user_id = $loggeduserdata['id'];
        $salesMainModel->where('id',$sales_id)->set(['compounding_status'=>1,'compounding_done_by'=>$logged_user_id])->update();
        return BluelimeSystem::get_success_ajax_return('ok');
        
    }
    public function addlabeldirection()
    {
        $sales_id = $_POST['sales_id'] ?? -1;
        $compounding_amharic_direction = $_POST['selected_direction']??'';
        $salesMainModel = model('Inventory/SalesMainModel');

        //BluelimeSystem::show_and_die($_POST);

        $salesMainModel->where('id',$sales_id)->set('compounding_amharic_direction',$compounding_amharic_direction)->update();
        return BluelimeSystem::get_success_ajax_return('ok');
        
    }
    public function add_template_data(){
        $value = $_POST['value'];
        $value = ucfirst($value);

        $autoCompleteModel = model('Hospital/AutoCompleteModel');

        $auto_compelete = $autoCompleteModel->where('name', 'compounding_remarks')->findAll();
        $auto_compelete = $auto_compelete[0] ?? [];
        $id = $auto_compelete['id'] ?? -1;
        if($id == -1)
            $id = $autoCompleteModel->insert(['name'=>'compounding_remarks', 'data' => ['["'.$value.'"]']]);
        else{
            if(array_search($value,$auto_compelete['data']) === false){
                array_push($auto_compelete['data'], $value);
                $update_data = ['data' => BluelimeSystem::convert_array_to_string($auto_compelete['data'])];
                $autoCompleteModel->update($id,$update_data);
            }
            else
                return BluelimeSystem::get_error_ajax_return($value . ' already exisits.');
        }
        return BluelimeSystem::get_success_ajax_return($value);
    }

    public function compounding_sales(): string
    {
        $sales_id = $_GET['sales_id'] ?? -1;
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $userModel = model('System/UserModel');
        $autoCompleteModel = model('Hospital/AutoCompleteModel');

        $sales_main = $salesMainModel->find($sales_id);
        $sales_main['pr_group'] = $compoundingPrescriptionMainModel->where('id',$sales_main['prescription_id'])->findColumn('prescription_group_id')[0]??-1;      
        
        $sales_detail = $salesDetailModel->where('sales_id', $sales_id)->findAll();
        $registered_by_name = $userModel->where('id',$sales_main['registered_by'])->findColumn('name');

        $sales_main['registered_by_name'] = $registered_by_name[0];
        
        $prescription_group = [];
        $prescriptions = $prescriptionModel->where('prescription_group_id', $sales_main['pr_group'])->orderBy('id', 'DESC')->findAll();
        $prescription_details = BluelimeSystem::gather_objects_by_reference($prescriptions,'id','Hospital/PrescriptionDetailModel','prescription_id');
        $compoundings = $compoundingPrescriptionMainModel->where('prescription_group_id', $sales_main['pr_group'])->orderBy('id', 'DESC')->findAll();
        $compounding_details = BluelimeSystem::gather_objects_by_reference($compoundings,'id','Hospital/CompoundingPrescriptionDetailModel','main_id');

        foreach($prescriptions as $index =>$row){
            $prescriptions[$index]['details'] = [];
            foreach($prescription_details as $detail){
                if($row['id'] == $detail['prescription_id'])
                    array_push($prescriptions[$index]['details'], $detail);
            }
        }

        foreach($compoundings as $index =>$row){
            $compoundings[$index]['compounding_detail'] = [];
            foreach($compounding_details as $detail){
                if($row['id'] == $detail['main_id'])
                    array_push($compoundings[$index]['compounding_detail'], $detail);
            }
        }

        foreach($prescriptions as $row){
            if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
            array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['prescriptions'], $row);
        }

        foreach($compoundings as $row){
            if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
            array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['compoundings'], $row);
        }
        $compounding_remarks = $autoCompleteModel->where('name','compounding_remarks')->findColumn('data')[0] ?? [];

        //BluelimeSystem::show_and_die($compounding_remarks);

        $data = [
            'compounding_remarks' => $compounding_remarks,
            'sales_main' => $sales_main,
            'sales_detail' => $sales_detail,
            'prescription_group' => $prescription_group
        ];
        $data['active_menu'] = 'compounding_list';
        return view('Inventory/Dispensary/compounding_sales', $data);
    }
    public function sales_print(): string
    {
        $sales_id = $_GET['sales_id'] ?? -1;
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $userModel = model('System/UserModel');

        $sales_main = $salesMainModel->find($sales_id);
        $sales_detail = $salesDetailModel->where('sales_id', $sales_id)->findAll();
        $registered_by_name = $userModel->where('id',$sales_main['registered_by'])->findColumn('name');

        $sales_main['registered_by_name'] = $registered_by_name[0];
        $data = [
            'sales_main' => $sales_main,
            'sales_detail' => $sales_detail,
        ];
        $data['active_menu'] = 'sales_list';
        
        if($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::DISPENSARY)
            return view('Inventory/Dispensary/sales_print', $data);
        return view('Inventory/Dispensary/compounding_sales_print', $data);
    }


    public function view_prescription(): string
    {
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $compoundingPrescriptionMain = model('Hospital/CompoundingPrescriptionMainModel');
        $patientModel = model('Hospital/PatientModel');
        $transactionModel = model('Inventory/TransactionModel');
        $itemModel = model('Inventory/ItemModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $default_store_id = $loggeduserdata['extra']['default_store'] ?? -1;
        $prescription_group_id = $_POST['prescription_group_id'];

        $prescriptions = $prescriptionModel->where('prescription_group_id',$prescription_group_id)->findAll();
        $compoundings = $compoundingPrescriptionMain->where('prescription_group_id',$prescription_group_id)->findAll();
        $patient_id = $prescriptions[0]['patient_id'] ?? $compoundings[0]['patient_detail']['id'] ?? -1;
        $is_void = $prescriptions[0]['is_void'] ?? $compoundings[0]['is_void'] ?? -1;
        $patient = $patientModel->find($patient_id);
        
        $prescription_id_list = [...BluelimeSystem::collect_field_value_from_object($prescriptions, 'id'), -1];

        $prescription_detail = $prescriptionDetailModel->whereIn('prescription_id',$prescription_id_list)->findAll();
        //$items = BluelimeSystem::gather_objects_by_reference($prescription_detail,'drug_id','Inventory/ItemModel','id',true);

        $patient_name = $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'];
        $registered_by_name = $prescriptions[0]['registered_by_name'] ?? $compoundings[0]['created_by_name'];

        foreach($prescription_detail as $index => $pd){
            $prescription_detail[$index]['patient_name'] = $patient_name;
            $prescription_detail[$index]['is_void'] = $is_void;
            $prescription_detail[$index]['patient_id'] = $patient_id;
            $prescription_detail[$index]['sex'] = $patient['sex'];
            $prescription_detail[$index]['date_of_birth'] = $patient['date_of_birth'];
            $prescription_detail[$index]['registered_by_name'] = $registered_by_name;
            
            $prescription_detail[$index]['current_balance'] = $transactionModel->select('SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id', $default_store_id)->where('item_id',$pd['drug_id'])->findColumn('sum(quantity)')[0];
            $prescription_detail[$index]['item_generic_name'] = $pd['drug_detail']['item_generic_name'];
            $prescription_detail[$index]['item_brand_name'] = $pd['drug_detail']['item_brand_name'];
            $prescription_detail[$index]['preparation'] = $pd['drug_detail']['preparation'];
            $prescription_detail[$index]['strength'] = $pd['drug_detail']['strength'];
            $prescription_detail[$index]['prescription_group_id'] = $prescription_group_id;
            $prescription_detail[$index]['type'] = 'Prescription';
        }
        
        $has_compounding = [
            'patient_name' => $patient_name,
            'is_void' => $is_void,
            'patient_id' => $patient_id,
            'sex' => $patient['sex'],
            'date_of_birth' => $patient['date_of_birth'],
            'registered_by_name' => $registered_by_name,
            'prescription_group_id' => $prescription_group_id,
            'type' => 'Compounding',
            'compounding_id' => $compoundings[0]['id'] ?? -1,
            'compounding_count' => count($compoundings),
        ];

        if(count($compoundings) > 0)
            array_push($prescription_detail,$has_compounding);
        return BluelimeSystem::convert_array_to_string($prescription_detail);
    }

    public function srv_form(): string
    {
        $srvMainModel = model('Inventory/SRVMainModel');
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $itemModel = model('/Inventory/ItemModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        $transactionModel = model('Inventory/TransactionModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user_id = $loggeduserdata['id'];
        $default_store_id = $loggeduserdata['extra']['default_store'] ?? -1;
        $search_key = $_GET['search_key'] ?? '';
        $position = $_GET['position'] ?? '';
        $search_type = $_GET['search_type'] ?? '';

        $searchkey_approved_siv = '';
        if($search_type == 'approved_siv')
            $searchkey_approved_siv = $search_key;
        
        if(trim($search_key) != '')
            $srvMainModel = $srvMainModel->where('id LIKE', "%$search_key%")->orWhere('srv_code LIKE', "%$search_key%");

        $srv_main = $srvMainModel->where('is_issued' ,0)->orderBy('id','DESC')->findAll(10,0);
        foreach($srv_main as $index => $store){
            $srv_main[$index]['requesting_store'] = $storeModel->where('id',$store['requesting_store'])->findColumn('store_name')[0];
            $srv_main[$index]['requested_store'] = $storeModel->where('id',$store['requested_store'])->findColumn('store_name')[0];
        }
        foreach($srv_main as $index => $user){
            $srv_main[$index]['requested_by__name'] = $userModel->where('id',$user['requested_by'])->findColumn('name')[0];
        }

        
        //BluelimeSystem::show_and_die($siv_main);

        $transactions = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id', 1)->groupBy('item_id')->findAll();
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

        //issued

        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $sivMainModel = $sivMainModel->where('created_at <=', $filter_date);
            else
                $sivMainModel = $sivMainModel->where('created_at >=', $filter_date);
        }

        if(trim($search_key) != '')
            $sivMainModel = $sivMainModel->groupStart()->where('srv_id LIKE', "%$search_key%")->orWhere('id LIKE', "%$search_key%")->orWhere('description LIKE', "%$search_key%")->groupEnd();

        $siv_main = $sivMainModel->where('status',BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
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

        $approved_sivs = $sivMainModel->where('status >',BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)->orderBy('id','DESC');
        if(!empty($searchkey_approved_siv)){
            $approved_sivs = $approved_sivs->groupStart()->where('srv_id LIKE', "%$searchkey_approved_siv%")->orWhere('id LIKE', "%$searchkey_approved_siv%")->orWhere('description LIKE', "%$searchkey_approved_siv%")->groupEnd();
            $approved_sivs = $approved_sivs->findAll(10);
        }
        else
            $approved_sivs = $approved_sivs->findAll(5);

        

        $data['active_menu'] = 'srv_form';
        $data['srv_main'] = $srv_main;
        $data['siv_main'] = $siv_main;
        $data['stores'] = $stores;
        $data['items'] = $items;
        $data['logged_user'] = $loggeduserdata;
        $data['approved_sivs'] = $approved_sivs;
        $data['searchkey_approved_siv'] = $searchkey_approved_siv;
        //$data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();

        return  view('Include/_header',$data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Dispensary/_left_sidebar_dispensary')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Dispensary/srv_form')
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

        $data['active_menu'] = 'srv_form';
        
        return view('Inventory/Dispensary/siv_print', $data);
    }

    public function delete_srv(){
        $srv_id = $_GET['srv_id'];
        $srvMainModel = model('Inventory/SRVMainModel');

        $srv = $srvMainModel->find($srv_id);

        $is_issued = $srv['is_issued'] ?? 1;

        if(!$is_issued)
            $srvMainModel->delete($srv_id);
        
        return redirect()->to('/inventory/dispensary/srv_form'); 
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

        $srv_main['requested_by'] = $requested_by;

        $data = [
            'srv_main' => $srv_main,
            'srv_detail' => $srv_detail,
        ];
        $data['active_menu'] = 'srv_form';
       
        
        return view('Inventory/Dispensary/srv_print', $data);
    }

    public function approve_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        $itemModel = model('Inventory/ItemModel');
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();

        $siv = $sivMainModel->find($siv_id);
        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND, 'recieved_by' => $loggeduserdata['id']]);
            
            $siv_details = $sivDetailModel->where('siv_id', $siv_id)->findAll();
            $transaction_data = [];
            $total_quantity = 0;
            foreach($siv_details as $siv_line){
                $compunding_quantity = $siv_line['quantity'];
                if($siv['requesting_store'] == 3)
                    $compunding_quantity = $siv_line['quantity'] * $siv_line['item_detail']['compounding_volume'];

                $transaction_data[] = [
                    'store_id' => $siv['issuing_store'],
                    'item_id' => $siv_line['item_id'],
                    'batch_number' => $siv_line['batch_no'],
                    'expiry_date' => ($siv_line['expiry_date']=='0000-00-00' ? NULL : $siv_line['expiry_date']),
                    'transaction_type' => BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_INTERNAL,
                    'transaction_reference' => $siv['id'],
                    'quantity' => round(-1 * $siv_line['quantity'], 2),
                    'unit_cost' => $siv_line['average_cost']
                ];

                $transaction_data[] = [
                    'store_id' => $siv['requesting_store'],
                    'item_id' => $siv_line['item_id'],
                    'batch_number' => $siv_line['batch_no'],
                    'expiry_date' => ($siv_line['expiry_date']=='0000-00-00' ? NULL : $siv_line['expiry_date']),
                    'transaction_type' => BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE_INTERNAL,
                    'transaction_reference' => $siv['id'],
                    'quantity' => round( $compunding_quantity, 2),
                    'unit_cost' => $siv_line['average_cost']
                ];
                $total_quantity += $siv_line['quantity'];
            }

            if(!empty($transaction_data))
                $transactionModel->insertBatch($transaction_data);

            $issued_items_id_list = BluelimeSystem::collect_field_value_from_object($siv_details,'item_id');
            $itemModel->whereIn('id', $issued_items_id_list)->update(row:['is_active' => 1]);

            $message = 'You have approved SIV '.$siv['id']."\n# Items: ".count($siv_details)."\n# Total qty: ".$total_quantity;
            $message_detail = '';
            foreach($siv_details as $siv_line){
                $message_detail .= '> '.$siv_line['quantity'].' '.$siv_line['item_detail']['item_brand_name'].' '.$siv_line['item_detail']['item_generic_name']."\n";
            }
            BluelimeSystem::send_sms_message($loggeduserdata['mobile'], $message);
            BluelimeSystem::send_telegram_message($loggeduserdata['mobile'], $message."\n\n".$message_detail);
        }
        return redirect()->to('/inventory/dispensary/siv_print?siv_id='.$siv_id);
    }

    

    public function reverse_approved_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $transactionModel = model('Inventory/TransactionModel');

        $siv = $sivMainModel->find($siv_id);

        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST]);

            $transactionModel->whereIn('transaction_type', [BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_INTERNAL, BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE_INTERNAL])
                ->where('transaction_reference', $siv['id'])
                ->delete();
        }
        return redirect()->to('/inventory/dispensary/siv_print?siv_id='.$siv_id);
    }

    public function stock_card()
    {
        $item_id = $_GET['stock_item_id'];
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        
        $store_id = $_GET['store_id'] ?? 2;
        $stock_cards = BluelimeSystem::get_stock_cards($item_id, $store_id);
        $selected_item = $itemModel->find($item_id);
        $peachtree_items = $peachtreeItemModel->where('item_id',$item_id)->findAll(1);
        $peachtree_id = $peachtree_items[0]['peachtree_id'];

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_ingegratoin = $finance_maintain[0]['data'];
        $peachtree_id .= '_'.$peachtree_ingegratoin['code_suffix']['store'];
        $selected_item['peachtree_id'] = $peachtree_id;

        $store_title = 'Dispensary #'.$peachtree_ingegratoin['branch']['number'].' ('.$peachtree_ingegratoin['branch']['name'].')';

        if($store_id == 3){
            $store_title = 'Compounding #'.$peachtree_ingegratoin['branch']['number'].' ('.$peachtree_ingegratoin['branch']['name'].')';
        }

        $data['stock_cards'] = $stock_cards;
        $data['selected_item'] = $selected_item;
        $data['store_title'] = $store_title;
        $data['active_menu'] = 'inventory';
        
        return view('Inventory/Dispensary/stock_card',$data);
    }

    /*public function pharmacy_grouped_item(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';

        $sales_type = $_GET['sales_type']?? BluelimeConfiguration::SALES_TYPE::DISPENSARY;

        $salesMainModel = model('Inventory/SalesMainModel');

        $saleses = $salesMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->where('sales_type', $sales_type)->findAll();

        $sales_details = BluelimeSystem::gather_objects_by_reference($saleses,'id','Inventory/SalesDetailModel','sales_id');
        
        $salse_report_data = [];
        foreach($sales_details as $sales_detail){
            $index = 'item_'.$sales_detail['item_id'].'_'.$sales_detail['unit_price'];
            if(!isset($salse_report_data[$index])){
                $salse_report_data['item_'.$sales_detail['item_id'].'_'.$sales_detail['unit_price']] = [
                    'item_detail'=>$sales_detail['item_detail'],
                    'unit_price' => $sales_detail['unit_price'],
                    'quantity' => 0,
                    'sub_total' => 0,
                    'vat' => 0,
                    'total' => 0,
                ];
            }

            $salse_report_data[$index]['quantity'] += $sales_detail['quantity'];

            $salse_report_data[$index]['sub_total'] = $salse_report_data[$index]['quantity'] * $salse_report_data[$index]['unit_price'];
            if($sales_detail['item_detail']['is_taxable'])
                $salse_report_data[$index]['vat'] = $salse_report_data[$index]['sub_total'] * 0.15;
            
            $salse_report_data[$index]['total'] = $salse_report_data[$index]['sub_total'] + $salse_report_data[$index]['vat'];
        }

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "sales_report_pharmacy",
            'active_sub_menu'   => "dispensary_grouped_item"
        ];

        if($sales_type == BluelimeConfiguration::SALES_TYPE::COMPOUNDING)
            $data['active_sub_menu'] = 'compounding_grouped_item';

        return view('Inventory/Dispensary/pharmacy_grouped_item', $data);

    }*/
    public function view_prescription_printout(): string
    {
        $prescription_group_id = $_GET['prescription_group_id'] ?? -1;

        $visitModel = model('Hospital/VisitModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $userModel = model('System/UserModel');
        $patientModel = model('Hospital/PatientModel');
        $notificationModel = model('Hospital/NotificationModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        

        //BluelimeSystem::show_and_die($prescriptionModel);
        $prescriptions = $prescriptionModel->where('prescription_group_id',$prescription_group_id)->findAll();
        $prescription_id_list = [...BluelimeSystem::collect_field_value_from_object($prescriptions, 'id'),-1];
        $prescription_detail = $prescriptionDetailModel->whereIn('prescription_id',$prescription_id_list)->findAll();

        $compounding_mains = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->findAll();
        $compounding_details = BluelimeSystem::gather_objects_by_reference($compounding_mains, 'id', 'Hospital/CompoundingPrescriptionDetailModel', 'main_id');

        foreach($compounding_mains as $index => $compounding_main){
            $compounding_mains[$index]['compounding_detail'] = [];
            foreach($compounding_details as $compounding_detail){
                if($compounding_detail['main_id'] == $compounding_main['id'])
                    array_push($compounding_mains[$index]['compounding_detail'], $compounding_detail);
            }
        }

        $notificationModel->whereIn('link_id',$prescription_id_list)->where('notification_type','Prescriptions')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $visit_id = $prescriptions[0]['visit_id'] ?? $compounding_mains[0]['visit_id'];
        $visit = $visitModel->find($visit_id);
        
        $patient_id = $visit['patient_id'];
        $registered_date = $visit['visit_date'];
        $registered_by = $prescriptions[0]['registered_by'] ?? $compounding_mains[0]['created_by'];
        $is_void = $prescriptions[0]['is_void'] ?? $compounding_mains[0]['is_void'];
        $void_date = $prescriptions[0]['updated_at'] ?? $compounding_mains[0]['updated_at'];
        $patient = $patientModel->find($patient_id);
        $doctor = $userModel->find($registered_by);

        $data['prescription_main'] = [
            'id' => $patient_id,
            'registered_date' => $registered_date,
            'is_void' => $is_void,
            'void_date' => $void_date,
            'doctor' => $doctor,
        ];

        $data['compounding_mains'] = $compounding_mains;
        $data['prescription_detail'] = $prescription_detail;
        $data['active_menu'] = 'prescriptions';
        //$data['doc_sign'] = $doc_sign[0];
        $data['patient'] = $patient;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Dispensary/_left_sidebar_dispensary')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Dispensary/view_prescription')
                .view('Include/_footer');     
    }
    public function siv_detail(): string
    {
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');

        $siv_main = $sivMainModel->find($siv_id);

        $issued_by = $userModel->where('id',$siv_main['issued_by'])->findColumn('name');
        $issuing_store_name = $storeModel->where('id',$siv_main['issuing_store'])->findColumn('store_name');
        $requesting_store_name = $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name');
        
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();

        $siv_main['issued_by'] = $issued_by;
        $siv_main['issuing_store_name'] = $issuing_store_name[0];
        $siv_main['requesting_store_name'] = $requesting_store_name[0];
        $data = [
            'siv_main' => $siv_main,
            'siv_detail' => $siv_detail,
        ];
       
        
        $data['active_menu'] = 'storerequest';
        
        return view('Inventory/Dispensary/siv_print', $data);
    }
    public function order_detail(): string
    {
        $sales_id = $_REQUEST['sales_id'] ?? -1;
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $userModel = model('System/UserModel');

        $sales_main = $salesMainModel->find($sales_id);
        $sales_detail = $salesDetailModel->where('sales_id', $sales_id)->findAll();
        foreach($sales_detail as $index => $pre){
            $sales_detail[$index]['customer_name'] = $sales_main['customer_name'];
            $sales_detail[$index]['customer_id'] = $sales_main['customer_id'];
            $sales_detail[$index]['sales_type'] = $sales_main['sales_type'];
        }
        
        $registered_by_name = $userModel->where('id',$sales_main['registered_by'])->findColumn('name');

        $sales_main['registered_by_name'] = $registered_by_name[0];
        //BluelimeSystem::show_and_die($sales_main);
        
        return BluelimeSystem::convert_array_to_string($sales_detail);
    }

    public function new_item(): string
    {
        $search_key_input = $_GET['search_key'] ?? '';
        $search_key_list = explode(' ',$search_key_input);

       
        $itemModel = model('Inventory/ItemModel');
        $measurementModel = model('System/MeasurementModel');
        $itemCategoryModel = model('Inventory/ItemCategoryModel');

        foreach($search_key_list as $search_key){
            if(trim($search_key) != '')
                $itemModel = $itemModel->groupStart()->orWhere('item_code LIKE', "%$search_key%")->orWhere('item_generic_name LIKE', "%$search_key%")->orWhere('item_brand_name LIKE', "%$search_key%")->orWhere('item_category LIKE', "%$search_key%")->groupEnd();
        }

        $items = $itemModel->orderBy('item_brand_name,item_generic_name')->paginate(15);
        $pager = $itemModel->pager;
        $measurements = $measurementModel->findAll();
        $categories = $itemCategoryModel->findAll();
        $item = $itemModel->where('id',@$_GET['iid'])->findAll();
        //BluelimeSystem::show_and_die($measurements[0]);
        $data['active_menu'] = 'new_item_dispensary';
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
        $data['search_key_input'] = $search_key_input;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Dispensary/_left_sidebar_dispensary')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Dispensary/new_item')

        .view('Include/_footer');

             
    }
    public function changeitemstatus()
    {
        $itemModel = model('Inventory/ItemModel');
        
        $item_id = $_POST['item_id'];
        $status = intval($_POST['is_active'])==0? 1: 0;
        //BluelimeSystem::show_and_die(intval($_POST['is_active']));
        $data = [
            'is_active' => $status
        ];

        $itemModel->update($item_id,$data);
    }
    public function save_item()
    {
        $itemModel = model('Inventory/ItemModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];
        
        
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
        else
            $id = $itemModel->insert($data,true);

        return redirect()->to(site_url('/inventory/dispensary/new_item'));
    }

    public function fix_with_old_price(){
        $search_key = $_GET['search_key'] ?? '';

        // $itemModel = model('Inventory/ItemModel')
        // $items = $itemModel->orderBy('item_name')->findAll();

        $items = BluelimeSystem::get_all_items_with_price_and_quantity(1);
        
        $items_j = [];
        foreach($items as $row){
            $items_j["item_id_".$row['id']] = $row;
        }

        $old_item_with_price = [['name'=>'panadol', 'price'=>100]];
        $data = [
            'active_menu' => 'fix_with_old_price',
            'items' => $items_j,
            'old_item_with_price'
        ];
       
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')
        .view('Inventory/Dispensary/_left_sidebar_dispensary')
        .view('Include/_left_sidebar_bottom')
        .view('Inventory/Dispensary/fix_with_old_price')
        .view('Include/_footer');

    }
    public function onlineprescriptions(){
        $onlinePrescriptionModel = model('Hospital/OnlinePrescriptionModel');

        $op = $onlinePrescriptionModel->where('branch_from<>',$_ENV['BRANCH'])->where('branch_to',$_ENV['BRANCH'])->paginate(15);
        $pager = $onlinePrescriptionModel->pager;
       
        $data['active_menu'] = 'sales_form';
        $data['op'] = $op;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();


        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Dispensary/_left_sidebar_dispensary')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Dispensary/online_prescription')
                .view('Include/_footer'); 
        
    }
    public function get_online_prescription()
    {
        $pid = $_GET['id']??-1;
        
        $onlinePrescriptionModel = model('Hospital/OnlinePrescriptionModel');
        $attributes = $onlinePrescriptionModel->where('id',$pid)->findColumn('image')[0];
        
        //BluelimeSystem::show_and_die($attributes);
        
        echo '<img style="width: 590px;"  src="data:image/jpeg;base64,'.base64_encode(  $attributes ?? '' ).'"/>';
    }

    public function inventory(){
        $show_zero = $_GET['show_zero'] ?? 0;
        $store_id = $_GET['store_id'] ?? 2;
        $item_type = strtoupper(trim($_GET['item_type'] ?? ''));
        if(!in_array($item_type, ['DRUG','COSMOTICS']))
            $item_type = '';

        $items = BluelimeSystem::get_all_items_quantity($store_id);

        $inventory_data = [];
        foreach($items as $item){
            if(($item['current_balance'] == 0) && !$show_zero)
                continue;
            if($item_type !== '' && strtoupper(trim($item['item_type'] ?? '')) !== $item_type)
                continue;

            $temp = [
                'id' => $item['id'],
                'item_code' => $item['item_code'],
                'item_generic_name' => $item['item_generic_name'],
                'item_brand_name' => $item['item_brand_name'],
                'preparation' => $item['preparation'],
                'strength' => $item['strength'],
                'current_balance' => $item['current_balance']
            ];
            array_push($inventory_data, $temp);
        }

        $inventory_data = BluelimeSystem::sort_array($inventory_data, ['item_brand_name','item_generic_name']);

        $data = [
            'inventory_data'   => $inventory_data,
            'store_id'   => $store_id,
            'item_type'  => $item_type,
            'active_menu' => 'inventory'
        ];
        return view('Inventory/Dispensary/inventory', $data);
    }
    public function inventory_quantity_on_hand(){
        $store_id = $_GET['store_id'] ?? 0;
        $show_zero = $_GET['show_zero'] ?? 1;

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $peachtree_integration = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $peachtree_integration[0]['data'];

        $store_name = '';
        $peachtree_suffix = '';
        switch ($store_id){
            case 1:
                $store_name = 'Main Store';
                $peachtree_suffix = $peachtree_integration['code_suffix']['store'];
                break;
            case 2:
                $store_name = 'Dispensary';
                $peachtree_suffix = $peachtree_integration['code_suffix']['dispensary'];
                break;
            case 3:
                $store_name = 'Compounding';
                $peachtree_suffix = $peachtree_integration['code_suffix']['compounding'];
                break;
        }

        $items = [];
        if($store_id > 0)
            $items = BluelimeSystem::get_all_items_quantity($store_id);

        $inventory_data = [];
        foreach($items as $item){
            if(($item['current_balance'] == 0) && !$show_zero)
                continue;

            //Compounding
            if(($store_id == 3) && (floatval($item['compounding_volume']) == 0))
                continue;

            $inventory_data[] = $item;
        }

        $peachtree_items = BluelimeSystem::gather_objects_by_reference($inventory_data,'id','Inventory/PeachtreeItemModel','item_id',true);
        foreach($inventory_data as $index => $row){
            $inventory_data[$index]['peachtree_id'] = $peachtree_items['item_id_'.$row['id']]['peachtree_id'].'_'.$peachtree_suffix;
            $inventory_data[$index]['peachtree_description'] = $peachtree_items['item_id_'.$row['id']]['peachtree_description'];
        }

        $data = [
            'store_name' => $store_name,
            'inventory_data' => $inventory_data,
            'store_id' => $store_id,
            'active_menu'=>'reports',
            'active_sub_menu'=>'inventory_quantity_on_hand'
        ];
        return view('Inventory/Dispensary/inventory_qty_on_hand', $data);
    }

    public function inventory_location(){
        $itemModel = model('Inventory/ItemModel');
        $items = $itemModel->findAll();
        $data = [
            'items' => $items,
            'active_menu' => 'inventory_count',
            'active_sub_menu' => 'inventory_location',
        ];
        return view('Inventory/Dispensary/inventory_location', $data);
    }

    public function save_inventory_location(){
        $item_id = $_POST['item_id'];
        $inventory_location = $_POST['inventory_location'];
        $store_id = $_POST['store_id'];
        $logged_user = BluelimeAuthentication::get_logged_user_data();

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
        $store_id = $_GET['store_id'] ?? 2;
        $type = $_GET['type'] ?? '';
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
            //Drug
            if(($type=='') && ($items_with_price_and_qty['id_'.$item_location['item_id']]['item_type'] != 'DRUG'))
                continue;
            //Cosmetic
            if(($type=='cosmetics') && ($items_with_price_and_qty['id_'.$item_location['item_id']]['item_type'] != 'COSMOTICS'))
                continue;
            
            if($type=='compounding')
                $items_with_price_and_qty['id_'.$item_location['item_id']]['measurement'] = $items_with_price_and_qty['id_'.$item_location['item_id']]['compounding_measurement'];

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
            'type' => $type,
            'branch_number' => $peachtree_integration['branch']['number'],
            'store_name' => $store_id == 2 ? 'Dispensary' : 'Compounding',
            'items' => $items,
            'active_menu' => 'inventory_count',
            'active_sub_menu' => 'inventory_count_print',
        ];

        return view('Inventory/Dispensary/inventory_count_print', $data);
    }

    public function credit_receipts(){
        $date_from = $_GET['date_from'] ?? date('Y-m-d');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        // $show_all = $_GET['show_all'] ?? false;

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $sales_mains = $salesMainModel->select('*, "0" as total_amount')->Where('company_id >',0)->where('is_recieved',1)->where('recieved_date >=', $date_from)->where('recieved_date <=', $date_to)->findAll();
        $sales_details = $salesDetailModel->select('sales_id, SUM((quantity*unit_price)+(quantity*vat)) as total_amount')->whereIn('sales_id',[-99,...array_column($sales_mains,'id')])->groupBy('sales_id')->findAll();

        $sales_mains = BluelimeSystem::index_object_list_array_by_unique_field($sales_mains,'id');
        foreach($sales_details as $sales_detail){
            $sales_mains['id_'.$sales_detail['sales_id']]['total_amount'] += $sales_detail['total_amount'];
        }

        $view_data = [
            'active_menu' => 'credit_receipts',
            'sales_mains' => $sales_mains,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ];

        return view('Inventory/Dispensary/credit_receipts',$view_data);
    }
    public function sales_by_item(){
        $date_from = $_GET['date_from'] ?? date('Y-m-d');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        // $show_all = $_GET['show_all'] ?? false;

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $sales_mains = $salesMainModel->select('*, "0" as total_amount')->Where('company_id >',0)->where('is_recieved',1)->where('recieved_date >=', $date_from)->where('recieved_date <=', $date_to)->findAll();
        $sales_details = $salesDetailModel->select('sales_id, SUM((quantity*unit_price)+(quantity*vat)) as total_amount')->whereIn('sales_id',[-99,...array_column($sales_mains,'id')])->groupBy('sales_id')->findAll();

        $sales_mains = BluelimeSystem::index_object_list_array_by_unique_field($sales_mains,'id');
        foreach($sales_details as $sales_detail){
            $sales_mains['id_'.$sales_detail['sales_id']]['total_amount'] += $sales_detail['total_amount'];
        }

        $view_data = [
            'active_menu' => 'credit_receipts',
            'sales_mains' => $sales_mains,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ];

        return view('Inventory/Dispensary/credit_receipts',$view_data);
    }
    public function prescription_by_item(){
        $date_from = $_GET['date_from'] ?? date('Y-m-d');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        // $show_all = $_GET['show_all'] ?? false;

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $sales_mains = $salesMainModel->select('*, "0" as total_amount')->Where('company_id >',0)->where('is_recieved',1)->where('recieved_date >=', $date_from)->where('recieved_date <=', $date_to)->findAll();
        $sales_details = $salesDetailModel->select('sales_id, SUM((quantity*unit_price)+(quantity*vat)) as total_amount')->whereIn('sales_id',[-99,...array_column($sales_mains,'id')])->groupBy('sales_id')->findAll();

        $sales_mains = BluelimeSystem::index_object_list_array_by_unique_field($sales_mains,'id');
        foreach($sales_details as $sales_detail){
            $sales_mains['id_'.$sales_detail['sales_id']]['total_amount'] += $sales_detail['total_amount'];
        }

        $view_data = [
            'active_menu' => 'credit_receipts',
            'sales_mains' => $sales_mains,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ];

        return view('Inventory/Dispensary/credit_receipts',$view_data);
    }

    public function pharmacy_grouped_item(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $sales_type = $_GET['sales_type']?? BluelimeConfiguration::SALES_TYPE::DISPENSARY;
        $item_type = $_GET['item_type'] ?? 'Drug';

        $salse_report_data = $this->create_pharmacy_grouped_item_report_data($from_date, $to_date, $sales_type, $item_type);

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "reports",
            'active_sub_menu'   => "dispensary_grouped_item"
        ];

        if($sales_type == BluelimeConfiguration::SALES_TYPE::COMPOUNDING)
            $data['active_sub_menu'] = 'compounding_grouped_item';

        return view('Inventory/Dispensary/pharmacy_grouped_item', $data);
    }
    public function pharmacy_grouped_item_price(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $sales_type = $_GET['sales_type']?? BluelimeConfiguration::SALES_TYPE::DISPENSARY;
        $item_type = $_GET['item_type'] ?? 'Drug';

        $salse_report_data = $this->create_pharmacy_grouped_item_report_data_price($from_date, $to_date, $sales_type, $item_type);

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "reports",
            'active_sub_menu'   => "dispensary_grouped_item_price"
        ];

        if($sales_type == BluelimeConfiguration::SALES_TYPE::COMPOUNDING)
            $data['active_sub_menu'] = 'compounding_grouped_item';

        return view('Inventory/Dispensary/pharmacy_grouped_item_price', $data);
    }

   private function create_pharmacy_grouped_item_report_data($from_date, $to_date, $sales_type, $item_type ): array{
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $peachtreeItemModle = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        $saleses = $salesMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->where('sales_type', $sales_type)->findAll();
        $sales_details = BluelimeSystem::gather_objects_by_reference($saleses,'id','Inventory/SalesDetailModel','sales_id');
        
        //$pos_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($saleses, 'pos_bill_id'),-1];

        $pOSBillModel = $pOSBillModel->where('response_date >=', substr($from_date,0,10))->where('response_date <=', substr($to_date,0,10))->where('type','Inventory');
        

        $pos_bills = $pOSBillModel->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');
        $saleses = BluelimeSystem::index_object_list_array_by_unique_field($saleses,'id');

        $item_id_list = [...BluelimeSystem::collect_field_value_from_object($sales_details,'item_id'),-99];

        $peachtreeItems = $peachtreeItemModle->whereIn('item_id', $item_id_list)->findAll();
        $peachtreeItems = BluelimeSystem::index_object_list_array_by_unique_field($peachtreeItems, 'item_id');

        $salse_report_data = [];
        foreach($sales_details as $sales_detail){
            $index = 'item_'.$sales_detail['item_id'].'_';
            $pos_bill = $pos_bills['id_'.$saleses['id_'.$sales_detail['sales_id']]['pos_bill_id']] ?? [];

            if(empty($pos_bill))
                continue;

            


            if($sales_detail['item_id'] == 0){
                $index = 'item_'.$sales_detail['item_id'].'__';
                $sales_detail['item_detail']['item_code'] = "CO_".$peachtreeIntegration['code_suffix']['dispensary'];
            }
            else
                $sales_detail['item_detail']['item_code'] = $peachtreeItems['item_id_'.$sales_detail['item_id']]['peachtree_id'].'_'.$peachtreeIntegration['code_suffix']['dispensary'];

            
            if(!isset($salse_report_data[$index])){                
                $salse_report_data[$index] = [
                    'item_detail'=>$sales_detail['item_detail'],
                    'quantity' => 0
                ];
            }

            if($sales_detail['item_id'] == 0){
                $salse_report_data[$index]['quantity'] = 0;
            }

            $salse_report_data[$index]['quantity'] += $sales_detail['quantity'];

        }
        return $salse_report_data;
    }

   private function create_pharmacy_grouped_item_report_data_price($from_date, $to_date, $sales_type, $item_type ): array{
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $peachtreeItemModle = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        $saleses = $salesMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->where('sales_type', $sales_type)->findAll();
        $sales_details = BluelimeSystem::gather_objects_by_reference($saleses,'id','Inventory/SalesDetailModel','sales_id');
        
        //$pos_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($saleses, 'pos_bill_id'),-1];

        $pOSBillModel = $pOSBillModel->where('response_date >=', substr($from_date,0,10))->where('response_date <=', substr($to_date,0,10))->where('type','Inventory');
        

        $pos_bills = $pOSBillModel->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');
        $saleses = BluelimeSystem::index_object_list_array_by_unique_field($saleses,'id');

        $item_id_list = [...BluelimeSystem::collect_field_value_from_object($sales_details,'item_id'),-99];

        $peachtreeItems = $peachtreeItemModle->whereIn('item_id', $item_id_list)->findAll();
        $peachtreeItems = BluelimeSystem::index_object_list_array_by_unique_field($peachtreeItems, 'item_id');

        $salse_report_data = [];
        foreach($sales_details as $sales_detail){
            $index = 'item_'.$sales_detail['item_id'].'_';
            $pos_bill = $pos_bills['id_'.$saleses['id_'.$sales_detail['sales_id']]['pos_bill_id']] ?? [];

            if(empty($pos_bill))
                continue;

            


            if($sales_detail['item_id'] == 0){
                $index = 'item_'.$sales_detail['item_id'].'__';
                $sales_detail['item_detail']['item_code'] = "CO_".$peachtreeIntegration['code_suffix']['dispensary'];
            }
            else
                $sales_detail['item_detail']['item_code'] = $peachtreeItems['item_id_'.$sales_detail['item_id']]['peachtree_id'].'_'.$peachtreeIntegration['code_suffix']['dispensary'];

            
            if(!isset($salse_report_data[$index])){                
                $salse_report_data[$index] = [
                    'item_detail'=>$sales_detail['item_detail'],
                    'unit_price' => $sales_detail['unit_price'],
                    'quantity' => 0,
                    'sub_total' => 0,
                    'vat' => 0,
                    'total' => 0
                ];
            }

            if($sales_detail['item_id'] == 0){
                $salse_report_data[$index]['quantity'] = 0;
                if($salse_report_data[$index]['sub_total']>0)
                    $salse_report_data[$index]['unit_price'] += $sales_detail['unit_price'];
            }

            $salse_report_data[$index]['quantity'] += $sales_detail['quantity'];
            $salse_report_data[$index]['sub_total'] = $salse_report_data[$index]['quantity'] * $salse_report_data[$index]['unit_price'];
            if($sales_detail['item_detail']['is_taxable'])
                $salse_report_data[$index]['vat'] = $salse_report_data[$index]['sub_total'] * 0.15;
            
            $salse_report_data[$index]['total'] = $salse_report_data[$index]['sub_total'] + $salse_report_data[$index]['vat'];
        }
        return $salse_report_data;
    }

    public function inventory_item_with_no_location(){
        $store_id = 2; //$_GET['store_id'] ?? 1;
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

        return view('Inventory/Dispensary/inventory_item_with_no_location', $data);
    }

}
