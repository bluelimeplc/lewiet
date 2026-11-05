<?php

namespace App\Controllers\Cashier;

use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;

class Cashier extends \App\Controllers\BaseController
{
    public function index()
    {
        $logged_user = BluelimeAuthentication::get_logged_user_data();

        if($logged_user['role'] == 'SUPER_ADMIN'){
            $i = $_GET['i'] ?? 0;
            if($i==1)
                $_SESSION['show_inventory'] = true;
            elseif($i==2)
                $_SESSION['show_inventory'] = false;

            $inventory = $_SESSION['show_inventory'] ?? false;
            if($inventory)
                return $this->inventory_sales();
            else
                return $this->hospital_sales();
        }
        elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
            return $this->inventory_sales();
        else
            return $this->hospital_sales();
    }

    public function hospital_sales(): string{
        $search_key = $_GET['search_key'] ?? '';

        $posBillModel = model('POS/POSBillModel');
        if(trim($search_key) != '')
            $posBillModel = $posBillModel->groupStart()->where('CAST(JSON_UNQUOTE(JSON_EXTRACT(pos_bill_json, "$.customer_name")) as CHAR) like', "%$search_key%")->orwhere('CAST(JSON_UNQUOTE(JSON_EXTRACT(pos_bill_json, "$.customer_code")) as CHAR) like', "%$search_key%")->orwhere('fs_number LIKE', "%$search_key%")->orwhere('total LIKE', "%$search_key%")->orwhere('id LIKE', "%$search_key%")->orwhere('response_date LIKE', "%$search_key%")->orwhere('payment_method LIKE', "%$search_key%")->groupEnd();

        $pos_bills = $posBillModel->where('type', 'Hospital')->orderBy('id', 'DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        foreach($pos_bills as $index => $pos_bill){
            $pos_bills[$index]['total'] = $this->calculate_total($pos_bill);
        }

        $data = [
            'pos_bills' => $pos_bills,
            'search_key' => $search_key,
            'pager'=>$posBillModel->pager,
            'active_menu'=>'list',
            'cashier_type' => 'Clinic'
        ];

        //return 'hospital_sales';
        return view('Cashier/sales_list', $data);
    }

    public function inventory_sales(): string{
        $search_key = $_GET['search_key'] ?? '';

        $posBillModel = model('POS/POSBillModel');
        if(trim($search_key) != '')
            $posBillModel = $posBillModel->groupStart()->where('CAST(JSON_UNQUOTE(JSON_EXTRACT(pos_bill_json, "$.customer_name")) as CHAR) like', "%$search_key%")->orwhere('CAST(JSON_UNQUOTE(JSON_EXTRACT(pos_bill_json, "$.customer_code")) as CHAR) like', "%$search_key%")->orwhere('fs_number LIKE', "%$search_key%")->orwhere('total LIKE', "%$search_key%")->orwhere('id LIKE', "%$search_key%")->orwhere('response_date LIKE', "%$search_key%")->orwhere('payment_method LIKE', "%$search_key%")->groupEnd();

        $pos_bills = $posBillModel->where('type', 'Inventory')->orderBy('id', 'DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        foreach($pos_bills as $index => $pos_bill){
            $pos_bills[$index]['total'] = $this->calculate_total($pos_bill);
        }

        $data = [
            'pos_bills' => $pos_bills,
            'search_key' => $search_key,
            'pager'=>$posBillModel->pager,
            'active_menu'=>'list',
            'cashier_type' => 'Pharmacy'
        ];
        
        return view('Cashier/sales_list', $data);
    }

    private function calculate_total($pos_bill):float{
        $total = 0.0;

        foreach($pos_bill['pos_bill_json']['sales_item_list'] as $line)
            $total += $line['quantity'] * ($line['unit_price'] + ($line['vat'] * ($line['unit_price'] * .15)));
        
        return $total;
    }

