<?php

namespace App\Models\PeachtreeIdMap;

use CodeIgniter\Model;

class BluelimeItemModel extends Model
{
    protected $table      = '_inventory_peachtree_item';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['item_id','peachtree_id','peachtree_description','quantity_on_hand', 'item_value','average_cost','location'];
}