<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViaSale extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_name', 'id');
    } //
    public function viaProduct()
    {
        return $this->belongsTo(ViaProduct::class, 'via_product_id', 'id');
    } //
}
