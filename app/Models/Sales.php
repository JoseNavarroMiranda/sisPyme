<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Sales extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(salesDetails::class, 'sales_id');
    }
}
