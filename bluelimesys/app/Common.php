<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;

 /** 
 * HOSPITAL,DENTAL,INVENTORY
 * */
define( 'BLULIME_DEFAULT_APP', 'HOSPITAL');
define('ENV_CONSULTATION_SERVICE_ID',$_ENV['ENV_CONSULTATION_SERVICE_ID']??1);
define('FISCAL_YEAR_BEGINIG',BluelimeSystem::get_fiscal_year_range()[0]);
define('FISCAL_YEAR_ENDING',BluelimeSystem::get_fiscal_year_range()[1]);

class FINANCE_MAINTAIN_TYPE {
    public const DAILY_SALES_BY_PAYMENT_TYPE = 'DAILY_SALES_BY_PAYMENT_TYPE';
    public const PEACHTREE_INTEGRATION = 'PEACHTREE_INTEGRATION';
    public const CREDIT_INVOICE_API = 'CREDIT_INVOICE_API';
}

class PAYMENT_METHOD {
    public const ABAY = 'Abay';
    public const ARIF_PAY = 'Arif Pay';
    public const AWASH = 'Awash';
    public const BERHAN = 'Berhan';
    public const BOA = 'BOA';
    public const CASH = 'Cash';
    public const CBE = 'CBE';
    public const COOP = 'COOP';
    public const ENAT = 'Enat';
    public const NIB = 'Nib';
    public const POS = 'POS';
    public const POS_AWASH = 'POS Awash';
    public const POS_BERHAN = 'POS Berhan';
    public const POS_BOA = 'POS BOA';
    public const POS_CBE = 'POS CBE';
    public const POS_ENAT = 'POS Enat';
    public const POS_ZEMEN = 'POS Zemen';
    public const SANTIM_PAY = 'Santim Pay';
    public const TSEHAY = 'Tsehay Bank';
    public const TELE_BIRR = 'Telebirr';
    public const CREDIT = 'Credit';
}

class SALES_TYPE {
    public const COMPOUNDING = "COMPOUNDING";
    public const DISPENSARY = "DISPENSARY";
}
class INVENTORY_TRANSACTION_TYPE {
    public const RECEIVE = 'RECEIVE';
    public const RECEIVE_INTERNAL = 'RECEIVE_INTERNAL';
    public const DISPENSE_INTERNAL = 'DISPENSE_INTERNAL';
    public const DISPENSE_SALES = 'DISPENSE_SALES';
    public const TRANSFER_OUT = 'TRANSFER_OUT';
    public const TRANSFER_IN = 'TRANSFER_IN';
    public const COMPOUNDING_SALES = 'COMPOUNDING_SALES';
    public const ADJUSTMENT = 'ADJUSTMENT';
    public const ADJUSTMENT_BEGINNING = 'ADJUSTMENT_BEGINNING';
    #public const BEGINIG = 'BEGINIG';
    public const BEGINNING = 'BEGINNING';
}

class USER_ROLE_EXTRA {
    public const REPORT_VIEWER = 'REPORT_VIEWER';
    public const DELETE_PENDING_BILL = 'DELETE_PENDING_BILL';
    public const POST_PAST_SALES = 'POST_PAST_SALES';
    public const FINANCE_SUPER = 'FINANCE_SUPER';
    public const SHOW_ON_APPOINTMENT = 'SHOW_ON_APPOINTMENT';
}

class USER_ROLE {
    public const STORE = 'STORE';
    public const DOCTOR = 'DOCTOR';
    public const LABORATORY = 'LABORATORY';
    public const RECEPTION = 'RECEPTION';
    public const NURSE = 'NURSE';
    public const ADMIN = 'ADMIN';
    public const FINANCE = 'FINANCE';
    public const DISPENSARY = 'DISPENSARY';
    public const COMPOUNDING = 'COMPOUNDING';
    public const CASHIER_INVENTORY = 'CASHIER_INVENTORY';
    public const CASHIER_HOSPITAL = 'CASHIER_HOSPITAL';
    public const CALL_CENTER = 'CALL_CENTER';
    public const PATHOLOGY = 'PATHOLOGY';
    public const MIS = 'MIS';
}

class SERVICE_TYPE {
    public const LABORATORY = 'Laboratory';
    public const PATHOLOGY = 'Pathology';
    public const CONSULTATION = 'Consultation';
    public const PROCEDURE = 'Procedure';
}

class VOUCHER_SATUS {
    public const PENDING = 0;
    public const APPROVED_FIRST = 1;
    public const APPROVED_SECOND = 2;
    public const APPROVED_THIRD = 3;
    public const EXPORTED_FOR_PEACHTREE = 9;
}

class BluelimeConfiguration{
    public const VAT = 0.15;
    public const VISIT_STATUS_NEW = 0;
    public const VISIT_STATUS_OPEN = 1;
    public const VISIT_STATUS_CLOSED = 2;
    public const VISIT_STATUS_VITAL_TAKEN = 3;
    
    public const BILL_PENDING           = 0;
    public const BILL_OPEN              = 1;
    public const BILL_CLOSED            = 2;
    public const BILL_REMOVED           = 3;    
    public const BILL_RECIEVED          = 4;
    public const BILL_SENT_TO_POS       = 5;
    public const BILL_REMOVE_REQUESTED  = 6;

    public const APPOINTMENT_ACTIVE = 1;
    public const APPOINTMENT_MISSED = 2;
    public const APPOINTMENT_VISITED = 3;
    public const APPOINTMENT_LATE_VISIT = 4;
    public const APPOINTMENT_CANCELED = -1;
    public const APPOINTMENT_PENDING = 0;
    public const NOTIFICATION_NEW = 0;
    public const NOTIFICATION_SEEN = 1;
    public const DEFAULT_PRICE_LIST_ID = 1;
    public const DISCOUNT_PRICE_PERCENT_ID = -3;
    public const DISCOUNT_PRICE_AMOUNT_ID = -2;
    public const CONSULTATION_SERVICE_ID = ENV_CONSULTATION_SERVICE_ID;
    public const SERVICE_STATUS_DELAY = -1;
    public const UNPAID_VISIT_DEFAULT_DAYS = 15;
    public const LAB_RESULT_PENDING = 0;
    public const LAB_RESULT_APPROVED = 1;
    public const LAB_ORDER_PENDING = 0;
    public const LAB_ORDER_PAID = 1;
    public const INVENTORY_TRANSACTION_TYPE = INVENTORY_TRANSACTION_TYPE::class; 
    public const USER_ROLE = USER_ROLE::class; 
    public const USER_ROLE_EXTRA = USER_ROLE_EXTRA::class; 
    public const SERVICE_TYPE = SERVICE_TYPE::class; 
    public const SALES_TYPE = SALES_TYPE::class; 
    public const PAYMENT_METHOD = PAYMENT_METHOD::class; 
    public const VOUCHER_SATUS = VOUCHER_SATUS::class; 
    public const FINANCE_MAINTAIN_TYPE = FINANCE_MAINTAIN_TYPE::class; 
    public const NUMBER_OF_ROW_OF_LIST = 20;
    public const BLUELIME_API_KEY = '1a84fbb9e02814b0c6f04525acce83b1_CHANGED';

    public const EMAIL_CONFIG = [
        'SMTPHost' => 'pathology.rankderma.com',
        'SMTPUser' => 'noreply@pathology.rankderma.com',
        'SMTPPass' => 'JpJ62i^Rk8m_=R1x',
        'SMTPPort' => 465,
        'protocol' => 'smtp',
        'mailType' => 'html'
    ];
}



class BluelimeError{
    /*
        E_1_001
        W_1_001

        E Error
        W Warning

          1 Exception type
            3 POS Error
            2 Bill error
        001 Error number 
    */
    private static $erorr_list = [
        "E_4_002" => 'SMS error. Invalid mobile number.',
        "E_4_001" => 'SMS error. Security error.',

        "E_3_001" => 'Cannot send invoice to POS machine.',
        "E_3_002" => 'Cannot void invoice from POS machine.',

        "E_2_001" => 'Cannot delete already paid invoice.',
        "E_2_002" => 'Cannot delete from POS.',
    ];

    public static function get_message(string $error_code):string{
        return self::$erorr_list[$error_code] ?? '';
    }
}

class BluelimeSystem
{
    public static $Authentication = BluelimeAuthentication::class;
    public static $Config = BluelimeConfiguration::class;

    /**
     * Author: @abiygirma
     * It open a file and append the log. If there is no file it will create it
     *
     * @param string $location_in_writable_log
     * @param string $file_name
     * @param string $log
     * @return void
     */
    public static function write_log(string $location_in_writable_log, string $file_name, $log, string $title = '', bool $auto_header = true, bool $return_file_handler = false)
    {
        $file_name = $file_name . '_' . date('Y-m-d') . '.log';
        try{
            $log_file = fopen(WRITEPATH.'logs/'.$location_in_writable_log."/".$file_name, 'a');
        }
        catch (\Exception $e){
            if(!is_dir(WRITEPATH.'logs/'.$location_in_writable_log))
                mkdir(WRITEPATH.'logs/'.$location_in_writable_log,0777,true);
            $log_file = fopen(WRITEPATH.'logs/'.$location_in_writable_log."/".$file_name, 'w');
        }

        $header = "\n\n==================================================\n";
        $header .= date("Y-m-d H:i:s"). " (".time().")";
        if ($title != '')
            $header .= "\n--------------------------------\n" . $title;
        $header .= "\n--------------------------------------------------\n";

        if($auto_header)
            fwrite($log_file,$header);

        if(is_array($log))
            $log = BluelimeSystem::convert_array_to_string($log);

        fwrite($log_file,$log);
        if($return_file_handler)
            return $log_file;

        fclose($log_file);
        return true;
    }

    public static function write_bluelime_debug_log(string $location_in_writable_log, string $file_name, string|array $log, string $title = '', bool $auto_header = true)
    {
        if(self::bluelime_debug_is_on())
            self::write_log($location_in_writable_log, $file_name, $log, $title, $auto_header);
    }

    public static function bluelime_debug_is_on(): bool
    {
        if($_ENV["BLUELIME_DEV"]??false)
            return true;
        return false;
    }

    /**
     * Author: Abiy G
     * It is used to display the content of a variable and exit php
     *
     * @param mixed $var   Any variable that will be displyed by var_dump()
     * @param boolean $die
     * @return void
     */
    public static function show_and_die($var, $die = true)
    {
        var_dump($var);
        
        if($die)
            die();
    }
    public static function split_full_name($fullName) {
        
        $names = preg_split('/\s+/', trim($fullName));
        
        $firstName = '';
        $middleName = '';
        $lastName = '';

        $count = count($names);

        if ($count == 1) {
            $firstName = $names[0];
        } elseif ($count == 2) {
            list($firstName, $lastName) = $names;
        } elseif ($count >= 3) {
            $firstName = array_shift($names); // First element is the first name
            $lastName = array_pop($names);    // Last element is the last name
            $middleName = implode(' ', $names); // Remaining elements form the middle name(s)
        }

        return [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName
        ];
    }


    public static function print_associated_array($array, $die = true){

        echo '<table border="1">
                <tr>
                    <th>Index</th><th>Value</th>
                </tr>';
        foreach($array as $index => $value){
            if(is_array($value)){
                echo '
                <tr>
                    <td>' . $index . '</td>
                    <td>';
                        self::print_associated_array($value, false);
                echo '</td>
                </tr>';
            }
            else{
                echo '
                <tr>
                    <td>' . $index . '</td><td>' . $value . '</td>
                </tr>';
            }
        }

        echo '</table>';

        if($die)
            die();
    }

