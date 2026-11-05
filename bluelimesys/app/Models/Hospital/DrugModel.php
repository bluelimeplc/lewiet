<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class DrugModel extends Model
{
    protected $table      = 'hospital_drugs';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];

}