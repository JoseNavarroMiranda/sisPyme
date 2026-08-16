<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'total_amount',
        'status',
        'user_id',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(salesDetails::class, 'sales_id');
    }
}
