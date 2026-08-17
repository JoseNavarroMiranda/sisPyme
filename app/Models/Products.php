<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'image_path',
        'category_id',
        'supplier_id',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }
}
