<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    } //
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    } //
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'payment_method', 'id');
    } //
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'others_id', 'id');
    } //

    public function particularData()
    {
        $particulars = $this->particulars;

        // Check if particulars start with 'Sale#'
        if (strpos($particulars, 'Sale#') !== false) {
            preg_match('/\d+/', $particulars, $matches); // Extract the ID
            $saleId = $matches[0] ?? null;
            return $saleId ? Sale::find($saleId) : null; // Return sale data

            // Check if particulars start with 'Purchase#'
        } elseif (strpos($particulars, 'Purchase#') !== false) {
            preg_match('/\d+/', $particulars, $matches); // Extract the ID
            $purchaseId = $matches[0] ?? null;
            return $purchaseId ? Purchase::find($purchaseId) : null; // Return purchase data

            // Check for specific cases like 'Adjust Due Collection' or 'Return'
        } elseif (strpos($particulars, 'Adjust Due Collection') !== false || strpos($particulars, 'Return') !== false) {
            return Returns::find($this->others_id); // Match with others_id and return return data
        } elseif (strpos($particulars, 'OthersReceive') !== false || strpos($particulars, 'OthersPayment') !== false) {
            return Investor::find($this->others_id);
        }

        // Default case: return null if no match
        return null;
    }
}
