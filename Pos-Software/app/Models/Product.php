<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id', 'id');
    }
    function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
    function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
    function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }
    function damage()
    {
        return $this->hasMany(Damage::class);
    }
    //
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'product_id');
    }
    function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function variation()
    {
        return $this->hasOne(Variation::class, 'product_id', 'id')->where('status', 'default');
    }
}
