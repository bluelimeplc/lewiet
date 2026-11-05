<?php

namespace App\Controllers;

use BluelimeConfiguration;
use BluelimeSystem;

class DataCorrection extends BaseController
{
    public function index()
    {
        return "The purpose of this controller is to correct database data on the time of import or update. Procedures should run onece in life time.";

    }

    public function mukera(){
        $transactionModel = model('Inventory/TransactionModel');
        $transactionModel = $transactionModel->where('id',80795);
        $builder = $transactionModel->db;
        $builder->where('id', 80795);
        $builder->set("updater", "CONCAT(updater,', mukera')", false);
        $builder->update();

        // $transactionModel = $transactionModel->where('id',80795);
        // $transactionModel->update();
        echo 'Done';
    }

    private function write_end_time_log($log_file, $process_name, $started_at){
        $ended_at = time();
        $time_diff = $ended_at - $started_at;
        $output = "\n$process_name Total time taken: ".intval($time_diff/60)." : ".($time_diff%60)." \n";
        fwrite($log_file, $output);
    }

    public function calculate_average_cost_for_old_grv(){
        //TODO run only once
        $signature = $_GET['signature'] ?? '';
        if($signature != date("mdhi"))
            return 'Invalid signature. Hint: mdhi '. date("Y-m-d h:i:s");


        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        
        $log_file = fopen(WRITEPATH.'/logs/Bluelime/average_cost_'.time().'.txt', 'w');
        
        $started_at = time();
        $started_at2 = time();
        $output = "fill_unit_cost_from_old_grvs Started at: ".date("Y-m-d h:i")."\n";
        fwrite($log_file, $output);
        $this->fill_unit_cost_from_old_grvs();
        $this->write_end_time_log($log_file, "fill_unit_cost_from_old_grvs", $started_at2);

        $output = "\ncalculate_average_cost_for_begining Started at: ".date("Y-m-d h:i")."\n";
        fwrite($log_file, $output);
        $started_at2 = time();
        $this->calculate_average_cost_for_begining();
        $this->write_end_time_log($log_file, "calculate_average_cost_for_begining", $started_at2);

        $grvMains = $grvMainModel->where('created_at >=', '2025-07-08')->orderBy('id','ASC')->findAll();
        $grvMains = BluelimeSystem::index_object_list_array_by_unique_field($grvMains, 'id');
        $grv_id_list = array_column($grvMains, 'id');
        $grvDetails = $grvDetailModel->whereIn('grv_id', $grv_id_list)->findAll();
        foreach($grvDetails as $grvDetail)
        {
            $grvMains['id_'.$grvDetail['grv_id']]['grv_detail'] ??= [];
            $grvMains['id_'.$grvDetail['grv_id']]['grv_detail'][] = $grvDetail;
        }

    
        
        $output = "\nGRV Started at: ".date("Y-m-d h:i")."\n";
        $output .= "GRV Detail ID\tItem ID\tGRV ID\tQuantity\tUnit Price\tUpto Balance\tLast Unit Cost\tNew Average Cost\n";
        fwrite($log_file, $output);
        $started_at2 = time();

        foreach($grvMains as $grvMain)
        {
            if(empty($grvMain['grv_detail']))
                continue;
            foreach($grvMain['grv_detail'] as $grvDetail)
            {
                $grv_date = substr($grvMain['created_at'],0,10);
                //Get upto balance
                $upto_balance = $this->get_upto_balance($grvDetail['item_id'], $grv_date);
                
                //Get last unit cost
                $last_unit_cost = $this->get_last_unit_cost($grvDetail['item_id'], $grv_date);
                
                $new_average_cost = $grvDetail['unit_price'];
                if($upto_balance > 0)
                    $new_average_cost = (($upto_balance * $last_unit_cost) + ($grvDetail['quantity'] * $grvDetail['unit_price'])) / ($upto_balance + $grvDetail['quantity']);
                
                $grvDetailModel->update($grvDetail['id'], ['average_cost' => $new_average_cost]);
                $output = "{$grvDetail['id']}\t{$grvDetail['item_id']}\t{$grvDetail['grv_id']}\t{$grvDetail['quantity']}\t{$grvDetail['unit_price']}\t{$upto_balance}\t{$last_unit_cost}\t{$new_average_cost}\n";
                fwrite($log_file, $output);
                //echo $output;
            
                //Update average cost in transaction
                $this->update_transaction_average_cost_forward($grvDetail['item_id'], $new_average_cost, $grv_date);
                $transactionModel->where('store_id',1)->where('item_id', $grvDetail['item_id'])->where('transaction_reference', $grvMain['id'])->where('transaction_type', BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE)->update(row:['unit_cost' => $new_average_cost, 'updater' => "grv_updater"]);
            }
        }

        $this->write_end_time_log($log_file, "GRV", $started_at2);


        /* OLD
        foreach($grvDetails as $grvDetail)
        {
            //Get upto balance
            [$upto_balance,$grv_transaction_id] = $this->get_upto_balance($grvDetail['item_id'], $grvDetail['grv_id']);
            
            //Get last unit cost
            $last_unit_cost = 0;
            if($grv_transaction_id > 0)
                $last_unit_cost = $this->get_last_unit_cost($grvDetail['item_id'], $grv_transaction_id);
            
            $new_average_cost = $grvDetail['unit_price'];
            if($upto_balance > 0)
                $new_average_cost = (($upto_balance * $last_unit_cost) + ($grvDetail['quantity'] * $grvDetail['unit_price'])) / ($upto_balance + $grvDetail['quantity']);
            
            $grvDetailModel->update($grvDetail['id'], ['average_cost' => $new_average_cost]);
            $output = "{$grvDetail['id']}\t{$grvDetail['item_id']}\t{$grvDetail['grv_id']}\t{$grvDetail['quantity']}\t{$grvDetail['unit_price']}\t{$upto_balance}\t{$last_unit_cost}\t{$new_average_cost}\n";
            fwrite($log_file, $output);
            //echo $output;
        
            //Update average cost in transaction
            $this->update_transaction_average_cost_upto_next_grv($grvDetail['item_id'], $grv_transaction_id, $new_average_cost, $grvDetail['grv_id']);
        }
        */
        
        $output = "\nFill from near transaction Started at: ".date("Y-m-d h:i")."\n";
        fwrite($log_file, $output);
        $started_at2 = time();
        $this->fill_from_near_transaction();
        $this->write_end_time_log($log_file, "Fill from near transaction", $started_at2);

        $ended_at = time();
        $time_diff = $ended_at - $started_at;
        $output = "\nTotal time taken: ".intval($time_diff/60)." : ".($time_diff%60)." \n";
        fwrite($log_file, $output);
        
        return 'done 5 <br>'. date("Y-m-d h:i");
    }

