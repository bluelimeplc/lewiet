<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class AllergyModel extends Model
{
    protected $table      = 'hospital_allergies';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];

}