<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class ExpiryDateTempModel extends Model
{
    protected $table      = 'inventory_expiry_date_temp';
    protected $primaryKey = 'id';
 
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
   

    protected $allowedFields = ['item_id', 'expiry_date' , 'quantity'];

}