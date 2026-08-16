<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class salesDetails extends Model
{
    protected $table = 'sales_details';

    protected $fillable = [
        'sales_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
