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
<<<<<<< HEAD
    function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }
=======
    // function size()
    // {
    //     return $this->belongsTo(Size::class, 'size');
    // }
>>>>>>> 391dcfb33e304f64965f3150c664fcc0fdf5afcd
}
