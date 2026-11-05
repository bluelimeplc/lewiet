<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class DiagnosisModel extends Model
{
    protected $table      = 'hospital_diagnoses'; 
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}
