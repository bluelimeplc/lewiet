<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ProcedureResultsModel extends Model
{
    protected $table      = 'hospital_procedure_results';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['patient_id','order_id','order_detail_id','procedure_id','procedure_name', 'attribute_id', 'attribute_name', 'result','registered_by'];
    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}