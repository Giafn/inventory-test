<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'user_id',
        'number',
        'date',
    ];

    public function details()
    {
        return $this->hasMany(SalesDetail::class, 'sale_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
