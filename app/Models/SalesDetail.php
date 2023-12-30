<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;

    protected $table = 'sales_details';

    protected $fillable = [
        'sales_id',
        'inventory_id',
        'qty',
        'price',
    ];

    // belongs to sales
    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    // belongs to inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