    /**
     * Make simple changes to fix mobile number.
     *
     * @param string $str
     * @return string
     * 
     * Author: Abiy G
     */
    public static function correct_mobile($mobile): string{
        $mobile = trim($mobile);
        $mobile = str_ireplace(' ', '', $mobile);
        $mobile = @intval($mobile);
        $mobile = "0$mobile";

        $new_mobile = '';
        if(strlen($mobile) == 10)
            $new_mobile = $mobile;
        return $new_mobile;
    }
    public static function patient_information(int $patient_id): string
    {
        $patientModel = model('Hospital/PatientModel');
        $patient_info = $patientModel->where('id',$patient_id)->findAll();
        //BluelimeSystem::show_and_die($patient_info[0]['first_name']);
        $patient_name = $patient_info[0]['first_name']." ".$patient_info[0]['middle_name']." ".$patient_info[0]['last_name'];
        return $patient_name;
    }
    public static function get_patient_id(int $visit_id): string
    {
        $visitModel = model('Hospital/VisitModel');
        $patient_id = $visitModel->where('id',$visit_id)->findColumn('patient_id')[0];
        //BluelimeSystem::show_and_die($patient_info[0]['first_name']);
        return $patient_id;
    }
    public static function convert_to_array(string|null $str): array
    {
        if(empty($str))
            $str = '';
        
        $array_val = [];
        if(trim($str) !=='')
        {
            @$json_obj = json_decode($str,true);
            if(is_array($json_obj))
                $array_val = $json_obj;
        }
        
        return $array_val;
    }

    /**
     * Converts array to a string, and can be stored in a table
     *
     * @param [type] $val
     * @return string
     * 
     * Author: Abiy G
     */
    public static function convert_array_to_string($val): string
    {
        if(!is_array($val))
            $val = [];
        $str_val = (!empty($val)) ? json_encode($val) : '[]' ;
        return $str_val;
    }

    public static function move_file_safely(string $from_path, string $to_path){
        $file_detail = pathinfo($to_path);
        $new_file_name = $file_detail['basename'];
        $cnt = 1;
        while(file_exists($file_detail['dirname'] . '/' . $new_file_name)){
            $new_file_name = $file_detail['filename'] . '_' . $cnt++ . '.' . $file_detail['extension'];
        }
        $to_path = $file_detail['dirname'] . '/' . $new_file_name;

        if(file_exists($from_path))
            rename(from: $from_path, to:$to_path);
    }

    public static function sort_array(array $sortable_array, array $sorting_fields, bool $DESC=false): array{
        $sorted_array = [];
        while(count($sortable_array) > 0){
            $chosen_index = -1;
            foreach($sortable_array as $index => $row){
                if($chosen_index == -1){
                    $chosen_index = $index;
                    continue;
                }

                $left = $sortable_array[$index];
                $right = $sortable_array[$chosen_index];
                if($DESC){
                    $right = $sortable_array[$index];
                    $left = $sortable_array[$chosen_index];
                }

                $left_data = '';
                $right_data = '';
                foreach($sorting_fields as $sorting_field){
                    $left_data .= trim(strtolower($left[$sorting_field]));
                    $right_data .= trim(strtolower($right[$sorting_field]));
                }
                if($left_data < $right_data)
                    $chosen_index = $index;
            }

            $sorted_array[$chosen_index] = $sortable_array[$chosen_index];
            unset($sortable_array[$chosen_index]);
        }
        return $sorted_array;
    }

    public static function get_success_ajax_return($value):string
    {
        if(is_array($value))
            $value = self::convert_array_to_string($value);
        else
            $value = '"'.$value.'"';

        $return_json = '{"status":"success","value":'.$value.'}';
        return $return_json;
    }

    public static function get_error_ajax_return(string $message):string
    {
        $return_json = '{"status":"error","message":"'.$message.'"}';
        return $return_json;
    }

    /**
     * Gather/Collect data from model by matching a field
     *
     * @param array $search_in
     * @param string $search_field
     * @param string $model_name
     * @param string $model_field
     * @return array
     * 
     * Author: @abiygirma
     */
    public static function gather_objects_by_reference(array $search_in, string $search_field, string $model_name, string $model_field, bool $index_by_model_field = false): array{
        $object_id_list = self::collect_field_value_from_object($search_in,$search_field);
        
        $model = model($model_name);
        
        $gathered_objects = [];

        if(!empty($object_id_list))
            $gathered_objects = $model->whereIn($model_field, $object_id_list)->findAll();
        
        if($index_by_model_field){
            // $gathered_objects_temp = [];
            // foreach($gathered_objects as $index => $row){
            //     $gathered_objects_temp[$model_field.'_'.$row[$model_field]] = $row;
            // }
            $gathered_objects = self::index_object_list_array_by_unique_field($gathered_objects, $model_field);
        }

        return $gathered_objects;
    }
    public static function collect_field_value_from_object(array $search_in, string $search_field): array{
        $object_id_list = [];
        foreach($search_in as $row)
            array_push($object_id_list,$row[$search_field]);
        
        $object_id_list = array_unique($object_id_list);
        return $object_id_list;
    }

    public static function index_object_list_array_by_unique_field(array $object_list, string $indexing_field_name): array{
        $objects_temp = [];
        foreach($object_list as $index => $row){
            $objects_temp[$indexing_field_name.'_'.$row[$indexing_field_name]] = $row;
        }
        return $objects_temp;
    }

    public static function get_latest_price_list(int $store_id=2):array{
        //Store_id must always be despensary
        $store_id = 2;
        
        $priceListModel = model('Inventory/PriceListModel');

        $query = "SELECT effective_dates FROM (SELECT MAX(effective_date) as effective_dates FROM `inventory_pricelist` WHERE `effective_date` <= '".date('Y-m-d')."' AND `store_id` = '".$store_id."' AND `inventory_pricelist`.`deleted_at` IS NULL GROUP BY `item_id`) as dates GROUP BY effective_dates;";

        $result = $priceListModel->db->query($query)->getResultArray();

        $effective_dates = [date('Y-m-d')];
        foreach($result as $row)
            array_push($effective_dates, $row['effective_dates']);

        $price_list = $priceListModel->whereIn('effective_date', $effective_dates)->where('store_id',$store_id)->orderBy('item_id', 'ASC')->orderBy('effective_date', 'DESC')->orderBy('id', 'DESC')->findAll();

        $latest_price_list = [];
        $prv_item_id = -1;
        foreach($price_list as $row){
            if($row['item_id'] != $prv_item_id)
                array_push($latest_price_list, $row);
            $prv_item_id = $row['item_id'];
        }
        
        return $latest_price_list;
    }

    public static function get_latest_price_for_item(int $item_id):float{
        //Store_id must always be despensary
        $store_id = 2;
        
        $priceListModel = model('Inventory/PriceListModel');

        $latest_price_row = $priceListModel->select('MAX(id) as id')->where('item_id',$item_id)->findAll(1);
        $priceList_id = $latest_price_row[0]['id']??-99;
        $latest_price_row = $priceListModel->find($priceList_id);
        
        $unit_price = $latest_price_row['unit_price']??0;
        
        return $unit_price;
    }

    public static function get_all_items_with_price(int $store_id): array{
        $itemModel = model('Inventory/ItemModel');
        
        $latest_price_list = self::get_latest_price_list($store_id);
        $items_temp = $itemModel->orderBy('id', 'DESC')->findAll();
        $items = [];
        
        $last_inserted_index = 0;
        foreach($items_temp as $item){
            $item['unit_price'] = 0;
            foreach($latest_price_list as $row){
                if($row['item_id'] == $item['id']){
                    $item['unit_price'] = $row['unit_price'];
                    break;
                }
            }
            /*
            for($last_inserted_index; $last_inserted_index < count($latest_price_list); $last_inserted_index++){
                if($item['id'] == $latest_price_list[$last_inserted_index]['item_id']){
                    $item['unit_price'] = $latest_price_list[$last_inserted_index]['unit_price'];
                    break;
                }
                if($item['id'] < $latest_price_list[$last_inserted_index]['item_id']){
                    break;
                }
            }
            */

            $items['item_id_'.$item['id']] = $item;
        }
        return $items;
    }

    public static function get_all_items_with_price_and_quantity(int $store_id, bool $include_inactive = false): array{
        
        $itemModel = model('Inventory/ItemModel');
        $transactionModel = model('Inventory/TransactionModel');
        
        $item_quantity = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('created_at >=',FISCAL_YEAR_BEGINIG)->where('created_at <=',FISCAL_YEAR_ENDING)->where('store_id', $store_id)->groupBy('item_id')->orderBy('item_id', 'ASC')->findAll();
        
        $latest_price_list = self::get_latest_price_list($store_id);

        if(!$include_inactive)
            $itemModel = $itemModel->where('is_active', 1);

        $items_temp = $itemModel->orderBy('id', 'ASC')->findAll();
        $items = [];
        
        $last_inserted_unit_price_index = 0;
        $last_inserted_quantity_index = 0;
        foreach($items_temp as $item){
            $item['unit_price'] = 0;
            $item['current_balance'] = 0;
            $item['compounding_unit_price'] = 0;
            
            for($last_inserted_unit_price_index; $last_inserted_unit_price_index < count($latest_price_list); $last_inserted_unit_price_index++){
                if($item['id'] == $latest_price_list[$last_inserted_unit_price_index]['item_id']){
                    $item['unit_price'] = $latest_price_list[$last_inserted_unit_price_index]['unit_price'];
                    if($item['compounding_volume'] > 0)
                        $item['compounding_unit_price'] = $item['unit_price'] / $item['compounding_volume'];
                    break;
                }
                if($item['id'] < $latest_price_list[$last_inserted_unit_price_index]['item_id']){
                    break;
                }
            }
                
            for($last_inserted_quantity_index; $last_inserted_quantity_index < count($item_quantity); $last_inserted_quantity_index++){
                if($item['id'] == $item_quantity[$last_inserted_quantity_index]['item_id']){
                    $item['current_balance'] = $item_quantity[$last_inserted_quantity_index]['quantity'];
                }
                if($item['id'] < $item_quantity[$last_inserted_quantity_index]['item_id']){
                    break;
                }
            }

            $items['item_id_'.$item['id']] = $item;
        }
        //BluelimeSystem::print_associated_array($items);
        $items = self::sort_array($items, ['item_generic_name', 'item_brand_name']);
        //sort($items,SORT_ASC);
        return $items;
    }

    public static function get_all_items_quantity(int $store_id,array $item_id_list=[]): array{

        $itemModel = model('Inventory/ItemModel');
        $transactionModel = model('Inventory/TransactionModel');
        
        $transactionModel = $transactionModel->select('item_id, SUM(quantity) as quantity')
                                ->where('created_at >=',FISCAL_YEAR_BEGINIG)
                                ->where('created_at <=',FISCAL_YEAR_ENDING)
                                ->where('store_id', $store_id);
        
        if(!empty($item_id_list))
            $transactionModel = $transactionModel->whereIn('item_id',$item_id_list);                        
        
        $item_quantity = $transactionModel->groupBy('item_id')
                                ->orderBy('item_id', 'ASC')
                                ->findAll();

        $item_quantity = BluelimeSystem::index_object_list_array_by_unique_field($item_quantity, 'item_id');

        $items_temp = $itemModel->orderBy('id', 'ASC')->findAll();
        $items = [];
        
        foreach($items_temp as $item){
            $item['current_balance'] = $item_quantity['item_id_'.$item['id']]['quantity'] ?? 0;
            $items['item_id_'.$item['id']] = $item;
        }
        //BluelimeSystem::print_associated_array($items);
        //$items = self::sort_array($items, ['item_brand_name','item_generic_name']);
        //sort($items,SORT_ASC);
        return $items;
    }

