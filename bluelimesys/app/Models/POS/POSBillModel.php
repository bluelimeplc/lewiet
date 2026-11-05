<?php

namespace App\Models\POS;

use CodeIgniter\Model;

class POSBillModel extends Model
{
    protected $table      = 'pos_bill';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['invoice_no', 'pos_bill_json', 'total', 'sales_id_list', 'type' , 'response_data', 'fs_number', 'machine_number','payment_method', 'transferred_amount', 'payment_reference', 'sent_to_pos_date', 'response_date', 'is_void','digital_payment_detail','digital_payment_status'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = ['pos_bill_json', 'sales_id_list'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowCallbacks = true;
    protected $afterFind      = ['change_model_items_to_array'];
}