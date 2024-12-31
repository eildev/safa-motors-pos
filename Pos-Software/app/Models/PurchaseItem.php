<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];

    function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    function Purchas()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

}