    public static function get_available_item_batch_expiry_date(int $store_id){
        $transactionModel = model('Inventory/TransactionModel');
        $item_expirys = $transactionModel->select('item_id, batch_number, MIN(expiry_date) as expiry_date, SUM(quantity) as qty')
            ->where('created_at >=', FISCAL_YEAR_BEGINIG)
            ->where('created_at <=', FISCAL_YEAR_ENDING)
            ->where('store_id', $store_id)
            ->groupBy(['item_id', 'batch_number'])
            ->having('qty > 0')
            ->orderBy('item_id', 'ASC')
            ->orderBy('expiry_date', 'ASC')
            ->findAll();
        return $item_expirys;
    }

    public static function get_stock_cards(int $item_id, int $store_id){
        $transactionModel = model('Inventory/TransactionModel');
        $storeModel = model('Inventory/StoreModel');
        $grvMainModel = model('Inventory/GRVMainModel');
        $sivMainModel = model('Inventory/SIVMainModel');
        //$itemModel = model('Inventory/ItemModel');
        $supplierModel = model('Inventory/SupplierModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $priceListModel = model('Inventory/PriceListModel');
        
        $suppliers = $supplierModel->findAll();
        $suppliers = BluelimeSystem::index_object_list_array_by_unique_field($suppliers,'id');
        $stores = $storeModel->findAll();
        $grvs = $grvMainModel->findAll();
        $sivs = $sivMainModel->findAll();

        $transactions = $transactionModel->where('store_id', $store_id)->where('item_id',$item_id)->findAll();

        foreach($transactions as $index => $transaction){
            $transactions[$index]['unit_price'] = $priceListModel->where('item_id', $item_id)->where('effective_date <=', substr($transaction['created_at'],0,10))->orderBy('effective_date','DESC')->findColumn('unit_price')[0] ?? 0;

            if($transaction['transaction_type']=="RECEIVE"){
                $grvDetail = $grvDetailModel->where('grv_id', $transaction['transaction_reference'])->where('item_id', $item_id)->findAll();
                
                foreach($grvs as $grv){
                        if($grv['id'] == $transaction['transaction_reference']){
                            $transactions[$index]['transaction_code'] = $grv['grv_code'];
                            $transactions[$index]['supplier_name'] = $suppliers['id_'.$grv['supplier_id']]['company_name'] ?? '';
                            $transactions[$index]['unit_cost'] = $grvDetail[0]['unit_price'];
                            break;
                    }
                }
            }
            else{
                foreach($sivs as $siv){
                        if($siv['id'] == $transaction['transaction_reference']){
                            $transactions[$index]['transaction_code'] = $siv['siv_code'];
                            break;
                    }
                }
            }

            foreach($stores as $store){
                if($store['id'] == $transaction['store_id']){
                    $transactions[$index]['store_name'] = $store['store_name'];
                    break;
                }
            }
          
        }

        $current_fiscal_period = self::get_fiscal_year_range();
        $current_fiscal_period = $current_fiscal_period[0].' - '.$current_fiscal_period[1];
        $stock_cards = [];
        foreach($transactions as $transaction){
            $fiscal_period = self::get_fiscal_year_range($transaction['created_at']);
            $fiscal_period_index = $fiscal_period[0].' - '.$fiscal_period[1];

            if(!isset($stock_cards[$fiscal_period_index])){
                $stock_cards[$fiscal_period_index] = ['ending_balance'=>0,'transactions'=>[], 'fiscal_beginig'=>$fiscal_period[0],'fiscal_ending'=>$fiscal_period[1], 'is_current_fiscal_period'=>($current_fiscal_period==$fiscal_period_index)?true:false];
            }

            $stock_cards[$fiscal_period_index]['ending_balance'] += $transaction['quantity'];
            $stock_cards[$fiscal_period_index]['transactions'][] = $transaction;
        }

        $stock_cards = self::sort_array($stock_cards , ['fiscal_beginig']);

        foreach($stock_cards as $index => $stock_card){
            $stock_cards[$index]['transactions'] = self::sort_array($stock_card['transactions'],['created_at','id']);
        }

        return $stock_cards;
    }


    public static function count_my_unsent_inventory_sales(){
        $salesMainModel = model('Inventory/SalesMainModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $sales_main = $salesMainModel->where('registered_by', $loggeduser['id'])->where('sent_to_pos', 0)->findColumn('COUNT(id)');
        $count = $sales_main[0] ?? 0;
        return $count;
    }
    /**
     * Match a search key in an object for the specified fields. If it matchs one returns true.
     *
     * @param string $search_key
     * @param array $object
     * @param array $fields
     * @param boolean $case_insensitive
     * @return void
     */
    public static function search_in_fields(string $search_key, array $object, array $fields, bool $case_insensitive = true) : bool{
        if(empty($search_key))
            return false;
        
        if($case_insensitive)
            $search_key = strtolower($search_key);

        foreach($fields as $field){
            $value = $object[$field];
            if(empty($value))
                continue;

            if($case_insensitive)
                $value = strtolower($value);
            
            if(str_contains($value, $search_key))
                return true;
        }
        return false;
    }

    public static function search_index_in_array(string $search_key, array $object, string $field, bool $case_insensitive = true){
        if(empty($search_key))
            return null;
        
        if($case_insensitive)
            $search_key = strtolower($search_key);

        foreach($object as $index => $row){
            $value = $row[$field];
            if(empty($value))
                continue;

            if($case_insensitive)
                $value = strtolower($value);
            
            if($value == $search_key)
                return $index;
        }
        return null;
    }

    /**
     * Calculated the number of days between two given dates.
     *
     * @param string $date1
     * @param string $date2
     * @return integer  Number of days
     * 
     * Author: @abiygirma
     */
    public static function date_difference(string $date1, string $date2): int
    {
        
        //$month = date('m',strtotime($date2));
        //$year = date('Y',strtotime($date2));
        //$dim=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        
        
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);

        
        $interval = $date1->diff($date2);
        $day_difference = $interval->d + (365 * $interval->y) + (30 * $interval->m) + 1;

        return $day_difference;
    }
    public static function year_difference(string $date1, string $date2): int
    {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);

        $interval = $date1->diff($date2);
        $day_difference = $interval->y;

        return $day_difference;
    }
    public static function month_difference(string $date1, string $date2): int
    {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);

        $interval = $date1->diff($date2);
        $day_difference = $interval->m;

