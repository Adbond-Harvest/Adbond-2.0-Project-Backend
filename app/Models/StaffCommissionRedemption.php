<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Models\StaffCommissionTransaction;

use app\Enums\CommissionTransactionType;

class StaffCommissionRedemption extends Model
{
    use HasFactory;

    public static $type = "app\Models\StaffCommissionRedemption";

    protected $fillable = [
        'user_id',
        'amount',
        'bank_account_id'
    ];

    /**
     * Get the user that owns the commission redemption
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bank account associated with this redemption
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Get the transaction record for this redemption
     */
    public function transaction()
    {
        return $this->morphOne(StaffCommissionTransaction::class, 'transaction');
    }

    protected static function boot()
    {
        parent::boot();
        
        // After creating a new redemption
        static::created(function ($redemption) {
            // get the latest transaction for this user
            // $latestTransaction = StaffCommissionTransaction::where("user_id", $redemption->user_id)->orderBy("created_at", "DESC")->first(); 

            // if($latestTransaction) {
            // // Create corresponding transaction record
            //     StaffCommissionTransaction::create([
            //         'user_id' => $redemption->user_id,
            //         'transaction_id' => $redemption->id,
            //         'transaction_type' => CommissionTransactionType::REDEMPTION->value,
            //         'balance' => $latestTransaction->balance - $redemption->amount, // Negative amount for redemption
            //     ]);
            // }
        });

        // Before deleting a redemption
        static::deleting(function ($redemption) {
            // Delete associated transaction
            StaffCommissionTransaction::where([
                'transaction_id' => $redemption->id,
                'transaction_type' => CommissionTransactionType::REDEMPTION->value
            ])->delete();
        });
    }
}
