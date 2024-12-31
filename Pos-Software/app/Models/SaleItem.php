<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];
    function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    function saleId()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