    private function calculate_average_cost_for_begining(){
        $transactionModel = model('Inventory/TransactionModel');

        //Get all transactions which have average cost and created after 2025-07-08
        // Do it for store then for other
        $transactions = $transactionModel->select('MAX(id) AS id, item_id, unit_cost')
                                        ->where('created_at >=', '2025-07-08')
                                        ->where('unit_cost IS NOT', NULL)
                                        ->where('unit_cost >', 0)
                                        ->where('transaction_type', 'BEGINNING')
                                        ->where('store_id', 1)
                                        ->groupBy('item_id, unit_cost')
                                        ->orderBy('id','ASC')->findAll();

        foreach($transactions as $transaction)
        {
            //Update average cost in transaction
            $this->update_transaction_average_cost_upto_next_grv_for_begining($transaction['item_id'], $transaction['unit_cost'],"1");
        }

        $transactions = $transactionModel->select('MAX(id) AS id, item_id, unit_cost')
                                        ->where('created_at >=', '2025-07-08')
                                        ->where('unit_cost IS NOT', NULL)
                                        ->where('unit_cost >', 0)
                                        ->where('transaction_type', 'BEGINNING')
                                        ->where('store_id !=', 1)
                                        ->groupBy('item_id, unit_cost')
                                        ->orderBy('id','ASC')->findAll();

        foreach($transactions as $transaction)
        {
            //Update average cost in transaction
            $this->update_transaction_average_cost_upto_next_grv_for_begining($transaction['item_id'], $transaction['unit_cost']);
        }
        return 'done 2';
    }

    private function fill_unit_cost_from_old_grvs(){
        $transactionModel = model('Inventory/TransactionModel');
        $transactions = $transactionModel->select('DISTINCT(item_id) AS item_id')->where('created_at >=', '2025-07-08')->where('unit_cost', NULL)->where('quantity >', 0)->where('transaction_type', BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::BEGINNING)->findAll();
        $item_id_list = array_column($transactions, 'item_id');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $grvMainModel = model('Inventory/GRVMainModel');
        $grv_id_list = $grvMainModel->where('status >=', BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)->where('created_at <', '2025-07-08')->orderBy('id','ASC')->findColumn('id');

        $grv_detail_id_list = $grvDetailModel->select('MAX(id) AS id, item_id')->whereIn('grv_id', $grv_id_list)->whereIn('item_id', $item_id_list)->groupBy('item_id')->findColumn('id');
        $grv_detail_id_list ??= [-99];
        $grvDetails = $grvDetailModel->select('item_id, unit_price')->whereIn('id', $grv_detail_id_list)->findAll();

        foreach($grvDetails as $grvDetail)
        {
            $item_id = $grvDetail['item_id'];
            $unit_cost = $grvDetail['unit_price'];

            $transactionModel->where('created_at >=', '2025-07-08')
                             ->where('unit_cost', NULL)
                             ->where('item_id', $item_id)
                             ->where('transaction_type', BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::BEGINNING)
                             ->where('deleted_at', NULL)
                             ->update(row:['unit_cost' => $unit_cost, 'updater' => "fill_unit_cost_from_old_grvs"]);
        }
        return 'done 1';
    }

