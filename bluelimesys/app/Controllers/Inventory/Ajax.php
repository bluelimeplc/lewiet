<?php

namespace App\Controllers\Inventory;

use BluelimePOS;
use BluelimeSystem;

class Ajax extends \App\Controllers\BaseController
{
    public function index()
    {
        echo 'Welcome to ajax';
    }

    public function get_item_batch_quantity():string
    {
        $item_id = $_POST['item_id'] ?? -1;
        $store_id = $_POST['store_id'] ?? -1;

        $transactionModel = model('Inventory/TransactionModel');

        $item_quantity = $transactionModel->select('SUM(quantity) as quantity, batch_number')->where('store_id', $store_id)->where('item_id', $item_id)->groupBy('batch_number')->findAll();
        //return  $transactionModel->db->getLastQuery();
        return \BluelimeSystem::convert_array_to_string($item_quantity);
    }

}