        return $day_difference;
    }
    public static function calculate_age(string $date_of_birth){
        return self::year_difference($date_of_birth, date('Y-m-d'));
    }

    /**
     * Converts ec date to gc
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return string
     */
    public static function convert_ec_to_gc(int $year, int $month, int $day): string | bool
    {
        if(!self::validate_ec($year, $month, $day))
            return false;
        
        $meskerem_1_in_gc = self::get_first_year_date_of_gc_for_ec($year);
        $number_of_days = self::calculate_number_of_days_of_ec($month, $day);

        $date_in_gc =  date_add(date_create($meskerem_1_in_gc), date_interval_create_from_date_string($number_of_days . " days"));
        return date_format($date_in_gc, "Y-m-d");
    }

    /**
     * Accepts year month and day and validate interms of ethiopian calendar
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return bool
     */
    private static function validate_ec(int $year, int $month, int $day): bool
    {
        if(($year == 0) || ($month == 0) || ($day == 0))
            return false;
        
        if($month > 13)
            return false;
        if($day > 30)
            return false;
        
        $pagume = 5;

        if(($year % 4) == 3)
            $pagume = 6;

        if(($month == 13) && ($day > $pagume))
            return false;

        return true;
    }

    /**
     * Return Meskerem 1 date of the year
     *
     * @param int $year
     * @return string
     */ 
    private static function get_first_year_date_of_gc_for_ec(int $year) : string
    {
        $year += 7;
        
        //September 
        $month = '09';

        $date = 11;

        if(($year % 4) == 3)
            $date += 1;

        $first_day = $year . "-" . $month . "-" . $date;

        return $first_day;   
    }

    /**
     * Calculate number of days from Meskerem 1 of that year
     *
     * @param int $month
     * @param int $day
     * @return int
     */
    private static function calculate_number_of_days_of_ec(int $month, int $day): int
    {
        $number_of_days = ($month - 1) * 30;
        $number_of_days += $day - 1;

        return $number_of_days;
    }

    /**
     * Summary of get_fiscal_year_range
     * @param mixed $date
     * @return string[begining_date, ending_date]
     */
    public static function get_fiscal_year_range($date = ''): array{
        if(empty($date))
            $date = date('Y-m-d');

        $year = intval(date_format(date_create($date),"Y"));

        $begining_date = '';
        $ending_date = '';
        if($date > "$year-07-07 23:59:59"){
            $begining_date = "$year-07-08";
            $ending_date = ($year+1)."-07-07";
        }
        else{
            $begining_date = ($year-1)."-07-08";
            $ending_date = "$year-07-07";
        }

        return ["$begining_date 00:00:00","$ending_date 23:59:59"];
    }

    public static function get_fiscal_period_list(int $ec_year): array{
        $gc_year = $ec_year + 7;
        $period_list = [
            "p1"  => ['from'=> $gc_year.'-07-08 00:00:00', 'to'=> $gc_year.'-08-06 23:59:59'],
            "p2"  => ['from'=> $gc_year.'-08-07 00:00:00', 'to'=> $gc_year.'-09-10 23:59:59'],
            "p3"  => ['from'=> $gc_year.'-09-11 00:00:00', 'to'=> $gc_year.'-10-10 23:59:59'],
            "p4"  => ['from'=> $gc_year.'-10-11 00:00:00', 'to'=> $gc_year.'-11-09 23:59:59'],
            "p5"  => ['from'=> $gc_year.'-11-10 00:00:00', 'to'=> $gc_year.'-12-09 23:59:59'],
            "p6"  => ['from'=> $gc_year.'-12-10 00:00:00', 'to'=> ++$gc_year.'-01-08 23:59:59'],
            "p7"  => ['from'=> $gc_year.'-01-09 00:00:00', 'to'=> $gc_year.'-02-07 23:59:59'],
            "p8"  => ['from'=> $gc_year.'-02-08 00:00:00', 'to'=> $gc_year.'-03-09 23:59:59'],
            "p9"  => ['from'=> $gc_year.'-03-10 00:00:00', 'to'=> $gc_year.'-04-08 23:59:59'],
            "p10" => ['from'=> $gc_year.'-04-09 00:00:00', 'to'=> $gc_year.'-05-08 23:59:59'],
            "p11" => ['from'=> $gc_year.'-05-09 00:00:00', 'to'=> $gc_year.'-06-07 23:59:59'],
            "p12" => ['from'=> $gc_year.'-06-08 00:00:00', 'to'=> $gc_year.'-07-07 23:59:59']
        ];
        return $period_list;
    }

    public static function get_accounting_period($date):string{
        $period_list = [
            "1"  => ['from'=> '0708', 'to'=> '0806'],
            "2"  => ['from'=> '0807', 'to'=> '0910'],
            "3"  => ['from'=> '0911', 'to'=> '1010'],
            "4"  => ['from'=> '1011', 'to'=> '1109'],
            "5"  => ['from'=> '1110', 'to'=> '1209'],
            "6"  => ['from'=> '1210', 'to'=> '1231'],
            "6 "  => ['from'=> '0101', 'to'=> '0108'],
            "7"  => ['from'=> '0109', 'to'=> '0207'],
            "8"  => ['from'=> '0208', 'to'=> '0309'],
            "9"  => ['from'=> '0310', 'to'=> '0408'],
            "10" => ['from'=> '0409', 'to'=> '0508'],
            "11" => ['from'=> '0509', 'to'=> '0607'],
            "12" => ['from'=> '0608', 'to'=> '0707']
        ];

        $month_and_date = date("md",strtotime($date));

        foreach($period_list as $period => $date_range){
            if((intval($month_and_date) >= intval($date_range['from'])) && (intval($month_and_date) <= intval($date_range['to'])))
                return trim($period);
        }
        return '_E';
    }

    public static function get_accounting_period_last_date($date){
        $last_date = [
            "1"=>'08-06',
            "2"=>'09-10',
            "3"=>'10-10',
            "4"=>'11-09',
            "5"=>'12-09',
            "6"=>'01-08',
            "7"=>'02-07',
            "8"=>'03-09',
            "9"=>'04-08',
            "10"=>'05-08',
            "11"=>'06-07',
            "12"=>'07-07',
        ];

        $period = self::get_accounting_period($date);
        $time_value = strtotime($date);
        $year = date("Y", $time_value);
        $month = intval(date("m", $time_value));
        //Some times we need to add one year to make the end date fall on Jan
        if((intval($period) == 6) && ($month == 12))
            $year += 1;
        return "{$year}-{$last_date[$period]}";
    }
    
    public static function set_alert(string $body_html, string $command = "OK", string $alert_type = "INFO", string $title = "Alert", bool $autoHide = false){
        $session = \Config\Services::session();
        $_SESSION['dental_alert'] = [
            "body_html"     =>  $body_html,
            "command"       =>  $command,
            "alert_type"    =>  $alert_type,
            "title"         =>  $title,
            "autoHide"      =>  $autoHide
        ];
    }

    public static function get_alert() : array{
        $session = \Config\Services::session();
        $alert = $_SESSION['dental_alert'] ?? [];
        $_SESSION['dental_alert'] = [];
        return $alert;
        //Good
    }

    public static function day_to_no($day){
        if($day == "Sat"){
            return $day = 6;
        }
        elseif ($day == "Sun") {
            return $day = 7;
        }elseif ($day == "Mon") {
            return $day = 1;
        }elseif ($day == "Tue") {
            return $day = 2;
        }elseif ($day == "Wed") {
            return $day = 3;
        }elseif ($day == "Thu") {
            return $day = 4;
        }else {
            return $day = 5;
        }
    }

    public static function no_to_day($day){
        if($day == 6){
            return $day = "Sat";
        }
        elseif ($day == 7) {
            return $day = "Sun";
        }elseif ($day == 1) {
            return $day = "Mon";
        }elseif ($day == 2) {
            return $day = "Tue";
        }elseif ($day == 3) {
            return $day = "Wed";
        }elseif ($day == 4) {
            return $day = "Thu";
        }else {
            return $day = "Fri";
        }
    }

    
    public static function date_now() : string {
        return date("Y-m-d H:i:s");
    }

    public static function create_jwt($data, $status, $api_key){
        $signature = self::create_jwt_signature($data,$api_key);
        $jwt = [
            "status" => $status,
            "data"   => $data,
            "signature" => $signature
        ];

        return json_encode($jwt);
    }

    public static function create_jwt_signature($data, $api_key){
        if(is_array($data))
            $data = json_encode($data);
        $signature = self::get_jwt_signature($data,$api_key);
        return $signature;
    }

    public static function check_jwt_signature($data, $api_key, $signature):bool{
        if(is_array($data))
            $data = json_encode($data);
        $new_signature = self::get_jwt_signature($data,$api_key);
        if($new_signature == $signature)
            return true;
        return false;
    }
    public static function get_jwt_signature($data, $api_key):string{
        if(is_array($data))
            $data = json_encode($data);
        $signature = $data.$api_key.date('Ymd').$_ENV['SECRET_KEY_V2'];
        $signature = hash('sha256', $signature);
        return $signature;
    }


    /**
     * Changes string value to array object for given columns. This should be called from a model class.
     * the "data" must be an array that is generated from find or findAll.
     *
     * @param array $columns    List of columns that the converstoin is going to take
     * @param array $data       The model data
     * @return array
     * 
     * Author: @abiygirma
     */
    public static function change_model_items_to_array(array $columns, array $data) : array {
       
        if(!empty($data['data']) && ($data['singleton'] == true)) {
            $converted_data = self::convert_single_row_item_to_array($columns, $data['data']);

            foreach($columns as $column)
                $data['data'][$column] = $converted_data[$column];
        }
        elseif(!empty($data['data']) && ($data['singleton'] == false)) {
            foreach($data['data'] as $index => $row){
                $converted_data = self::convert_single_row_item_to_array($columns, $row);
                foreach($columns as $column)
                    $data['data'][$index][$column] = $converted_data[$column];
            }
        }
        
        return $data;
    }

    private static function convert_single_row_item_to_array(array $columns, array $data) : array {
        foreach($columns as $column)
            $data[$column] = \BluelimeSystem::convert_to_array($data[$column]??'');

        return $data;
    }

    public static function round_model_item_currency(array $columns, array $data):array{
        foreach($data['data'] as $index => $row){
            foreach($columns as $column){
                if(isset($row[$column]))
                    $data['data'][$index][$column] = self::round_currency($row[$column]);
            }
        }
        return $data;
    }

    private static function round_currency($value):float{
        return round(floatval($value), 2);
    }

    /**
     * Set price for each service by checking from price list. If it cannot find a price for 
     * service for a specific company it will set the default price for the service. If service is 
     * not found it will set a price 0. 
     * If there is a Discount %, it will set that discount for all service. 
     *
     * @param int $company_id
     * @param array $service_list
     * @return array Updated array $service_list
     * 
     * Author: @abiygirma
     */
    public static function add_price_detail_in_service_list(int $company_id, array $service_list) : array {
        $serviceModel = model('Hospital/ServiceModel');
        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');

        $pricelist = $pricelistModel->where('company_id', $company_id)->findColumn('id');

        $price_list_id = \BluelimeConfiguration::DEFAULT_PRICE_LIST_ID;
        if(!empty($pricelist))
            $price_list_id = $pricelist[0];
        
        $price_list_detail_default = $pricelistDetailModel->where('pricelist_id', \BluelimeConfiguration::DEFAULT_PRICE_LIST_ID)->findAll();
        $price_list_detail = $price_list_detail_default;
        
        if($price_list_id != \BluelimeConfiguration::DEFAULT_PRICE_LIST_ID)
            $price_list_detail = $pricelistDetailModel->where('pricelist_id', $price_list_id)->findAll();
        
        $discount_percent = self::get_discount_percent($service_list);
        
        foreach($service_list as $index => $row){
            $price = 0;
            $service_type = '';
            if($row['service_id'] == \BluelimeConfiguration::DISCOUNT_PRICE_AMOUNT_ID){
                $service_list[$index]['quantity'] = 1;
                $service_list[$index]['price'] = -1 * floatval($row['quantity']);
                $service_list[$index]['discount'] = 0;
                $service_list[$index]['service_type'] = 'Discount';
                
            }
            elseif($row['service_id'] == \BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID){
                unset($service_list[$index]);
                continue;
            }
            else {
                list($price,$service_type) = self::get_price_and_service_type($row['service_id'],$price_list_detail);
                if(($price == 0) && ($price_list_id != \BluelimeConfiguration::DEFAULT_PRICE_LIST_ID))
                    list($price,$service_type) = self::get_price_and_service_type($row['service_id'],$price_list_detail_default);
                
                $service_list[$index]['price'] = $service_list[$index]['price'] ?? $price;
                $service_list[$index]['service_type'] = $service_type;
                $service_list[$index]['discount'] = $discount_percent;
            }

        }
       // \BluelimeSystem::show_and_die($service_list);
        return $service_list;
    }

    private static function get_price_and_service_type(int $service_id, array $price_list_detail) : array {
        $price = 0;
        $service_type = '';
        foreach($price_list_detail as $row){
            if($service_id == $row['service_id']){
                $price = floatval($row['service_price']);
                $service_type = $row['service_type'];
                break;
            }
        }
         //\BluelimeSystem::show_and_die($service_id);
        return [$price, $service_type];
    }

    private static function get_discount_percent(array $service_list) : float {
        $discount_percent = 0;
        foreach($service_list as $row){
            if(intval($row['service_id']) == \BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID){
                $discount_percent = floatval($row['quantity']);
                break;
            }
        }
        return $discount_percent;
    }

    /**
     * Collect visit id by traversing trough the chain using related_visit. 
     *
     * @param integer $follow_up_id
     * @param integer $visit_id
     * @param boolean $includeThisOne
     * @return array
     * 
     * Author: @abiygirma
     */
    public static function collect_follow_up_visits(int $follow_up_id, int $visit_id = -1, bool $includeThisOne = true) : array{
        //If follow up id is zero or null there should not be follow up visit list or related visites.
        if(intval($follow_up_id) == 0)
            return [];

        $related_visits_collection = [];

        $visitModel = model('Hospital/VisitModel');

        $visitModel = $visitModel->where('follow_up_id', $follow_up_id);

        if(!$includeThisOne)
            $visitModel = $visitModel->where('id !=', $visit_id);

        $related_visits_collection = $visitModel->findColumn('id');

        if(empty($related_visits_collection))
            $related_visits_collection = [];

        return $related_visits_collection;

        /*
        $related_visit = $visitModel->where('id = ', $visit_id)->findColumn('related_visit');

        if(!empty($related_visit)){
            if(!empty($related_visit[0])){
                $related_visit = $related_visit[0];
                array_push($related_visits_collection, ...self::collect_related_visits($related_visit));
            }
        }
        */
    }

    /**
     * Fetch visit full data from list of visit id
     *
     * @param array $visit_id_list
     * @return array
     * 
     * Author: @abiygirma
     */
    public static function get_visits_encounters(array $visit_id_list) : array{
        if(empty($visit_id_list))
            return [];
        
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $visit_encounters = $visitEncounterModel->whereIn('visit_id', $visit_id_list)->findAll();
        return $visit_encounters;
    }

    public static function encounter_history($encounters, $is_taginputs = false){
        foreach($encounters as $row){
            $title = $row['title'] ?? '';
            if($is_taginputs){
                $title = '';
                $tags = \BluelimeSystem::convert_to_array($row['title']);
                foreach($tags as $tag){
                    if(!is_array($tag))
                        $title .= $tag;
                    elseif(trim($tag['service_name']) != '')
                        $title .= $tag['service_name'];
                }
            }
            $description = $row['description'] ?? '';
            if(!empty(trim($title)) || !empty(trim($description)))
                echo $title .': '. $description  . "\n";
        }
    }

    private static function create_order(string $sex,string $date_of_birth,string $description,string $mobile,int $patient_id,string $patient_name, int $visit_id, string $order_by,string $registered_by, int $bill_id, array $order_detail, bool $is_external, bool $is_free_service){
        if(empty($order_detail))
            return false;

        $orderMainModel = model('Hospital/OrderMainModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');

        $service_detail = BluelimeSystem::gather_objects_by_reference($order_detail, 'ordered_service_id', 'Hospital/ServiceModel', 'id', true);

        $order_mains = [];
        foreach($order_detail as $detail){
            if($detail['ordered_service_id']<0) //Discount
                continue;
                
            $service_type = $service_detail['id_'.$detail['ordered_service_id']]['service_type'];

            $order_mains[$service_type] = [
                'patient_id' => $patient_id,
                'patient_name' => $patient_name,
                'mobile' => $mobile,
                'sex' => $sex,
                'date_of_birth' => $date_of_birth,
                'order_comment' => $description,
                'visit_id' => $visit_id,
                'ordered_by' => $order_by,
                'registered_by' => $registered_by,
                'bill_id' => $bill_id,
                'order_service_type' => $service_type,
                'is_external' => $is_external
            ];
            
            
        }

        foreach($order_mains as $index => $order_main){
            
            if($is_free_service == 1){
                $order_main['order_status'] = 1;
            }
            
            $order_main_id = $orderMainModel->insert($order_main);
            $order_mains[$index]['id'] = $order_main_id;
        }

        foreach($order_detail as $index => $row){
            if($row['ordered_service_id']<0) //Discount
            {
                unset($order_detail[$index]);
                continue;
            }

            $service_type = $service_detail['id_'.$row['ordered_service_id']]['service_type'];
            $order_detail[$index]['order_main_id'] = $order_mains[$service_type]['id'];
        }
        
        $orderDetailModel->insertBatch($order_detail);
    }

    public static function create_bill(string $sex,string $date_of_birth,string $description,int $patient_id, string $patient_name, string $mobile, int $company_id, string $company_name, int $visit_id, string $bill_type,  string $ordered_by,string $registered_by, string $registered_by_name, array $service_list, bool $is_external, bool $is_free_service) : int {
        $bill_status = \BluelimeConfiguration::BILL_PENDING;
        $payed_date = NULL;
        if($is_free_service == 1){
            $bill_status = \BluelimeConfiguration::BILL_RECIEVED;
            $payed_date = BluelimeSystem::date_now();
        }
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        $data = [
            'patient_id'=>  $patient_id,
            'visit_id'=> $visit_id,
            'bill_type'=> $bill_type,
            'registered_by'=> $registered_by,
            'ordered_by'=> $ordered_by,
            'patient_name'=>$patient_name,
            'payed_date'=>$payed_date,
            'registered_by_name'=> $registered_by_name,
            'company_id'=>$company_id,
            'company_name'=>$company_name,
            'mobile'=>$mobile,
            'bill_status'=>$bill_status,
            'created_by'=> $loggeduser['id']
        ];

        $billModel = model('Hospital/BillModel');

        $bill_id = $billModel->insert($data);
        if(!is_int($bill_id))
            $bill_id = -1;
        else
            self::create_bill_detail($sex,$date_of_birth,$description,$mobile,$patient_name,$bill_id,$company_id, $service_list, $patient_id, $visit_id, $ordered_by,$registered_by, $is_external,$is_free_service);

        return $bill_id;
    }

    private static function create_bill_detail(string $sex,string $date_of_birth,string $description,string $mobile,string $patient_name,int $bill_id, int $company_id, array $service_list, int $patient_id, int $visit_id, string $ordered_by, string $registered_by, bool $is_external, bool $is_free_service){
        $bill_price_detail = self::add_price_detail_in_service_list($company_id,$service_list);
        
        $bill_detail_data = [];

        foreach($bill_price_detail as $service){
            $billDetail = [
                'bill_id' => $bill_id,
                'service_id' => $service['service_id'],
                'service_name' => $service['service_name'],
                'service_type' => $service['service_type'],
                'bill_quantity' => ($service['price']>=0)?$service['bill_quantity']:1,
                'bill_amount' => $service['price'],
                'bill_discount' => $service['discount'],
                'bill_date' => \BluelimeSystem::date_now(),
                'service_status' => 0
            ];

            array_push($bill_detail_data, $billDetail);
        }
        //\BluelimeSystem::show_and_die($bill_detail_data);
        $billDetailModel = model('Hospital/BillDetailModel');
        if(!empty($bill_detail_data))
            $billDetailModel->insertBatch($bill_detail_data);

        $bill_details = $billDetailModel->where('bill_id', $bill_id)->findAll();
        
        $laboratory_order_detail = [];
        foreach($bill_details as $bill_detail){
            //BluelimeSystem::show_and_die($bill_detail['service_type']);
            if(($bill_detail['service_type'] != BluelimeConfiguration::SERVICE_TYPE::CONSULTATION)){
                
                $temp = [
                    'ordered_service_id' => $bill_detail['service_id'],
                    'bill_detail_id' => $bill_detail['id']
                ];
                if($is_free_service == 1){
                    $temp['payment_id'] = -1;
                }
                array_push($laboratory_order_detail, $temp);
            }
            
        }
        
        self::create_order($sex,$date_of_birth,$description,$mobile,$patient_id,$patient_name, $visit_id, $ordered_by, $registered_by, $bill_id, $laboratory_order_detail, $is_external,$is_free_service);
    }

    public static function check_labratory_result_reference_range(array $laboratory_result_attribute, int $attribute_id, $result_value,){
        $attribute_line = [];

        foreach($laboratory_result_attribute as $row){
            if($attribute_id == $row['id']){
                $attribute_line = $row;
                break;
            }
        }

        $range_from = $attribute_line['reference_range_from'] ?? '';
        $range_to = $attribute_line['reference_range_to'] ?? '';
        $quantifiable = $attribute_line['quantifiable'] ?? false;
        
        if($quantifiable){
            if($result_value > $range_to)
                return 'UP';
            elseif($result_value < $range_from)
                return 'DOWN';
        }

        return '';
    }

    public static function get_patient_encounter_history(int $patient_id):array{
        $visitModel = model('Hospital/VisitModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $followUpModel = model('Hospital/FollowUpModel');
        $serviceModel = model('Hospital/ServiceModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');

        $visit_list = $visitModel->where('patient_id', $patient_id)->findAll();
        
        $visit_id_list = BluelimeSystem::collect_field_value_from_object($visit_list,'id')??[-1];
        
        $follow_up_id_list = BluelimeSystem::collect_field_value_from_object($visit_list,'follow_up_id');
        if(empty($visit_id_list)){
            return [];
        }

        $order_mains = $orderMainModel->whereIn('visit_id', $visit_id_list)->findAll();
        $order_mains = BluelimeSystem::index_object_list_array_by_unique_field($order_mains,'id');
        $order_main_id_list = [...BluelimeSystem::collect_field_value_from_object($order_mains, 'id'),-1];
        $order_details =  $orderDetailModel->whereIn('order_main_id', $order_main_id_list)->findAll();

        $services = BluelimeSystem::gather_objects_by_reference($order_details,'ordered_service_id','Hospital/ServiceModel','id',true);
        
        $bill_details = BluelimeSystem::gather_objects_by_reference($order_details,'bill_detail_id','Hospital/BillDetailModel','id',true);

        foreach($order_details as $index => $order_detail){
            $order_details[$index]['visit_id'] = -1;
            $order_details[$index]['service_detail'] = $services['id_'.$order_detail['ordered_service_id']] ?? [];
            $order_details[$index]['visit_id'] = $order_mains['id_'.$order_detail['order_main_id']]['visit_id'];
            $order_details[$index]['bill_detail'] = $bill_details['id_'.$order_detail['bill_detail_id']] ?? [];
            
            // foreach($order_mains as $order_main){
            //     if($order_main['id'] == $order_detail['order_main_id'])
            //         $order_details[$index]['visit_id'] = $order_main['visit_id'];
            // }
        }

        //BluelimeSystem::show_and_die($visit_id_list);

        $encounterHistory = $visitEncounterModel->whereIn('visit_id', $visit_id_list)->findAll();
        
        $follow_up_list = $followUpModel->whereIn('id', $follow_up_id_list)->findAll();

        foreach($follow_up_list as $index => $follow_up){
            $follow_up_list[$index]['encounter_history'] = [];
            $follow_up_list[$index]['order_history'] = [];

            $my_visit_list = [];
            foreach($visit_list as $visit){
                if($visit['follow_up_id'] == $follow_up['id'])
                    array_push($my_visit_list, $visit['id']);
            }
            foreach($encounterHistory as $encounter){
                if(array_search($encounter['visit_id'],$my_visit_list) !== false)
                    array_push($follow_up_list[$index]['encounter_history'], $encounter);
            }

            foreach($order_details as $order_detail){
                if(array_search($order_detail['visit_id'],$my_visit_list) !== false)
                    array_push($follow_up_list[$index]['order_history'], $order_detail);
            }
        }

        return $follow_up_list;

    }
    public static function get_patient_procedures(int $patient_id): array|null{
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');

        $billIdList = $billModel->where('patient_id',$patient_id)->where('bill_status',\BluelimeConfiguration::BILL_RECIEVED)->findColumn('id') ?? [-1];

        $patient_procedures = $billDetailModel->whereIn('bill_id', $billIdList)->where('service_type', 'Procedure')->orderBy('bill_date', 'DESC')->findAll();

        return $patient_procedures;
    }

    public static function check_sms_service():bool{
        return true;
        
        $notificationModel = model('Hospital/NotificationModel');
        $notification_status = $notificationModel->where('notification_type','SMS')->orderBy('id','DESC')->findColumn('status')[0]??[""];
        $url = "http://192.168.1.20:9090/sendsms";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        
        if(!$result && ($notification_status!=0)){
            $notfiy_data = [
                'notification_type'=>  "SMS",
                'description'=> "SMS",
                'link_type'=> 'email',
                'status'=> 0
             ];
            $notificationModel->insert($notfiy_data,true);

            $email = \Config\Services::email();

            $email->setFrom('rankderma@bluelimeplc.com', 'RankSMS');
            $email->setTo('bluelimeplc@gmail.com');

            $email->setSubject("⚠️ SMS Server is  offline  as of ".date('d-m-Y H:i:s'));
            $email->setMessage('Unable to send Message, http://192.168.1.20:9090/sendsms is Offline Check Server or Contact Administrator');

            $email->send();
            curl_close($ch);
            return false;
        }
        if(($result) && ($notification_status==0)){
            $timeoff = $notificationModel->where('notification_type','SMS')->orderBy('id','DESC')->findColumn('notification_date')[0]??"";
            $notfiy_data = [
                'notification_type'=>  "SMS",
                'description'=> "SMS",
                'link_type'=> 'email',
                'status'=> 1
             ];
            $notificationModel->insert($notfiy_data,true);
        
            $email = \Config\Services::email();

            $email->setFrom('rankderma@bluelimeplc.com', 'RANKSMS');
            $email->setTo('bluelimeplc@gmail.com');

            $email->setSubject("✅ SMS Server is  back Online  as of ".date('d-m-Y H:i:s'));
            $email->setMessage("SMS Server is now back online. It was down from ".$timeoff. " to ".date('d-m-Y H:i:s'));

            $email->send();
        }
        
        curl_close($ch);
        return true;
    }

    public static function roundToNearestThirty($timeStr, $is24HrFormat = false) {
        $timeStr = trim($timeStr);

        if($is24HrFormat){
            $am_pm = substr($timeStr, -2,2);
            $am_pm = strtoupper($am_pm);
            if(($am_pm == 'AM') || ($am_pm == 'PM'))
                $timeStr = trim(substr($timeStr,0,strlen($timeStr)-2));

            $time = date_create_from_format('H:i', $timeStr);
        }
        else
            $time = date_create_from_format('h:i A', $timeStr);

        
        //$time = DateTime::createFromFormat('H:i A', $timeStr);
        
        if (!$time) {
            return 'Invalid time format.';
        }
        
        $minutes = (int)$time->format('i');
        $hours = (int)$time->format('H');
        $totalMinutes = $hours * 60 + $minutes;

        $flooredMinutes = floor($totalMinutes / 30) * 30;
        $newHours = floor($flooredMinutes / 60);
        $newMinutes = $flooredMinutes % 60;
        
        $time->setTime($newHours, $newMinutes, 0);
        
        return $time->format('h:i A');
    }
    public static function formatThirtyMinuteRange($timeStr, $is24HrFormat = false) {
        $timeStr = trim($timeStr);

        if($is24HrFormat){
            $am_pm = substr($timeStr, -2,2);
            $am_pm = strtoupper($am_pm);
            if(($am_pm == 'AM') || ($am_pm == 'PM'))
                $timeStr = trim(substr($timeStr,0,strlen($timeStr)-2));

            $start = date_create_from_format('H:i', $timeStr);
        }
        else
            $start = date_create_from_format('h:i A', $timeStr);

       
        //$start = DateTime::createFromFormat('H:i A', $timeStr);
        if (!$start) {
            return 'Invalid time format..';
        }

        $end = clone $start;
        $end->add(new DateInterval('PT30M'));

        $startFormatted = $start->format('h:i A');
        $endFormatted = $end->format('h:i A');

        return "$startFormatted - $endFormatted";
    }

    public static function formatRoundedThirtyMinuteRange($timeStr) {
    $time = DateTime::createFromFormat('h:i A', $timeStr);

    if (!$time) {
        return 'Invalid time format';
    }

    // Round to nearest 30 minutes using 24-hour format
    $minutes = (int)$time->format('i');
    $hours = (int)$time->format('H');
    $totalMinutes = $hours * 60 + $minutes;

    $roundedMinutes = round($totalMinutes / 30) * 30;
    $newHours = floor($roundedMinutes / 60);
    $newMinutes = $roundedMinutes % 60;

    $time->setTime($newHours, $newMinutes, 0);

    // Format range
    $startFormatted = $time->format('h:i A');
    $end = clone $time;
    $end->add(new DateInterval('PT30M'));
    $endFormatted = $end->format('h:i A');

    return "$startFormatted - $endFormatted";
    }   

    public static function send_telegram_message($phone_number, $message, $image=NULL){
        //return 'Common.php -> send_telegram_message() -> Fix telegram message first';
        //$sms_footer = "\n\nFor any enquiry call 6797\nPlease join our social media\nhttps://www.tiktok.com/@rankdermatology\n\nhttps://www.instagram.com/rankderma/\n\nhttps://t.me/RankDerma_bot\n\nhttps://www.rankderma.com";
        $sms_footer = "\nFor any enquiry call 6797\nPlease join our social media\nhttps://www.tiktok.com/@rankdermatology\n\nhttps://www.instagram.com/rankderma/\n\nhttps://www.rankderma.com";
        $message .= $sms_footer;
        $telegram_sender_api_key = '2814a3c6f04525a1a98fbb9e0cce83b0_20260502';

        $content = [
            'api_key' => '1a84fbb9e02814b0c6f04525acce83b0_20241112',
            'phone_number' => $phone_number,
            'message' => $message,
            'bot_id' => 'RankDerma_bot'
        ];
        if(!empty($image)){
            $content['image'] = $image;
        }

        $content = http_build_query($content);
        //$url = 'https://api.bluelimeplc.com/rank/save_message';
        $url = 'https://api.rankderma.com.et/telegram/save_and_send?api_key='.$telegram_sender_api_key;
        $verbose_file = "";
        $file_name = "telegram" . '_' . date('Y-m-d') . '.txt';   
        try{
            $verbose_file = fopen(WRITEPATH."logs/Telegram/$file_name", 'a');
        }
        catch (\Exception $e){
            if(!file_exists(WRITEPATH."logs/Telegram"))
                mkdir(WRITEPATH."logs/Telegram",0777,true);
            
            $verbose_file = fopen(WRITEPATH."logs/Telegram/$file_name", 'w');
        }

        $text = "\n-----------------------------------------------------------------\n";
        $text .= date("Y-m-d H:i:s")."\n";
        $text .= "To: $phone_number\n";
        $text .= "Message: $message\n";
        
        fwrite($verbose_file, $text);
        fwrite($verbose_file, "\nURL: $url\n");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER,array('Content-Type: multipart/form-data'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_STDERR, $verbose_file);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        
        $result = curl_exec($ch);
        
        fwrite($verbose_file,"\nResult\n{$result}");
        
        fclose($verbose_file);
    }

    private static $email =  null;
    private static $email_is_initilized = false;

    public static function send_simple_email($to, $subject, $message, $from = null, array $attachments = []){
        self::initialize_email();

        if($from === null)
            self::$email->setFrom(from: 'noreply@pathology.rankderma.com', name: 'RANK Derma');
        else
            self::$email->setFrom(from: $from);

        foreach($attachments as $attachment)
            self::$email->attach(trim($attachment));
        
        self::$email->setTo($to);
        self::$email->setBcc('emailnebiyu@gmail.com');
        self::$email->setSubject($subject);
        self::$email->setMessage($message);
        if (!self::$email->send()) {
            $errors = self::$email->getError();
            $errorMessage = is_array($errors) ? implode('; ', $errors) : $errors;
            return $errorMessage ?: 'Email sending failed';
        }

        return true;
    }

    private static function initialize_email(){
        if(!self::$email_is_initilized)
            self::$email =  Config\Services::email(BluelimeConfiguration::EMAIL_CONFIG);
    }

    public static function send_sms_message($phone_number, $message, $send_new_year_message = true){

        $sms_footer = "\n\nFor any enquiry call 6797\nPlease join our social media\nhttps://www.tiktok.com/@rankdermatology\n\nhttps://www.instagram.com/rankderma/\n\nhttps://t.me/RankDerma_bot\n\nwww.rankderma.com";
        $message .= $sms_footer;
        if(($_ENV['SMS_GATEWAY_READY']??0) == 0){
            BluelimeSystem::write_log('Bluelime/SMS','outgoing_sms','$_ENV[\'SMS_GATEWAY_READY\'] = 0');
            return false;
        }

        $phone_number = self::correct_mobile($phone_number);

        if((strlen($phone_number) != 10) || (substr($phone_number,0,1) != '0'))
            return false;

        $result = BluelimeSMS::send_sms([$phone_number],$message);

        if(empty($result))
            return false;
        
        //TODO Fix the string comparison before
        if($send_new_year_message && (date('m-d-Y') <= '11-09-2025')){
            $new_year_message = '🌼እንኳን ለ2018 አዲስ አመት በሰላም አደረስዎ🌼
🎉 በአዲሱ ቅርንጫፋችን ብቻ ለተወሰነ ግዜ የሚቆይ የሃይድራ ፌሻል አገልግሎት ላይ ቅናሽ አድርገናል‼️
ቀጠሮ ለማስያዝ 
📞6797
📞+251947515151
በማህበራዊ ሚዲያችንም መረጃዎችን ያግኙ
Please join our social media
https://www.tiktok.com/@rankdermatology

https://www.instagram.com/rankderma/
';
            //self::send_sms_message($phone_number, $new_year_message, false);
        }

        return true;
    }

    public static function import_appointment(){

        $appointmentModel = model('Hospital/AppointmentModel');
        try{
            $url = "https://api.bluelimeplc.com/getdata.php";
            $json_data = file_get_contents($url);
            
            $app = json_decode($json_data,JSON_OBJECT_AS_ARRAY);

            foreach($app as $ap){
                $data = [
                    'patient_id'    => 0,
                    'doctor_id'   => 0,
                    'appointment_date'     => date("Y-m-d", strtotime($ap['dt'])),
                    'appointment_reason' => $ap['service'],
                    'appointment_status'           => 0,
                    'full_name'       =>  $ap['name'].' '.$ap['fname'].' '.$ap['gname'],
                    'registered_by'        => 0,
                    'registered_by_name'      => "",
                    'doctor_name'        => "",
                    'paid_appointment' =>  $ap['id'],
                    'visit_id' => 0,
                    'reminder_sent'    => "",
                    'schedule_id'    => 0,
                    'mobile'    =>  $ap['mobile'],
                    'time'    =>  $ap['time'],
                ];
                $appexists = $appointmentModel->where('paid_appointment',$ap['id'])->findAll();
                // \BluelimeSystem::show_and_die($appexists);
                if (empty($appexists)){
                    $appointmentModel->insert($data,true);
                    $appointment_message = "Thank you! ".$ap['name'].' '.$ap['fname'].' '.$ap['gname']." Your booking ". date("Y-m-d", strtotime($ap['dt']))." ".BluelimeSystem::roundToNearestThirty(($ap['time']))." is pending for approval.\nYou will Shortly recieve Approved Schedule.\nRANK Specialized Dermatology Clinic ";
                    $telegram_bot = "\nJoin Telegram: https://t.me/RankDerma_bot";
                    $website = "\nBook Appointment: www.rankderma.com";
                    $branch = $_ENV['BRANCH']??"";
                    
                    if(\BluelimeSystem::check_sms_service()){
                        \BluelimeSystem::send_sms_message($ap['mobile'], $appointment_message.$branch.$telegram_bot.$website);
                    }
                    $email = \Config\Services::email();

                    $email->setFrom('rankderma@bluelimeplc.com', 'Online Appointment');
                    $email->setTo('bluelimeplc@gmail.com');

                    $email->setSubject("Online Appointment Made on ".date('d-m-Y H:i:s'));
                    $email->setMessage('Online Appointment Made Check on the System for Confirmation');

                    $email->send();
                }
            }
        }
        catch(Exception $e){
            
        }
    }

    public static function import_online_prescription(){

        $onlinePrescriptionModel = model('Hospital/OnlinePrescriptionModel');
       
        $url = "https://api.bluelimeplc.com/rank/export_online_prescription?branch=".strtoupper($_ENV['BRANCH']);
        $json_data = file_get_contents($url);
            
        $app = BluelimeSystem::convert_to_array($json_data);
            
       //BluelimeSystem::show_and_die($app);
        //$onlinePrescriptionModel->where('status',1)->findColumn('prescription_group_id');
        foreach($app as $ap){
            if(strtoupper($ap['branch_to'])==strtoupper($_ENV['BRANCH']) && $ap['status']==0){
            $data = [
                'branch'    => $ap['branch'],
                'phone_number'   => $ap['phone_number'],
                'message'     => $ap['message'],
                'image' => base64_decode($ap['image']),
                'status'           => $ap['status'],
                'patient_name'       =>  $ap['patient_name'],
                'branch_from'        => $ap['branch_from'],
                'branch_to'      => $ap['branch_to'],
                'doctor_name'        => $ap['doctor_name'],
                'prescription_group_id' =>  $ap['prescription_group_id'],
                'date_created' => $ap['date_created'],
            ];
        $id = $onlinePrescriptionModel->insert($data,true);

        }
            
        }
    }


    public static function export_prescription($phone_number, $message){
        $content = [
            'api_key' => '1a84fbb9e02814b0c6f04525acce83b0_20241112',
            'phone_number' => $phone_number,
            'message' => $message
        ];

        $content = http_build_query($content);
        $url = 'https://api.bluelimeplc.com/prescription/send_single';
        $verbose_file = "";
        $file_name = "prescription" . '_' . date('Y-m-d') . '.txt';   
        try{
            $verbose_file = fopen(WRITEPATH."logs/Prescription/$file_name", 'a');
        }
        catch (\Exception $e){
            mkdir(WRITEPATH."logs/Prescription",0777,true);
            $verbose_file = fopen(WRITEPATH."logs/Prescription/$file_name", 'w');
        }

        $text = "\n-----------------------------------------------------------------\n";
        $text .= date("Y-m-d H:i:s")."\n";

        fwrite($verbose_file, $text);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER,array('Content-Type: multipart/form-data'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_STDERR, $verbose_file);
        
        $result = curl_exec($ch);
        curl_close($ch);
        fclose($verbose_file);
    }

    public static function communicate_api2(string $caller, string $url, ?string $api_key,?array $post_data = []){
        if(BluelimeSystem::bluelime_debug_is_on())
            $verbose_file = BluelimeSystem::write_log('Bluelime/api2','communicate_api2','','CURL',true,true);
        
        try
        {
            $ch = curl_init();
            if($api_key !== null){
                if(strpos($url,'?')===false)
                    $url .= "?api_key=$api_key";
                else
                    $url .= "&api_key=$api_key";
            }

            curl_setopt($ch, CURLOPT_URL, $url);

            //curl_setopt($ch, CURLOPT_HEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $post_data = self::convert_array_to_string($post_data);

            curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);

            //Debug 
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\n$caller -> communicate_api2\nPOST_DATA: ".$post_data."\nURL: $url\n\n";
                fwrite($verbose_file,$log);

                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_STDERR, $verbose_file);
            }

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if(!($httpCode >= 200 && $httpCode < 300))
                $result = null;
            //curl_close($ch);
            
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\nResult\n".$result."\n";
                fwrite($verbose_file,$log);
            }
        } 
        catch(\Exception $e)
        {
            $result = $e->getMessage();
            
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\n\Error\n".$result."\n";
                fwrite($verbose_file,$log);
            }
        }
        
        if(BluelimeSystem::bluelime_debug_is_on())
            fclose($verbose_file);

        return $result;
    }


    public static function get_company_header()
    {
        $headerInformationModel = model('System/HeaderInformation');
        $url = explode('/',$_SERVER['PHP_SELF']);
        $request_uri = explode('/', $_SERVER['REQUEST_URI']);
        
        if(($request_uri[1]??'')==='inventory'  && end($url)!=='view_prescription_printout')
            $company_id = 2;
        else
            $company_id = 1;

        $company_header = $headerInformationModel->where('id', $company_id)->findAll()[0] ?? [];
        return $company_header;
    }

    private static function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
    
        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
    
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    public static function export_arra_in_csv($data, $file_name, $ignore_header = false){
        if (count($data) == 0) {
            return null;
        }
        self::download_send_headers($file_name);
        ob_start();
        $df = fopen("php://output", 'w');

        if(!$ignore_header)
            fputcsv($df, array_keys(reset($data)));

        foreach ($data as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        echo ob_get_clean();
        die();
    }

    public static function get_user_agent_info($controllerObj)
    {
        $agent = $controllerObj->request->getUserAgent();

        if ($agent->isBrowser()) {
            $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion() . ' ' . $agent->getPlatform(). ' ' . $agent->getMobile();
        } elseif ($agent->isRobot()) {
            $currentAgent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $currentAgent = $agent->getMobile();
        } else {
            $currentAgent = 'Unidentified User Agent';
        }

        return $currentAgent;
    }

    public static function send_curl_request($url, $header = [], $timeout = 5) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");

        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        // $verbose_file = self::write_log('Bluelime/CURL','send_curl_request','','CURL',true,true);
        // curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // curl_setopt($ch, CURLOPT_STDERR, $verbose_file);

        $result = curl_exec($ch);
        //curl_close($ch);
        return $result;
    }
}
class BluelimeAuthentication
{
    public static function set_logged_user_data(array $loggeduserdata): array
    {
        $session = \Config\Services::session();
        $loggeduserdata['login_id'] = $session->session_id;
        $_SESSION['loggeduserdata'] = $loggeduserdata;
        return $loggeduserdata;
    }

