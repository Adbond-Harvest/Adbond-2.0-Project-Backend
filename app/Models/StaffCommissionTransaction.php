<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCommissionTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'transaction_type',
        'balance'
    ];

    protected $casts = [
        'transaction_type' => 'string', // Ensure it's stored as a string
    ];

    /**
     * Get the user that owns the commission transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent transactionable model (Earning or Redemption)
     */
    public function transactionable()
    {
        return $this->morphTo('transaction');
    }
}
