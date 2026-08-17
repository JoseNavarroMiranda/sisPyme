<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventory_Movements extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return \Database\Factories\InventoryMovementsFactory::new();
    }

    protected $table = 'inventory__movements';

    protected $fillable = [
        'type',
        'quantity',
        'description',
        'product_id',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
