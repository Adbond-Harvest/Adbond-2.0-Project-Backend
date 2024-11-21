<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Models\StaffCommissionTransaction;

use app\Enums\CommissionTransactionType;

class StaffCommissionEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'commission',
        'commission_amount',
        'tax',
        'commission_after_tax'
    ];

    /**
     * Get the user that owns the commission earning
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this earning
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the transaction record for this earning
     */
    public function transaction()
    {
        return $this->morphOne(StaffCommissionTransaction::class, 'transaction');
    }

    protected static function boot()
    {
        parent::boot();

        // After creating a new commission earning
        static::created(function ($earning) {
            // get the latest transaction for this user
            $latestTransaction = StaffCommissionTransaction::where("user_id", $earning->user_id)->orderBy("created_at", "DESC")->first(); 
            // Create corresponding transaction record
            $transactionType = (string) CommissionTransactionType::EARNING->value;
            // dd($transactionType);
            StaffCommissionTransaction::create([
                'user_id' => $earning->user_id,
                'transaction_id' => $earning->id,
                'transaction_type' => $transactionType,
                'balance' => ($latestTransaction) ? $latestTransaction->balance + $earning->commission_after_tax : $earning->commission_after_tax,
            ]);
        });

        // Before deleting a commission earning
        static::deleting(function ($earning) {
            // Delete associated transaction
            StaffCommissionTransaction::where([
                'transaction_id' => $earning->id,
                'transaction_type' => CommissionTransactionType::EARNING->value
            ])->delete();
        });
    }
}
