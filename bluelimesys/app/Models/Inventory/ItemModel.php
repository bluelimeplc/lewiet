<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table      = 'inventory_items';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['item_generic_name', 'item_category' , 'item_brand_name', 'item_code','strength','preparation', 'item_category' ,'item_type' ,'measurement', 'min_balance', 'compounding_measurement', 'compounding_volume', 'notify_expiry', 'is_active', 'is_taxable','is_perishable', 'item_registered_by', 'item_registered_by_name'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}