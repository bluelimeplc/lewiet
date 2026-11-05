<?php

namespace App\Models\PeachtreeIdMap;

use CodeIgniter\Model;

class FromPeachtreeModel extends Model
{
    protected $table      = '_from_peachtree';
    protected $primaryKey = 'peachtree_id ';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['peachtree_id','bluelime_id','bluelime_id_full','item_description','quantity_on_hand', 'item_value','average_cost','location','flag'];
}