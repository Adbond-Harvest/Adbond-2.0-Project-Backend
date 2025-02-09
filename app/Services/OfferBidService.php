<?php

namespace app\Services;

use app\Models\Offer;
use app\Models\OfferBid;
use app\Models\PaymentStatus;

use app\Enums\OfferApprovalStatus;

class OfferBidService
{
    public $clientId = null;
    public $filter = null;
    public $count = null;

    public function save($data)
    {
        $bid = new OfferBid;

        $bid->client_id = $data['clientId'];
        $bid->offer_id = $data['offerId'];
        $bid->price = $data['price'];

        $bid->save();

        return $bid;
    }

    public function bids($with=[], $offset=0, $perPage=null)
    {
        $query = OfferBid::with($with);
        if($this->clientId) $query->where("client_id", $this->clientId);
        if($this->filter && is_array($this->filter)) {
            $filter = $this->filter;
            if(isset($filter['text'])) {
                $query->whereHas('offer', function($offerQuery) use($filter) {
                    $offerQuery->whereHas('package', function($packageQuery) use($filter) {
                        $packageQuery->where("name", "LIKE", "%".$filter['text']."%")
                        ->orWhereHas('project', function($projectQuery) use($filter) {
                            $projectQuery->where("name", "LIKE", "%".$filter['text']."%");
                        });
                    });
                });
            }
            if(isset($filter['offerId'])) $query = $query->where("offer_id", $filter['offerId']);
            if(isset($filter['date'])) $query = $query->whereDate("created_at", $filter['date']);
            
            if(isset($filter['status'])) {
                switch($filter['status']) {
                    case OfferApprovalStatus::PENDING->value : $query->whereNull("accepted"); break;
                    case OfferApprovalStatus::APPROVED->value : $query->where("accepted", 1); break;
                    case OfferApprovalStatus::REJECTED->value : $query->where("accepted", 0); break;
                }
            }
        }
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function getBid($bidId, $with=[])
    {
        return OfferBid::with($with)->where("id", $bidId)->first();
    }

    public function accept($bid) {
        $bid->accepted = true;
        $bid->update();

        return $bid;
    }

    public function reject($bid) {
        $bid->accepted = false;
        $bid->update();

        return $bid;
    }
}