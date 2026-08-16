<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }
}
