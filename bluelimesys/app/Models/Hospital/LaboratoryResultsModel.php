<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class LaboratoryResultsModel extends Model
{
    protected $table      = 'hospital_laboratory_results';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['order_id','order_detail_id','test_id', 'attribute_id', 'attribute_name', 'result','reference_range','result_flag','registered_by'];
    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}