    private function fill_from_near_transaction(){
        $trasactionModel = model('Inventory/TransactionModel');
        $transactions = $trasactionModel->where('created_at >=', '2025-07-08')->where('unit_cost', NULL)->where('item_id >',0)->findAll();
        foreach($transactions as $transaction)
        {
            $item_id = $transaction['item_id'];
            $transaction_id = $transaction['id'];

            //Get last transaction with unit cost
            $last_transaction = $trasactionModel->where('id <', $transaction_id)->where('item_id', $item_id)->where('unit_cost IS NOT', NULL)->where('unit_cost >', 0)->orderBy('id','DESC')->first();
            $next_transaction = $trasactionModel->where('id >', $transaction_id)->where('item_id', $item_id)->where('unit_cost IS NOT', NULL)->where('unit_cost >', 0)->orderBy('id','ASC')->first();
            $unit_cost = $last_transaction['unit_cost'] ?? NULL;
            if(!empty($last_transaction)){
                if(!empty($next_transaction)){
                    $last_transaction_age = strtotime($transaction['created_at']) - strtotime($last_transaction['created_at']);
                    $next_transaction_age = strtotime($next_transaction['created_at']) - strtotime($transaction['created_at']);
                    if($next_transaction_age < $last_transaction_age){
                        $unit_cost = $next_transaction['unit_cost'] ?? NULL;
                    }
                }
            }
            else{
                $unit_cost = $next_transaction['unit_cost'] ?? NULL;
            }
            if(empty($unit_cost))
                continue;
            $trasactionModel->update($transaction_id, ['unit_cost' => $unit_cost, 'updater' => "fill_from_near_transaction"]);
        }
        return 'done 3';
    }

    private function get_upto_balance($item_id, $grv_date) {
        $transactionModel = model('Inventory/TransactionModel');
        
        //Get upto grv transaction balance
        $upto_balance = $transactionModel->select('SUM(quantity) AS quantity')->where('store_id',1)->where('created_at >=', '2025-07-08')->where('item_id', $item_id)->where('created_at <', $grv_date)->findAll();
        $upto_balance = $upto_balance[0]['quantity'] ?? 0;

        return $upto_balance;
    }

    private function get_last_unit_cost($item_id, $grv_date) {
        $transactionModel = model('Inventory/TransactionModel');
    
        //Get upto grv transaction balance
        $last_transaction = $transactionModel->where('created_at >=', '2025-07-08')->where('store_id',1)->where('item_id', $item_id)->where('created_at <', $grv_date)->orderBy('id','DESC')->first();
        $last_unit_cost = $last_transaction['unit_cost'] ?? 0;
        
        return $last_unit_cost;
    }

    private function update_transaction_average_cost_forward($item_id, $average_cost, $grv_date) {
        $transactionModel = model('Inventory/TransactionModel');
        $transactionModel = $transactionModel->where('created_at >=', '2025-07-08')->where('item_id', $item_id)->where('created_at >=', $grv_date)->where('transaction_type !=', BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE);
        
        //Update average cost in grv transaction
        $transactionModel->update(row:['unit_cost' => $average_cost, 'updater' => "update_transaction_average_cost_forward"]);
    }

    //
    private function update_transaction_average_cost_upto_next_grv($item_id, $grv_transaction_id, $average_cost, $grv_id) {
        if($grv_transaction_id <= 0)
            return;

        $transactionModel = model('Inventory/TransactionModel');
        //Get next grv transaction id
        $next_grv_transaction = $transactionModel->where('created_at >=', '2025-07-08')->where('item_id', $item_id)->where('id >', $grv_transaction_id)->where('transaction_reference !=', $grv_id)->where('transaction_type', BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE)->orderBy('id','ASC')->first();
        $next_grv_transaction_id = $next_grv_transaction['id'] ?? 0;

        $transactionModel = $transactionModel->where('created_at >=', '2025-07-08')->where('item_id', $item_id)->where('id >=', $grv_transaction_id)->where('unit_cost', NULL);
        if($next_grv_transaction_id > 0)
            $transactionModel = $transactionModel->where('id <', $next_grv_transaction_id);
        
        //Update average cost in grv transaction
        $transactionModel->update(row:['unit_cost' => $average_cost, 'updater' => "update_transaction_average_cost_upto_next_grv"]);
    }

    private function update_transaction_average_cost_upto_next_grv_for_begining($item_id, $average_cost, $for_store = "") {
        $transactionModel = model('Inventory/TransactionModel');
        //Get next grv transaction id
        $next_grv_transaction = $transactionModel->where('created_at >=', '2025-07-08')->where('item_id', $item_id)->where('transaction_type', BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE)->orderBy('id','ASC')->first();
        $next_grv_transaction_id = $next_grv_transaction['id'] ?? 0;

        $transactionModel = $transactionModel->where('created_at >=', '2025-07-08')->where('item_id', $item_id)->where('unit_cost', NULL);
        if($next_grv_transaction_id > 0)
            $transactionModel = $transactionModel->where('id <', $next_grv_transaction_id);
        
        //Update average cost in grv transaction
        $transactionModel->update(row:['unit_cost' => $average_cost, 'updater' => "$for_store update_transaction_average_cost_upto_next_grv_for_begining"]);
    }
}