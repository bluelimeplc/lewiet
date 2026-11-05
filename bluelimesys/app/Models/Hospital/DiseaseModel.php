<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class DiseaseModel extends Model
{
    protected $table      = 'hospital_diseases';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}
