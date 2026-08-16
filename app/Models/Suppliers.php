<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'suppliers_rfc',
    ];

    public function products()
    {
        return $this->hasMany(Products::class, 'supplier_id');
    }
}
