<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTotalReferralEarningsView extends Model
{
    protected $table = 'client_total_referral_earnings_view';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
}
