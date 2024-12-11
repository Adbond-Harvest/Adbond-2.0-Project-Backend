<?php

namespace app\Services;

use app\Notifications\APIPasswordResetNotification;
use app\Exceptions\UserNotFoundException;

use app\Models\Payment;

use app\Helpers;

/**
 * Transaction service class
 */
class TransactionService
{
    public $clientId = null;
    public $count = null;

    public function transactions($with=[], $offset=0, $perPage=null)
    {
        $query = Payment::with($with);
        if($this->clientId) $query->where("client_id", $this->clientId);
        if($this->count) return $query->count();

        if($perPage==null) $perPage=env('PAGINATION_PER_PAGE');
        return $query->offset($offset)->limit($perPage)->orderBy("created_at", "DESC")->get();
    }

    public function transaction($id, $with=[])
    {
        return Payment::with($with)->where("id", $id)->first();
    }
    
}
