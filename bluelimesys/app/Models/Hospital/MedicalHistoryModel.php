<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class MedicalHistoryModel extends Model
{
    protected $table      = 'hospital_medical_history';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}
