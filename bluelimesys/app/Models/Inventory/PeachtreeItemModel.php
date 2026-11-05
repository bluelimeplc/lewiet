<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class PeachtreeItemModel extends Model
{
    protected $table      = 'inventory_peachtree_item';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['item_id', 'peachtree_id' , 'peachtree_description','exported'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}