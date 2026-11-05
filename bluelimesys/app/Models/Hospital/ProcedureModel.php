<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ProcedureModel extends Model
{
    protected $table      = 'hospital_procedures';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}
