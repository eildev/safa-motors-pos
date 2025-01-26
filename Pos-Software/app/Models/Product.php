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

    function saleUnit()
    {
        return $this->belongsTo(Unit::class, 'sale_unit', 'id');
    }
    function purchaseUnit()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit', 'id');
    }
    function damage()
    {
        return $this->hasMany(Damage::class, 'product_id');
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
    public function variations()
    {
        return $this->hasMany(Variation::class, 'product_id');
    }
    public function productvariation()
    {
        return $this->hasMany(Variation::class, 'product_id');
    }
}
