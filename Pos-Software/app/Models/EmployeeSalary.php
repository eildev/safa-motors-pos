<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];

   public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
   public function emplyee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
