<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use app\Models\PaymentMode;
use app\Services\FileService;

class Payment extends Model
{
    use HasFactory;

    public static $type = "app\Models\Payment";

    /**
     * Get the parent purchase model (Order or Offer).
     */
    public function purchase(): MorphTo
    {
        return $this->morphTo();
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
            if($payment->confirmed==null && $payment->payment_mode_id) {
                $payment->confirmed = ($payment->payment_mode_id == PaymentMode::cardPayment()->id && $payment->flag==0 && $payment->success==1);
            }
        });

        static::created(function ($payment) {
            if($payment->evidence_file_id) self::updateFile($payment->evidence_file_id, $payment);
            if($payment->receipt_file_id) self::updateFile($payment->receipt_file_id, $payment);
        });
    }

    private static function updateFile($fileId, $clientPackage)
    {
        $fileService = new FileService;
        $file = $fileService->getFile($fileId);
        if($file && (!$file->belongs_id || !$file->belongs_type)){
            $fileMeta = ["belongsId"=>$clientPackage->id, "belongsType"=>self::$type];
            $fileService->updateFileObj($fileMeta, $file);
        }
    }
}