    public static function get_logged_user_data()
    {
        $session = \Config\Services::session();
        $loggeduserdata = [];
        if(isset($_SESSION['loggeduserdata']))
            $loggeduserdata = $_SESSION['loggeduserdata'];
        return $loggeduserdata;
    }
    
    public static function logout()
    {
        $session = \Config\Services::session();
        session_destroy();
    }
}

class BluelmeHospital {
    public static function set_prescription_is_sold($prescription_group_id){
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $prescriptionModel->where('prescription_group_id',$prescription_group_id)->set('is_sold',1)->update();
        $compoundingPrescriptionMainModel->where('prescription_group_id',$prescription_group_id)->set('is_sold',1)->update();
        return BluelimeSystem::get_success_ajax_return('ok');
    }

    public static function save_compounding_prescription(array $compounding_prescription,int $patient_id =-1, bool $is_external = false, array $external_detail = []){
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $compoundingPrescriptionDetailModel = model('Hospital/CompoundingPrescriptionDetailModel');
        //$itemModel = model('Inventory/ItemModel');
        $patientModel = model('PatientModel');

        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $patient_detail = [];
        if($patient_id != -1)
            $patient_detail = $patientModel->find($patient_id);

        $compounding_prescription_main = [
            'patient_id' => $patient_id,
            'patient_detail' => BluelimeSystem::convert_array_to_string($patient_detail),
            'created_by' => $loggeduser['id'],
            'created_by_name' => $loggeduser['name'],
            'is_external' => $is_external,
            'external_detail' => BluelimeSystem::convert_array_to_string($external_detail),
        ];

        $prescription_id = $compoundingPrescriptionMainModel->insert($compounding_prescription_main);
        
        $items = BluelimeSystem::gather_objects_by_reference($compounding_prescription,'item_id','Inventory/ItemModel','id',index_by_model_field:true);

        $compounding_prescription_detail = [];
        foreach($compounding_prescription as $line){
            $temp = [
                'main_id' => $prescription_id,
                'item_id' => $line['item_id'],
                'item_detail' => BluelimeSystem::convert_array_to_string($items['id_'.$line['item_id']]),
                'amount' => $line['amount'],
                'measurement' => $line['measurement'],
                'in_percent' => $line['in_percent'],
            ];

            array_push($compounding_prescription_detail, $temp);
        }

        $compoundingPrescriptionDetailModel->insertBatch($compounding_prescription_detail);

        return $prescription_id;
    }
}

