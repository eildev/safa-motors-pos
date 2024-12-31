<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];
    function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    function purchaseItem()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
