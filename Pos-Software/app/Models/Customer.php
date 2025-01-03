<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