use \App\ThirdParty\Bluelime\POS\BluelimePOSMachine;
class BluelimePOS {
    public static function fix_name_for_reciept(string $name):string{

        $new_name = preg_replace("/[^A-Za-z0-9 -]/", ' ', $name);

        return $new_name;
    }

    public static function get_attachmwnt_data_invetory($pos_bill_id):array{
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $pOSBillModel = model('POS/POSBillModel');

        $pos_bill = $pOSBillModel->find($pos_bill_id);
        $sales_mains = $salesMainModel->where('pos_bill_id', $pos_bill_id)->orderBy('sales_type', 'DESC')->findAll();
        $sales_id_list = BluelimeSystem::collect_field_value_from_object($sales_mains,'id');
        if(empty($sales_id_list))
            $sales_id_list = [-1];

        $sales_details = $salesDetailModel->whereIn('sales_id', $sales_id_list)->findAll();
       
        foreach($sales_mains as $index => $sales_main){
            $sales_mains[$index]['sales_details'] = [];
            foreach($sales_details as $sales_detail){
                if($sales_main['id'] == $sales_detail['sales_id'])
                    array_push($sales_mains[$index]['sales_details'], $sales_detail);
            }
        }

        $sub_total = 0;
        $taxable_total = 0;
        $total_vat = 0;
        $total = 0;

        foreach($sales_mains as $sales_main){
            foreach($sales_main['sales_details'] as $sales_detail){
                $price = ($sales_detail['unit_price'] + $sales_detail['vat']) * $sales_detail['quantity'];
                if($sales_detail['vat'] > 0){
                    $total_vat += $sales_detail['vat'] * $sales_detail['quantity'];
                    $taxable_total += $sales_detail['unit_price'] * $sales_detail['quantity'];
                }
                else
                    $sub_total += $price;

                $total += $price;
            }
        }

        return [$pos_bill, $sales_mains, $sub_total, $taxable_total, $total_vat, $total];
    }
    public static function get_attachmwnt_data_hospital($pos_bill_id):array{
        $pOSBillModel = model('POS/POSBillModel');
        $billModel = model('Hospital/BillModel');
        $patientModel = model('Hospital/PatientModel');
        $bill = $billModel->where('pos_bill_id', $pos_bill_id)->findAll();

        $bill = $bill[0] ?? [];
        $pos_bill = $pOSBillModel->find($pos_bill_id);
        $pos_bill_json = $pos_bill['pos_bill_json'];

        $sales_main = [
            'id' => $pos_bill['id'],
            'created_at'=>$pos_bill['created_at'],
            'registered_by' => $bill['registered_by_name'] ?? '',
            'fs_number' => $pos_bill['fs_number'],
            'pos_bill_json' => $pos_bill_json,
            'active_menu'=>'list',
        ];

        $sub_total = 0;
        foreach($pos_bill_json['sales_item_list'] as $row){
            $sub_total += $row['quantity'] * $row['unit_price'];
        }

        $payment = [
            'none_taxable_sub_total' => $sub_total,
            'taxable_sub_total' => 0,
            'vat' => 0,
            'total' => $sub_total,
        ];

        $patient = $patientModel->find($bill['patient_id']??-99);


        return [$pos_bill, $sales_main, $sub_total, $payment, $bill, $patient];
    }

