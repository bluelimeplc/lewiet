<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class TreatmentModel extends Model
{
    protected $table      = 'hospital_treatments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}
