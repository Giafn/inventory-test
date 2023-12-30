<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_details';

    protected $fillable = [
        'purchase_id',
        'inventory_id',
        'qty',
        'price',
    ];

    // belongs to purchase
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    // belongs to inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