    public static function create_pos_bill(string $type, array $sales_line, string $customer_code, string $customer_name, string $customer_tin = '', string $company_name='', bool $is_credit = false, array $sales_id_list =[]):array {
        $pOSBillModel = model('POS/POSBillModel');
        $payment_method = $is_credit ? 'Credit' : 'Cash';

        $pos_bill_data = [ 'type' => $type, 'payment_method' => $payment_method, 'sales_id_list' => BluelimeSystem::convert_array_to_string($sales_id_list)];
        $pos_bill_id = $pOSBillModel->insert($pos_bill_data);

        if((trim($customer_name) == '') || (strtolower(trim($customer_name)) == 'walk in')){
            $customer_name = 'Walk in - '. $pos_bill_id;
        }
    
        $invoice_no = 'GLPH-'.sprintf('%06d', $pos_bill_id);
        if($type == 'Hospital')
            $invoice_no = 'RANK-'.sprintf('%06d', $pos_bill_id);

        $pos_bill_json = ['sales_item_list' => []];
        $total = 0;
        foreach($sales_line as $row){
            $temp = [
                'item_id' => $row['item_id'],
                'measurment' => $row['unit_of_measurment'],
                'quantity' => $row['item_quantity'],
                'item_type' => $row['item_type'],
                'unit_price' => $row['unit_price'],
                'item_name' => self::fix_name_for_reciept($row['item_description']),
                'vat' => $row['is_vat'],
            ];
            $price = $row['item_quantity'] * $row['unit_price'];
            $vat = $price * 0.15 * intval($row['is_vat']);
            $total += $price + $vat;

            array_push($pos_bill_json['sales_item_list'], $temp);
        }

        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $pos_bill_json['username'] = $logged_user['username'];
        $pos_bill_json['tin'] = $customer_tin;
        $pos_bill_json['invoice_type'] = 'CASH';
        $pos_bill_json['payment_type'] = 'Cash';
        if($is_credit){
            $pos_bill_json['invoice_type'] = 'CREDIT';
            $pos_bill_json['payment_type'] = 'Credit';
        }
        $pos_bill_json['customer_code'] = self::fix_name_for_reciept($customer_code);
        $pos_bill_json['customer_name'] = self::fix_name_for_reciept($customer_name);
        $pos_bill_json['company_name'] = self::fix_name_for_reciept($company_name);
        $pos_bill_json['invoice_no'] = $invoice_no;

        $update_data = ['invoice_no' => $invoice_no, 'pos_bill_json' => BluelimeSystem::convert_array_to_string($pos_bill_json), 'total' => $total];

        $pOSBillModel->update($pos_bill_id, $update_data);

        $pos_bill = ['id'=>$pos_bill_id, ...$pos_bill_data, 'invoice_no' => $invoice_no, 'pos_bill_json' => $pos_bill_json];
        //return [$customer_name, $pos_bill_id];
        return $pos_bill;
    }

    public static function delete_pos_bill(int $pos_bill_id){
        $pOSBillModel = model('POS/POSBillModel');
        $pOSBillModel->delete($pos_bill_id);
    }
    public static function send_to_pos(array $pos_bill):bool{
        $pOSBillModel = model('POS/POSBillModel');
        $pOSMachineModel = model('POS/POSMachineModel');

        $pos_bill_json = $pos_bill['pos_bill_json'];

        $machine_name = $_ENV['PHARMACY_POS_NAME'] ?? 'pharmacy_peds_1';
        if($pos_bill['type'] == 'Hospital')
            $machine_name = $_ENV['HOSPITAL_POS_NAME'] ?? 'reception_peds_1';

        $pos_machine = $pOSMachineModel->where('machine_name', $machine_name)->findAll();
        $pos_machine = $pos_machine[0];
        $success = BluelimePOSMachine::send_invoice_to_pos($pos_bill_json, $pos_machine);
        if($success){
            $update_data = ['sent_to_pos_date' => BluelimeSystem::date_now()];
            $pOSBillModel->update($pos_bill['id'], $update_data);
        }

        return $success;
    }