    public function payment(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();

        if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_HOSPITAL)
            return $this->hospital_paymnet();
        else
            return $this->inventory_paymnet();
    }

    private function hospital_paymnet(){
        $bill_id = $_GET['sales_id'] ?? -1;
        
        $billModel = model('Hospital/BillModel');
        $pOSBillModel = model('POS/POSBillModel');
        $bill = $billModel->find($bill_id);
        $pos_bill_id = $bill['pos_bill_id'];
        $posBill = $pOSBillModel->find($pos_bill_id);
        $pos_bill = $posBill['pos_bill_json'];

        $sales_main = [
            'id' => $bill['id'],
            'patient_name' => $bill['patient_name'],
            'pos_bill' => $pos_bill,
            'sent_to_pos' => $bill['sent_to_pos']
        ];

        $sales_detail = [];
        $line_items = $pos_bill['Line_Items'];

        $payment_detail = [
            'no_of_items' => count($line_items),
            'sub_total' => 0,
            'taxable_total' => 0,
            'vat' => 0,
            'total' => 0
        ];

        foreach($line_items as $row){
            array_push($sales_detail, ['item_name'=>$row['Item_Description'], 'quantity'=>$row['Item_Quantity'], 'price'=>$row['Item_Unit_Price']]);
            if($row['Item_Tax_Percent'] > 0){
                $payment_detail['taxable_total'] += $row['Item_Quantity'] * $row['Item_Unit_Price'];
                $payment_detail['vat'] = $row['Item_Quantity'] * $row['Item_Unit_Price'] * $row['Item_Tax_Percent'] / 100;
            }
            else
                $payment_detail['sub_total'] += $row['Item_Quantity'] * $row['Item_Unit_Price'];
        }

        $payment_detail['total'] = $payment_detail['sub_total'] + $payment_detail['taxable_total'] + $payment_detail['vat'];

        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $data['sales_main'] = $sales_main;
        $data['sales_detail'] = $sales_detail;
        $data['payment_detail'] = $payment_detail;
        $data['logged_user'] = $logged_user;
        $data['active_menu'] = 'list';

        return view('Cashier/send_to_pos', $data);
    }

    private function inventory_paymnet(){
        $pos_bill_id = $_GET['sales_id'] ?? -1;
       
        $pOSBillModel = model('POS/POSBillModel');

        $posBill = $pOSBillModel->find($pos_bill_id);
        $pos_bill_json = $posBill['pos_bill_json'];

        $payment_detail = [
            'no_of_items' => count($pos_bill_json['sales_item_list']),
            'sub_total' => 0,
            'taxable_total' => 0,
            'vat' => 0,
            'total' => 0
        ];
        
        $item_list = $pos_bill_json['sales_item_list'];
        foreach($item_list as $item){
            if($item['vat'] == 0)
                $payment_detail['sub_total'] += $item['unit_price'] *  $item['quantity'];
            else
                $payment_detail['taxable_total'] += $item['unit_price'] *  $item['quantity'];
            if($item['vat'])
                $payment_detail['vat'] += $item['unit_price'] * $item['quantity'] * 0.15;

            $payment_detail['total'] += $item['quantity'] * ($item['unit_price'] + ($item['unit_price'] * $item['vat'] * 0.15));
        }

        $data=['payment_detail' => $payment_detail,'pos_bill'=>$posBill, 'active_menu'=>'list',];
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $data['logged_user'] = $logged_user;

        return view('Cashier/send_to_pos', $data);
    }

    public function attachment(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();

        if($logged_user['role'] == 'SUPER_ADMIN'){
            $i = $_GET['i'] ?? 0;
            if($i==1)
                $_SESSION['show_inventory'] = true;
            elseif($i==2)
                $_SESSION['show_inventory'] = false;

            $inventory = $_SESSION['show_inventory'] ?? false;
            if($inventory)
                return $this->inventory_attachment();
            else
                return $this->hospital_attachment();
        }
        elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
            return $this->inventory_attachment();
        else
            return $this->hospital_attachment();
    }

    private function hospital_attachment(){
        $pos_bill_id = $_GET['pos_bill_id'] ?? -1;

        [$pos_bill, $sales_main, $sub_total, $payment] = BluelimePOS::get_attachmwnt_data_hospital($pos_bill_id);

        $data = [
            'sales_main' => $sales_main,
            'sub_total' => $sub_total,
            'taxable_total' => 0,
            'total_vat' => 0,
            'total' => $sub_total,
            'payment' => $payment,
            'pos_bill' => $pos_bill
        ];

        return view('Cashier/attachment_hospital', $data);
    }

    private function inventory_attachment(){
        $pos_bill_id = $_GET['pos_bill_id'] ?? -1;

        [$pos_bill, $sales_mains, $sub_total, $taxable_total, $total_vat, $total] = BluelimePOS::get_attachmwnt_data_invetory($pos_bill_id);

        $data = [
            'pos_bill' => $pos_bill,
            'sales_mains' => $sales_mains,
            'sub_total' => $sub_total,
            'taxable_total' => $taxable_total,
            'total_vat' => $total_vat,
            'total' => $total,
        ];
        $new = $_GET['new'] ?? 0;
        if($new)
            return view('Cashier/attachment_summerized', $data);

        return view('Cashier/attachment', $data);
    }


    public function send_to_pos(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();

        if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_HOSPITAL)
            return $this->send_to_pos_hospital();
        else
            return $this->send_to_pos_inventory();
    }

    private function send_to_pos_hospital(){
        $customer_name = $_POST['customer_name'];
        $tin = $_POST['tin'];
        $paymnet_method = $_POST['paymnet_method'];
        $pos_bill_id = $_POST['pos_bill_id'];

        $billModel = model('Hospital/BillModel');
        $pOSBillModel = model('POS/POSBillModel');

        $posBill = $pOSBillModel->find($pos_bill_id);
        $pos_bill_json = $posBill['pos_bill_json'];
        $pos_bill_json['Customer_Name'] = $customer_name;
        $pos_bill_json['Customer_TIN'] = $tin;
        $pos_bill_json['Payment_Type'] = $paymnet_method;

        $success = BluelimePOS::send_to_pos($pos_bill_json);
        if($success){
            $pos_bill_json = BluelimeSystem::convert_array_to_string($pos_bill_json);
            $pOSBillModel->update($pos_bill_id, ['pos_bill_json' => $pos_bill_json]);
            $bill_type = 'Cash';
            if($paymnet_method == 'Credit')
                $bill_type = 'Credit';

            $billModel->where('pos_bill_id', $pos_bill_id)->update(row:['sent_to_pos' => 1, 'bill_type' => $bill_type]);
            return BluelimeSystem::get_success_ajax_return(true);
        }
    }

    private function send_to_pos_inventory(){
        $customer_name = $_POST['customer_name'];
        $tin = $_POST['tin'];
        $paymnet_method = $_POST['paymnet_method']??'Credit';
        $pos_bill_id = $_POST['pos_bill_id'];

        $pOSBillModel = model('POS/POSBillModel');
        $salesMainModel = model('Inventory/SalesMainModel');

        $posBill = $pOSBillModel->find($pos_bill_id);
        $posBill['pos_bill_json']['customer_name'] = $customer_name;
        $posBill['pos_bill_json']['tin'] = $tin;
        
        $success = BluelimePOS::send_to_pos($posBill);
        
        if($success){
            $pos_bill_json = $posBill['pos_bill_json'];
            $pos_bill_json = BluelimeSystem::convert_array_to_string($pos_bill_json);
            $pOSBillModel->update($pos_bill_id, ['pos_bill_json' => $pos_bill_json, 'sent_to_pos_date' => BluelimeSystem::date_now(), 'payment_method'=>$paymnet_method]);
            $salesMainModel->where('pos_bill_id',$pos_bill_id)->update(row:['sent_to_pos' => 1]);
            return BluelimeSystem::get_success_ajax_return(true);
        }
        return BluelimeSystem::get_error_ajax_return(false);
    }

    public function update_payment_method(){
        $payment_method = $_POST['payment_method'];
        $transferred_amount = $_POST['transferred_amount'];
        $payment_reference = $_POST['payment_reference'];
        $pos_bill_id = $_POST['pos_bill_id'];
        $total = $_POST['total'];

        if($payment_method == BluelimeConfiguration::PAYMENT_METHOD::CASH)
            $transferred_amount = $total;

        if($payment_method == BluelimeConfiguration::PAYMENT_METHOD::CBE)
            $payment_reference = $this->get_payment_reference_for_cbe($payment_reference);

        $pOSBillModel = model('POS/POSBillModel');
        $update_data = [
            'payment_method'=>$payment_method,
            'transferred_amount'=>$transferred_amount,
            'payment_reference'=>$payment_reference
        ];

        if($pOSBillModel->update($pos_bill_id, $update_data))
            return $this->response->setJSON(BluelimeSystem::get_success_ajax_return('updated'));
        
        return $this->response->setJSON(BluelimeSystem::get_error_ajax_return('failed'));
    }

    private function get_payment_reference_for_cbe($payment_reference){
        $url_segments = explode('/', $payment_reference);
        if(($url_segments[0]??'') == 'https:'){
            $token = $url_segments[count($url_segments)-1];

            $url = 'https://Mb.cbe.com.et/api/v1/transactions/public/transaction-detail/'.$token;
            $header = [
                        "X-App-ID:d1292e42-7400-49de-a2d3-9731caa4c819",
                        "X-App-Version:0a01980b-9859-1369-8198-59f403820000"
            ];
            
            $response = BluelimeSystem::send_curl_request($url, $header);
            if ($response === false) {
                return $payment_reference;
            } else {
                $response = json_decode($response, true);
                $payment_reference = $response['id'] ?? $payment_reference;
            }
        }
        return $payment_reference;
    }

    public function update_name(){
        $customer_name = $_POST['customer_name'];
        $pos_bill_id = $_POST['pos_bill_id'];

        $pOSBillModel = model('POS/POSBillModel');

        $pos_bill = $pOSBillModel->find($pos_bill_id);
        $pos_bill_json = $pos_bill['pos_bill_json'];
        $pos_bill_json['customer_name'] = $customer_name;

        $pOSBillModel->update($pos_bill_id, ['pos_bill_json' => BluelimeSystem::convert_array_to_string($pos_bill_json)]);
        return BluelimeSystem::get_success_ajax_return('updated');
    }

    public function dashboard(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $response_date = $_GET['filter_date'] ?? date('Y-m-d');
        $receipt_type = $_GET['receipt_type'] ?? 'Cash Regiter';

        $pOSBillModel = model('POS/POSBillModel');
        $cashierSummaryModel = model('POS/CashierSummaryModel');

        

        $type = '';
        if(($logged_user['role'] == 'SUPER_ADMIN') || $logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE){
            $i = $_GET['i'] ?? 0;
            if($i==1)
                $_SESSION['show_inventory'] = true;
            elseif($i==2)
                $_SESSION['show_inventory'] = false;

            $inventory = $_SESSION['show_inventory'] ?? false;
            if($inventory)
                $type = 'Inventory';
            else
                $type = 'Hospital';
        }
        elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
            $type = 'Inventory';
        else
            $type = 'Hospital';

        $payment_method_reflection = new \ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
        $payment_methods = $payment_method_reflection->getConstants();
        
        $payment_report = [];
        foreach($payment_methods as $payment_method){
            $payment_report[$payment_method] = ['qty' => 0, 'amount' => 0];
        }

        $pOSBillModel = $pOSBillModel->where('type', $type)->where('response_date', $response_date);

        if($receipt_type == "Manual")
            $pOSBillModel = $pOSBillModel->where('response_data', 'Manual');
        else
            $pOSBillModel = $pOSBillModel->where('response_data !=', 'Manual');

        $pos_bills = $pOSBillModel->findAll();

        $total_count = 0;
        $total_amount = 0;
        $cash_partial = 0;
        foreach($pos_bills as $pos_bill){
            $payment_method = $pos_bill['payment_method'];
            if(!isset($payment_report[$payment_method]))
                $payment_report[$payment_method] = ['qty' => 0, 'amount' => 0];
            
            $total = $this->calculate_total($pos_bill);
            $payment_report[$payment_method]['qty']++;
            $transferred_amount = floatval($pos_bill['transferred_amount']);
            if($transferred_amount > 0){
                $cash_partial += $total - floatval($pos_bill['transferred_amount']);
                $total = $transferred_amount;
            }
            $payment_report[$payment_method]['amount'] += $total;
            $total_amount += $total;
            $total_count++;
        }
        $payment_report['Cash']['amount'] += $cash_partial;
        $total_amount += $cash_partial;

        $payment_report['TOTAL'] = ['qty' => $total_count, 'amount' => $total_amount];

        $payment_report_temp = [];
        $cashless_total = 0;
        $cashless_count = 0;
        foreach($payment_report as $index => $line){
            if(($index == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($index == 'TOTAL') || ($index == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                continue;
            $payment_report_temp[$index] = $line;
            $cashless_count += $line['qty'];
            $cashless_total += $line['amount'];
        }

        $payment_report_temp['BANK TOTAL'] = ['qty' => $cashless_count, 'amount' => $cashless_total];

        $payment_report_temp['Credit'] = $payment_report['Credit'];
        $payment_report_temp['Cash'] = $payment_report['Cash'];
        $payment_report_temp['TOTAL'] = $payment_report['TOTAL'];

        $payment_report = $payment_report_temp;

        // VOID Receipts

        $pOSBillModel = $pOSBillModel->where('type', $type)->where('response_date', $response_date)->where('is_void',1);
        if($receipt_type == "Manual")
            $pOSBillModel = $pOSBillModel->where('response_data', 'Manual');
        else
            $pOSBillModel = $pOSBillModel->where('response_data !=', 'Manual');
        
        $void_pos_bills = $pOSBillModel->onlyDeleted()->findAll();

        $void_reciepts = ['qty'=>0,'amount'=>0];
        foreach($void_pos_bills as $row){
            $void_reciepts['qty']++;
            $void_reciepts['amount'] +=$row['total'];
        }
        
        $payment_report['void_reciepts'] = $void_reciepts;

        $cashier_type = 'Clinic';
        if($type == 'Inventory')
            $cashier_type = 'Pharmacy';
        
        $cashier_summary = $cashierSummaryModel->where('summary_of',$cashier_type)->where('summary_date',$response_date)->findAll();

        $error_found = false;
        $fs_list = $pOSBillModel->select('fs_number,is_void,response_data')->where('type', $type)->where('response_date', $response_date)->findAll();
        $void_fs_list = $pOSBillModel->select('fs_number,is_void,response_data')->where('type', $type)->where('response_date', $response_date)->where('is_void',1)->onlyDeleted()->findAll();
        $fs_list = [...$fs_list, ...$void_fs_list];
        $fs_list = BluelimeSystem::sort_array($fs_list,['fs_number', 'is_void']);
        $prev_fs_list = [];
        foreach($fs_list as $row){
            if($row['response_data'] == 'Manual')
                $row['fs_number'] = intval(trim(str_ireplace('M','',$row['fs_number'])));
            else
                $row['fs_number'] = intval($row['fs_number']);

            if(empty($prev_fs_list)){
                $prev_fs_list = $row;
                continue;
            }

            if(($row['fs_number'] - $prev_fs_list['fs_number']) != 1){
                if((($row['response_data'] == 'Manual') && ($prev_fs_list['response_data'] == 'Manual')) || (($row['response_data'] != 'Manual') && ($prev_fs_list['response_data'] != 'Manual'))){
                    if((!$row['is_void']) && (!$prev_fs_list['is_void'])){
                        $error_found = true;
                        break;
                    }
                }
            }

            $prev_fs_list = $row;
        }
        
        $data = [
            'active_menu'=>'cashierdashboard',
            'filter_date' => $response_date,
            'receipt_type' => $receipt_type,
            'payment_report' => $payment_report,
            'cashier_type' => $cashier_type,
            'cashier_summary' => $cashier_summary,
            'error_found' => $error_found,
            'fs_list' => $fs_list
        ];

        return view('Cashier/dashboard', $data);
    }
    
    public function dashboard_detail(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $response_date = $_GET['filter_date'] ?? date('Y-m-d');

        $pOSBillModel = model('POS/POSBillModel');

        $type = '';
        if(($logged_user['role'] == 'SUPER_ADMIN') || $logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE){
            $i = $_GET['i'] ?? 0;
            if($i==1)
                $_SESSION['show_inventory'] = true;
            elseif($i==2)
                $_SESSION['show_inventory'] = false;

            $inventory = $_SESSION['show_inventory'] ?? false;
            if($inventory)
                $type = 'Inventory';
            else
                $type = 'Hospital';
        }
        elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
            $type = 'Inventory';
        else
            $type = 'Hospital';


        $pos_bills = $pOSBillModel->where('type', $type)->groupStart()->where('response_date', $response_date)->orLike('updated_at', "$response_date")->groupEnd()->orderBy('id','DESC')->findAll();
        //$pos_bills = $pOSBillModel->where('type', $type)->groupStart()->where('response_date', $response_date)->groupEnd()->orderBy('id','DESC')->findAll();

        foreach($pos_bills as $index => $pos_bill){
            $total = $this->calculate_total($pos_bill);
            $pos_bills[$index]['total'] = $total;
        }

        $pos_bills = BluelimeSystem::sort_array($pos_bills, ['fs_number']);
        
        $cashier_type = 'Clinic';
        if($type == 'Inventory')
            $cashier_type = 'Pharmacy';

        $data = [
            'active_menu'=>'cashierdashboard',
            'filter_date' => $response_date,
            'pos_bills' => $pos_bills,
            'cashier_type' => $cashier_type
        ];

        return view('Cashier/dashboard_detail', $data);
    }

    public function manual_reciept(){
        $pos_bill_id = $_POST['pos_bill_id'];
        $reciept_no = $_POST['reciept_no'];
        $reciept_type = $_POST['reciept_type'];
        $payment_date = $_POST['payment_date'].' '.date('H:i:s');

        $response_data = 'Cash regiter maual posting';
        if($reciept_type == 'Manual'){
            $reciept_no = 'M '.$reciept_no;
            $response_data = 'Manual';
        }

        $pOSBillModel = model('POS/POSBillModel');
        $pos_bill = $pOSBillModel->find($pos_bill_id);
        if(empty($pos_bill))
            return BluelimeSystem::get_error_ajax_return('The invoice is not found.');

        $invoice_no = $pos_bill['invoice_no'];
        // $fs_number = trim($pos_bill['fs_number']);
        // if(empty($fs_number)){
            BluelimePOS::update_sales_response($invoice_no,$reciept_no, $response_data,machine_number:'',payment_date:$payment_date);
            return BluelimeSystem::get_success_ajax_return('Paid');
        // }
        // else
        //     return BluelimeSystem::get_error_ajax_return('Invoice already paid.');
    }

    public function void_payment(){
        $pos_bill_id = $_POST['pos_bill_id'];
        $remark = $_POST['remark'];
        $pOSBillModel = model('POS/POSBillModel');

        $pos_bill = $pOSBillModel->find($pos_bill_id);
        
        if($pos_bill['type'] == 'Inventory')
            $result = $this->void_payment_inventory($pos_bill, $remark);
        else
            $result = $this->void_payment_clinic($pos_bill, $remark);

        return $result;
    }

    private function void_payment_inventory($pos_bill, $remark){
        $pos_bill_id = $pos_bill['id'];
        $pOSBillModel = model('POS/POSBillModel');
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $transactionModel = model('Inventory/TransactionModel');

        $sales_id_list = [...$salesMainModel->where('pos_bill_id',$pos_bill_id)->findColumn('id'),-99];
        
        $transactionModel->whereIn('transaction_reference',$sales_id_list)->delete();
        $salesDetailModel->whereIn('sales_id', $sales_id_list)->delete();
        $salesMainModel->whereIn('id', $sales_id_list)->delete();
        $pOSBillModel->update($pos_bill_id, ['payment_reference' => "VOID\n$remark", 'is_void'=>1]);
        $pOSBillModel->delete($pos_bill_id);
        
        return BluelimeSystem::get_success_ajax_return('');
    }

    private function void_payment_clinic($pos_bill, $remark){
        $pos_bill_id = $pos_bill['id'];
        $pOSBillModel = model('POS/POSBillModel');
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');

        $bill_id_list = [...$billModel->where('pos_bill_id', $pos_bill_id)->findColumn('id'),-99];
        $user_id = $billModel->whereIn('id', $bill_id_list)->findColumn('registered_by');
        $user_id = $user_id[0];

        $billDetails = $billDetailModel->whereIn('bill_id',$bill_id_list)->where('service_type !=', BluelimeConfiguration::SERVICE_TYPE::LABORATORY)->where('service_status',0)->findAll();
        

        $visitModel->whereIn('bill_id',$bill_id_list)->delete();
        $billModel->whereIn('id', $bill_id_list)->delete();
        $pOSBillModel->update($pos_bill_id, ['payment_reference' => "VOID\n$remark", 'is_void'=>1]);
        $pOSBillModel->delete($pos_bill_id);

        $user = $userModel->find($user_id);

        if(!empty($user)){
            if($user['role'] == BluelimeConfiguration::USER_ROLE::DOCTOR){
                $paid_services = '';
                $total = 0;
                foreach($billDetails as $billDetail){
                    $paid_services .= $billDetail['service_name'].', ';
                    $total += ($billDetail['bill_quantity']*$billDetail['bill_amount'] * ((100-$billDetail['bill_discount'])/100));
                }
                echo $paid_services;
                if(BluelimeSystem::date_difference(date('Y-m-d'), $pos_bill['response_date']) < 60){
                    if($total > 100){
                        $message = "Hello ".$user['name']."\nA payment receipt is voided.\n\nID: ".$pos_bill['invoice_no']."\nPayment Date: ".$pos_bill['response_date']."\nServices: ".$paid_services."\nTOTAL: ".$total." ETB";
                        BluelimeSystem::send_telegram_message($user['mobile'], $message);
                    }
                }
            }
        }

        return BluelimeSystem::get_success_ajax_return('');
    }

    public function newdocumentupload_mobile()
    {
        
        $filter_date = $_GET['filter_date'];
        $cashier_type = $_REQUEST['cashier_type'];

        
        $data['filter_date'] = $filter_date;
        $data['cashier_type'] = $cashier_type;
        
        return view('Cashier/new_cashier_document_mobile', $data);
    }

    
    function delete_cashier_document(){
        $image_name = $_POST['image_name'];
        $filter_date = $_POST['filter_date'] ?? -1;
        
        $cashierSummaryModel = model('POS/CashierSummaryModel');
        $cashier_summary = $cashierSummaryModel->where('summary_date',$filter_date)->where('summary_status',0)->find()[0];
       
        $attached_cashier_document = $cashier_summary['cashier_document'];
        $attached_cashier_document = trim(str_replace($image_name,'',$attached_cashier_document),',');
        //BluelimeSystem::show_and_die($attached_cashier_document);
        $image_path = $_ENV['CASHIER_DOCUMENT_PATH'].$image_name;

        $cashierSummaryModel->where('summary_date',$filter_date)->set(['cashier_document'=>$attached_cashier_document])->update();

        if(is_file($image_path)){
            unlink($image_path);
        }
        return BluelimeSystem::get_success_ajax_return('');
    }
    public function addcashiersummary()
    {
        $filter_date     = $_POST['filter_date'];
        $this->upload_cashier_document();

        return redirect()->to(site_url('/cashier/dashboard?filter_date='.$filter_date));
    }

    public function addcashiersummary_mobile()
    {
        $filter_date     = $_POST['filter_date'];
        $cashier_type     = $_POST['cashier_type'];
        $image_list = $this->upload_cashier_document();

        return redirect()->to(site_url('/cashier/newdocumentupload_mobile?filter_date='.$filter_date.'&image_list='.$image_list.'&cashier_type='.$cashier_type));
    }

    private function upload_cashier_document($quality = -1){
        $cashierSummaryModel = model('POS/CashierSummaryModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $image_list = trim($_POST['image_list'] ?? '');
        $cashier_name     = $loggeduserdata['name']??'';
        $deposited_total     = $_POST['deposited_total'] ?? '';
        $z_total     = $_POST['z_total']?? '';
        $cashier_id     = $loggeduserdata['id']??-1;
        $filter_date     = $_POST['filter_date'];
        $cashier_type     = $_POST['cashier_type'];
        if($z_total=='' && $deposited_total==''){
            $data = [
                'cashier_name' => $cashier_name,
                'cashier_id'    => $cashier_id,
                'summary_of'    => $cashier_type,
                'summary_date'    => $filter_date
        ];
        }
        else{
        
            $data = [
                'cashier_name' => $cashier_name,
                'cashier_id'    => $cashier_id,
                'deposited_total'    => $deposited_total,
                'z_total'    => $z_total,
                'summary_of'    => $cashier_type,
                'summary_date'    => $filter_date
            ];
        }

        if($cashier_id==-1){
            unset($data['cashier_id']);
            unset($data['cashier_name']);
        }


        $found = $cashierSummaryModel->where('summary_of',$cashier_type)->where('summary_date',$filter_date)->find()[0]??[];
        
        if($found){
            $id = $cashierSummaryModel->where('summary_status',0)->update($found['id'],$data);
        }
        else{
            $id = $cashierSummaryModel->insert($data,true);            
        }
        if(isset($_FILES['cashier_document'])){
        $originalImage = $_FILES['cashier_document']['tmp_name'];
        
        $fl1Name = $filter_date.'_'.date('Ymd').'_'.sprintf('%05d', $id);

        $file_path = $_ENV['CASHIER_DOCUMENT_PATH'];

        if(is_uploaded_file($originalImage))
        {
            $i = 0;
            
            while(file_exists($file_path.$fl1Name.'.jpg'))
                $fl1Name = $fl1Name.'_'.$i;
            
            //move_uploaded_file($originalImage,$_ENV['MANUAL_CARD_PATH'].$fl1Name.'.jpg');

            switch (exif_imagetype($originalImage)) {
                case IMAGETYPE_PNG:
                    $imageTmp=imagecreatefrompng($originalImage);
                    break;
                case IMAGETYPE_JPEG:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
                case IMAGETYPE_GIF:
                    $imageTmp=imagecreatefromgif($originalImage);
                    break;
                case IMAGETYPE_BMP:
                    $imageTmp=imagecreatefrombmp($originalImage);
                    break;
                // Defaults to JPG
                default:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
            }
            // TODO Rotet image if it is not correct

            imagejpeg($imageTmp,$file_path.$fl1Name.'.jpg',$quality);
            imagedestroy($imageTmp);
            $img = $cashierSummaryModel->where('summary_of',$cashier_type)->where('summary_date',$filter_date)->find()[0];
            $upload_img = $img['cashier_document']==""?$fl1Name.'.jpg':$img['cashier_document'].','.$fl1Name.'.jpg';
            
            $cashierSummaryModel->update($img['id'], ['cashier_document' => $upload_img]);
            if($image_list != '')
                $image_list .= ',';

            $image_list .= $fl1Name.'.jpg';

        }
        return $image_list;
    }
    }
    public function cashierdocument(){
        ob_clean();
        header('Content-type: image');

        $image = $_GET['image'];
        $thumbnail = $_GET['thumbnail'] ?? false;
        $file_path = $file_path = $_ENV['CASHIER_DOCUMENT_PATH'];

        $im = imagecreatefromjpeg($file_path.$image);
        if($thumbnail)
            $im = imagescale($im,1000);
        imagejpeg($im, null,100);
        imagedestroy($im);
        die();
    }

    public function digital_payment(){
        $method = $_POST['method'];
        $mobile = $_POST['mobile'];
        $pos_bill_id = $_POST['pos_bill_id'];

        $posBillModel = model('POS/POSBillModel');
        $pos_bill = $posBillModel->find($pos_bill_id);
        $amount = $pos_bill['total'];
        $digital_payment_detail = $pos_bill['digital_payment_detail'] ?? '[]';
        $digital_payment_detail = BluelimeSystem::convert_to_array($digital_payment_detail);


        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $peachtree_integration = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->first()['data'];
        $branch_number = $peachtree_integration['branch']['number'] ?? '-1';
        
        //sleep(1);
        $random_number = rand(10,99);
        $payment_id = "RANK-{$branch_number}-".date('ymdHis').$random_number."-{$pos_bill_id}";
        $reason = "Rank Clinic Payment";
        if($pos_bill['type'] == 'Inventory')
            $reason = "Gelead Pharmacy Payment";

        $payment_init = ['method'=>$method, 'mobile'=>$mobile, 'payment_id'=>$payment_id, 'amount'=>$amount, 'date'=>BluelimeSystem::date_now()];
        $digital_payment_detail['initialized'] ??= [];

        //reset payment to 1 ETB for testing, remove this line in production
        $amount = 1;
        $santimpay_api_key = $_ENV['API_KEY_SANTIMPAY'];
        $url = "https://api.rankderma.com.et/paymentgateway/webhook/$method/$santimpay_api_key/generate";
        //$url = "http://api.rankderma.dev2/paymentgateway/webhook/$method/{$santimpay_api_key}/generate";
        $data = ['mobile' => $mobile, 'amount' => $amount, 'payment_id' => $payment_id, 'reason' => $reason];
        $result = BluelimeSystem::communicate_api2(__METHOD__, $url,null, $data);
        $result = BluelimeSystem::convert_to_array($result);
        if(empty($result) || !isset($result['status']) || ($result['status'] != 'success')){
            $payment_init['error'] = $result['message'] ?? 'Failed to generate payment request.';
            $payment_init['payment_url'] = '';
            $digital_payment_detail['initialized'][] = $payment_init;

            $posBillModel->update($pos_bill_id, ['digital_payment_detail' => BluelimeSystem::convert_array_to_string($digital_payment_detail)]);
            return $this->response->setJSON(['status'=>'error','message'=>'Failed to generate payment request.']);
        }

        $payment_init['payment_url'] = $result['payment_url'] ?? 'Error: Payment URL not found.';
        $payment_init['payment_url_short'] = $result['payment_url_short'] ?? 'Error: Payment URL not found.';
        $digital_payment_detail['initialized'][] = $payment_init;
        $posBillModel->update($pos_bill_id, ['digital_payment_detail' => BluelimeSystem::convert_array_to_string($digital_payment_detail),'digital_payment_status' => 'pending']);

        if($payment_init['payment_url_short'] != 'Error: Payment URL not found.'){
            BluelimeSystem::send_telegram_message($mobile, "$reason\n".$payment_init['payment_url_short'].'?t=telegram');
            BluelimeSystem::send_sms_message($mobile, "$reason\n".$payment_init['payment_url_short'].'?t=sms');
        }
        return $this->response->setJSON(['status'=>'success','value'=>$result]);
    }
}