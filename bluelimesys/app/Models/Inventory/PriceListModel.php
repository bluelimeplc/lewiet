<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class PriceListModel extends Model
{
    protected $table      = 'inventory_pricelist';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['item_id', 'unit_price', 'store_id', 'effective_date', 'registered_by'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}