    public static function delete_invoice(int $pos_bill_id){
        $pOSBillModel = model('POS/POSBillModel');
        $pOSMachineModel = model('POS/POSMachineModel');

        $pos_bill = $pOSBillModel->find($pos_bill_id);

        $machine_name = $_ENV['PHARMACY_POS_NAME'] ?? 'pharmacy_maraki_1';
        if($pos_bill['type'] == 'Hospital')
            $machine_name = $_ENV['HOSPITAL_POS_NAME'] ?? 'reception_peds_1';

        $pos_machine = $pOSMachineModel->where('machine_name', $machine_name)->findAll();
        $pos_machine = $pos_machine[0];
        
        $success = BluelimePOSMachine::void_invoice($pos_bill['invoice_no'], $pos_machine);
        if(!$success)
            return false;
        
        return $pOSBillModel->delete($pos_bill_id);
    }

    public static function read_response(){
        $pOSMachineModel = model('POS/POSMachineModel');
        $machine_name = $_ENV['MARAKI_POS'] ?? 'pharmacy_maraki_1';
        $machine_names = explode(',',$machine_name);

        $acknowledgee_list = [];

        foreach($machine_names as $machine_name){
            $machine_name = trim($machine_name);
            $pos_machine = $pOSMachineModel->where('machine_name', $machine_name)->findAll();
            $pos_machine = $pos_machine[0];
    
            $response_list = BluelimePOSMachine::read_response($pos_machine);
    
            foreach($response_list as $row){
                if(self::update_sales_response($row['reference_number'],$row['fs_number'],$row['response_data'],$row['machine_code']))
                    array_push($acknowledgee_list, $row);
                //rename($file_path.$row['file_name'], $xml_root_path.'Bluelime/from_response/'.$row['file_name']);
            }
            BluelimePOSMachine::acknowledge_response($acknowledgee_list, $pos_machine);    
        }
        return $acknowledgee_list;
    }

    public static function read_response_peds(){
        // $pOSMachineModel = model('POS/POSMachineModel');
        // $machine_name = 'reception_peds_1';
        // $pos_machine = $pOSMachineModel->where('machine_name', $machine_name)->findAll();
        // $pos_machine = $pos_machine[0];
        $pos_machine['active'] = 1;
        $pos_machine['type'] = 'PEDS';
        [$invoice_number, $fs_number, $machine_number, $respose_data, $success_acknowledgment, $fail_acknowledgment] = BluelimePOSMachine::read_response($pos_machine);

        $acknowledgment = $success_acknowledgment;

        
        if(intval($fs_number) < 1){
            $acknowledgment = $fail_acknowledgment;
        }
        else{
            $session = \Config\Services::session();
            $loggeduserdata = [
                'id' => -1,
                'name' => 'PEDS'
            ];
            $_SESSION['loggeduserdata'] = $loggeduserdata;
            
            if(!self::update_sales_response($invoice_number, $fs_number, $respose_data, machine_number:$machine_number))
                $acknowledgment = $fail_acknowledgment;
        }
        
        //return $acknowledgment;
        //Always return success. Because PEDS should not care about Bluelimes error.
        return $success_acknowledgment;
    }

    public static function update_sales_response(string $reference_number, string $fs_number, string $response_data, $machine_number ='', $payment_date=''):bool{
        $pOSBillModel = model('POS/POSBillModel');

        if($payment_date == '')
            $payment_date = BluelimeSystem::date_now();
        
        $pos_bill = $pOSBillModel->where('invoice_no', $reference_number)->findAll();

        if(empty($pos_bill))
            return false;

        $pos_bill = $pos_bill[0];

        $pos_bill_id = $pos_bill['id'];
        
        if($pos_bill['type'] == 'Hospital'){
            self::update_hospital_payment($pos_bill, $fs_number, $payment_date);
        }
        if($pos_bill['type'] == 'Inventory'){
            $salesMainModel = model('Inventory/SalesMainModel');

            $update_data = ['fs_number' => $fs_number, 'is_recieved' => 1, 'recieved_date' => $payment_date];
            $salesMainModel->where('pos_bill_id', $pos_bill_id)->update(row:$update_data);
            if(empty($pos_bill['response_date']))
                self::update_inventory_transaction($pos_bill_id);
        }

        $update_data = [
            'response_date' => $payment_date,
            'response_data' => $response_data,
            'fs_number' => $fs_number,
            'machine_number' => $machine_number
        ];

        return $pOSBillModel->update($pos_bill_id, $update_data);
    }

    private static function update_hospital_payment(array $pos_bill, string $reciept_no, $payed_date = ''){
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $visitModel = model('Hospital/VisitModel');
        $billPaymentModel = model('Hospital/BillPaymentModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $logged_user = BluelimeAuthentication::get_logged_user_data();

        if($payed_date == '')
            $payed_date = BluelimeSystem::date_now();

        $bills = $billModel->where('pos_bill_id', $pos_bill['id'])->findAll();

        foreach($bills as $bill){
            $bill_details = $billDetailModel->where('bill_id', $bill['id'])->where('service_status !=', BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();
            [$payed_amount, $payed_discount] = self::get_hospital_bill_total_amoun($bill_details);

            $bill_payment_data = self::create_hospital_payment_data($bill, $reciept_no, $payed_amount, $payed_discount,  $logged_user);

            $paymnet_id = $billPaymentModel->insert($bill_payment_data,true);

            $bill_detail_id_list = BluelimeSystem::collect_field_value_from_object($bill_details, 'id');
            if(empty($bill_detail_id_list))
                $bill_detail_id_list = [-1];
        
            $orderDetailModel->whereIn('bill_detail_id', $bill_detail_id_list)->update(row:['payment_id'=>$paymnet_id]);

            $orderMainModel->where('bill_id', $bill['id'])->update(row:['order_status' => BluelimeConfiguration::LAB_ORDER_PAID]);

            $billModel->update($bill['id'], ['bill_status' => BluelimeConfiguration::BILL_RECIEVED, 'fs_number' => $reciept_no, 'payed_date' => $payed_date]);
        }

        $bill_id_list = BluelimeSystem::collect_field_value_from_object($bills, 'id');
        if(empty($bill_id_list))
            $bill_id_list = [-1];

        $consultations = $billDetailModel->whereIn('bill_id', $bill_id_list)->where('service_type', 'Consultation')->findAll();
        $consultation = $consultations[0] ?? [];
        if(!empty($consultation)){
            foreach($bills as $bill){
                if($bill['id'] == $consultation['bill_id']){
                    $visitModel->where('id',$bill['visit_id'])->where('visit_status', BluelimeConfiguration::VISIT_STATUS_NEW)->update(row:['visit_status' => BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN, 'bill_id' => $bill['id']]);
                    break;
                }
            }
        }
    }

    private static function create_hospital_payment_data($bill, $reciept_no, $payed_amount, $payed_discount,  $logged_user):array{
        $bill_payment_data = [
            'bill_id'=>  $bill['id'],
            'payed_amount'=> $payed_amount,
            'registered_by'=> $logged_user['id'] ?? -2,
            'registered_by_name'=> $logged_user['name'] ?? 'POS',
            'payed_discount'=> $payed_discount,
            //'tin'=> $tin,
            'fs_number'=> $reciept_no,
            'company_name'=> $bill['company_name'],
            'company_id'=> $bill['company_id'],
            'patient_name'=> $bill['patient_name'],
            'patient_id'=> $bill['patient_id'],
            'payment_type'=> 'Cash',
            'mobile'=> $bill['mobile'],
            'due'=> 0,
            //'payment_mode'=> $payment_mode
        ];

        return $bill_payment_data;
    }

    private static function get_hospital_bill_total_amoun($bill_details){
        $total = 0;
        $discount_total = 0;
        foreach($bill_details as $row){
            $price = $row['bill_quantity'] * $row['bill_amount'];
            $discount = $price * $row['bill_discount'] / 100;
            $total += ($price - $discount);
            $discount_total += $discount;
        }
        return [$total, $discount_total];
    }

    private static function update_inventory_transaction(int $pos_bill_id){
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        $sales_mains = $salesMainModel->where('pos_bill_id', $pos_bill_id)->findAll();
        $sales_id_list = BluelimeSystem::collect_field_value_from_object($sales_mains,'id');

        if(empty($sales_id_list))
            $sales_id_list = [-1];

        $sales_details = $salesDetailModel->whereIn('sales_id', $sales_id_list)->findAll();

        $item_id_list = [-99,...BluelimeSystem::collect_field_value_from_object($sales_details, 'item_id')];

        $item_batch_quantity_temp = $transactionModel->select('item_id, batch_number, MIN(expiry_date) as expiry_date, SUM(quantity) as batch_quantity')->where('store_id', 2)->whereIn('item_id',$item_id_list)->groupBy('item_id, batch_number')->having('batch_quantity >',0)->findAll();

        //BluelimeSystem::show_and_die($transactionModel->db->lastQuery);

        $item_batch_quantity = [];
        foreach($item_batch_quantity_temp as $temp_row){
            if(empty($item_batch_quantity['item_id'.$temp_row['item_id']]))
                $item_batch_quantity['item_id_'.$temp_row['item_id']] = [];
            
            array_push($item_batch_quantity['item_id_'.$temp_row['item_id']], $temp_row);
        }

        foreach($item_batch_quantity as $index => $temp){
            $item_batch_quantity[$index] = BluelimeSystem::sort_array($temp,['expiry_date']);
        }

        $transaction_data = [];
        foreach($sales_mains as $index => $sales_main){
            $store_id = ($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::DISPENSARY) ? 2 : 3;
            $transaction_type = ($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::DISPENSARY) ? BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_SALES : BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::COMPOUNDING_SALES;
            $sales_mains[$index]['sales_detail'] = [];
            foreach($sales_details as $sales_detail){
                
                if($sales_detail['sales_id'] == $sales_main['id']){
                    $quantity =  $sales_detail['quantity'];

                    foreach(($item_batch_quantity['item_id_'.$sales_detail['item_id']]??[]) as $batch_quantity){
                        $current_quantity = $quantity;

                        if($batch_quantity['batch_quantity'] <= 0)
                            continue;

                        if($current_quantity > $batch_quantity['batch_quantity']){
                            $current_quantity = $batch_quantity['batch_quantity'];
                        }
                        $quantity -= $current_quantity;

                        $transaction_data_temp = [
                            'store_id' => $store_id,
                            'item_id'  => $sales_detail['item_id'],
                            'batch_number' => $batch_quantity['batch_number'],
                            'expiry_date' => $batch_quantity['expiry_date'],
                            'transaction_type' => $transaction_type,
                            'transaction_reference' => $sales_main['id'],
                            'quantity' => -1 * $current_quantity
                        ];
                        array_push($transaction_data, $transaction_data_temp);

                        if($quantity <= 0)
                            break;
                    }

                    if($quantity > 0){
                        $transaction_data_temp = [
                            'store_id' => $store_id,
                            'item_id'  => $sales_detail['item_id'],
                            'batch_number' => 'DEFAULT',
                            'expiry_date' => '2100-01-01',
                            'transaction_type' => $transaction_type,
                            'transaction_reference' => $sales_main['id'],
                            'quantity' => -1 * $quantity
                        ];
                        array_push($transaction_data, $transaction_data_temp);
                    }
                }
            }
        }

        if(!empty($transaction_data))
            $transactionModel->insertBatch($transaction_data);
    }
    
}
