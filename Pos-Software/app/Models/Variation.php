<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function stocks()
    {
        return $this->hasMany(Stocks::class, 'variation_id');
    }
    function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }
}