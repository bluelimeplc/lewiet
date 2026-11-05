<?php

namespace App\Models\POS;

use CodeIgniter\Model;

class CashierSummaryModel extends Model
{
    protected $table      = 'pos_cashier_summary';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['summary_of','summary_date', 'z_total', 'deposited_total', 'cashier_id', 'cashier_name' ,'cashier_document', 'approved_by' , 'summary_status'];

    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowCallbacks = true;
   
}