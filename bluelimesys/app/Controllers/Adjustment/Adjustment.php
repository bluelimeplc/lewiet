<?php

namespace App\Controllers\Adjustment;

use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimeSystem;

class Adjustment extends \App\Controllers\BaseController
{
    public function index(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $store_id = $logged_user['extra']['default_store'];

        $items = BluelimeSystem::get_all_items_with_price_and_quantity($store_id);
        $data = ['items' => $items, 'logged_user' => $logged_user, 'active_menu'=>'active_menu'];

        return view('Inventory/Adjustment/adjustment', $data);
    }

    public function check_despensary_item_quantity_is_adjustable(){
        $item_id = $_POST['item_id'];
        $store_id = 2;
        $transactionModel = model('Inventory/TransactionModel');

        $transaction = $transactionModel->where('item_id', $item_id)->where('store_id', $store_id)->where('transaction_type', '100 Maker')->findAll();
        
        //BluelimeSystem::print_associated_array($transaction);

        return empty($transaction) ? BluelimeSystem::get_error_ajax_return('already adjusted') : BluelimeSystem::get_success_ajax_return('adjustable');
    }

    public function adjust_item_quantity_for_despensary(){
        $item_id = $_POST['item_id'];
        $quantity = $_POST['quantity'];
        $store_id = 2;
    
        $transactionModel = model('Inventory/TransactionModel');
        $transaction = $transactionModel->select('SUM(quantity) as quantity')->where('store_id', $store_id)->where('item_id', $item_id)->findAll();

        $quantity_old = $transaction[0]['quantity'] ?? 0;

        $quantity_new = $quantity - ($quantity_old - 100);

        $transaction_data = [
            'store_id'=> $store_id,
            'item_id'=> $item_id,
            'batch_number'=> 'Begin',
            'transaction_type'=> BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::ADJUSTMENT_BEGINNING,
            'transaction_reference'=> 0,
            'quantity'=> $quantity_new,
        ];
    
        if($quantity_new != 0)
            $transactionModel->insert($transaction_data);

        $transactionModel->where('store_id', $store_id)->where('item_id',$item_id)->where('transaction_type', '100 Maker')->delete();
        
        $transaction = $transactionModel->select('SUM(quantity) as quantity')->where('store_id', $store_id)->where('item_id', $item_id)->findAll();

        return BluelimeSystem::get_success_ajax_return($transaction[0]['quantity']);
    }

    public function adjust_item_quantity_for_main_store(){
        $item_id = $_POST['item_id'];
        $quantity = $_POST['quantity'];
        $expiry_date = $_POST['expiry_date'];
        $signature = $_GET['signature'] ?? '';
        $signature_ = intval(date('Hi'));
        if(!(($signature <= ($signature_ +1)) && ($signature >= ($signature_ -1))))
            return BluelimeSystem::get_error_ajax_return('Invalid signature');
        //$store_id = 1;
        
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $store_id = $logged_user['extra']['default_store'];
    
        $transactionModel = model('Inventory/TransactionModel');
        $transaction = $transactionModel->select('SUM(quantity) as quantity')->where('store_id', $store_id)->where('item_id', $item_id)->findAll();

        $quantity_old = $transaction[0]['quantity'] ?? 0;

        $quantity_new = $quantity - $quantity_old;

        $transaction_data = [
            'store_id'=> $store_id,
            'item_id'=> $item_id,
            'batch_number'=> 'Begin',
            'transaction_type'=> BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::ADJUSTMENT_BEGINNING,
            'transaction_reference'=> 0,
            'quantity'=> $quantity_new,
        ];
    
        if($quantity_new != 0)
            $transactionModel->insert($transaction_data);

        $expiry_row = ['item_id' => $item_id, 'expiry_date' => $expiry_date, 'quantity' => $quantity];
        $expiryDateTempModel = model('Inventory/ExpiryDateTempModel');
        $expiryDateTempModel->insert($expiry_row);
 
        $transaction = $transactionModel->select('SUM(quantity) as quantity')->where('store_id', $store_id)->where('item_id', $item_id)->findAll();

        return BluelimeSystem::get_success_ajax_return($transaction[0]['quantity']);
    }
}