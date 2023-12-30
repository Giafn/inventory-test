<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'user_id',
        'number',
        'date',
    ];

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
