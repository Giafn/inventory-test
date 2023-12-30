<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'code',
        'name',
        'price',
        'stock',
    ];

    // have many purchase details
    public function purchase()
    {
        return $this->hasMany(PurchaseDetail::class, 'inventory_id');
    }

    // have many sales details
    public function sales()
    {
        return $this->hasMany(SalesDetail::class, 'inventory_id');
    }
}
