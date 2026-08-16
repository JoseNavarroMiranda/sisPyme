<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'rfc',
    ];

    public function sales()
    {
        return $this->hasMany(Sales::class, 'customer_id');
    }
}
