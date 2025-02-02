<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Damage extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];

    function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
