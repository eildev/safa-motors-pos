<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_account_id', 'id');
    } //
    public function expenseCat()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id', 'id');
    } //


}
