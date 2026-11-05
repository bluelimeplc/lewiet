<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class ItemCategoryModel extends Model
{
    protected $table      = 'inventory_item_category';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['category_name', 'description' , 'registered_by'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}