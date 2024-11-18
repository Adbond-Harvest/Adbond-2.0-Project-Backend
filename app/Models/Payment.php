<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use app\Models\PaymentMode;

class Payment extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function paymentPeriodStatus()
    {
        return $this->belongsTo(PaymentPeriodStatus::class);
    }

    public function paymentEvidence()
    {
        return $this->belongsTo(File::class, "evidence_file_id", "id");
    }

    public function paymentReceipt()
    {
        return $this->belongsTo(File::class, "receipt_file_id", "id");
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if(!$payment->confirmed && $payment->payment_mode_id) {
                $payment->confirmed = ($payment->payment_mode_id == PaymentMode::cardPayment()->id);
            }else{
                $payment->confirmed = false;
            }
        });
    }
}
