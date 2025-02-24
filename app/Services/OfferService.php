<?php

namespace app\Services;

use app\Models\Offer;
use app\Models\PaymentStatus;

use app\Enums\OfferApprovalStatus;

class OfferService
{
    public $clientId = null;
    public $filter = null;
    public $count = null;
    public $sales = null;

    public function save($data)
    {
        $offer = new Offer;

        $offer->client_id = $data['clientId'];
        $offer->package_id = $data['packageId'];
        $offer->client_package_id = $data['assetId'];
        $offer->units = $data['units'];
        $offer->project_id = $data['projectId'];
        $offer->price = $data['price'];
        $offer->package_price = $data['packagePrice'];
        if(isset($data['resellOrderId'])) {
            $offer->resell_order_id = $data['resellOrderId'];
            $offer->payment_status_id = PaymentStatus::pending()->id;
        }
        if(isset($data['active'])) $offer->active = $data['active'];
        // $offer->payment_status_id = PaymentStatus::pending()->id;

        $offer->save();

        return $offer;
    }

    public function update($data, $offer)
    {
        if(isset($data['price'])) {
            $offer->price = $data['price'];
            if($offer->approved == 0) $offer->approved = null;
        }
        if(isset($data['paymentStatusId'])) $offer->payment_status_id = $data['paymentStatusId'];
        $offer->update();

        return $offer;
    }

    public function bidAccepted($offer, $bid)
    {
        $offer->accepted_bid_id = $bid->id;
        $offer->active = false;

        $offer->update();

        return $offer;
    }

    public function offers($with=[], $offset=0, $perPage=null)
    {
        $query = Offer::with($with);
        if($this->clientId) $query->where("client_id", $this->clientId);
        if($this->sales == false) $query->whereNull("resell_order_id")->whereNull("accepted_bid_id");
        if($this->sales == true) $query->whereNotNull("resell_order_id")->OrWhereNotNull("accepted_bid_id");
        if($this->filter && is_array($this->filter)) {
            $filter = $this->filter;
            if(isset($filter['text'])) {
                $query->whereHas('package', function($packageQuery) use($filter) {
                    $packageQuery->where("name", "LIKE", "%".$filter['text']."%")
                    ->orWhereHas('project', function($projectQuery) use($filter) {
                        $projectQuery->where("name", "LIKE", "%".$filter['text']."%");
                    });
                });
            }
            if(isset($filter['date'])) $query = $query->whereDate("created_at", $filter['date']);
            
            if(isset($filter['status'])) {
                switch($filter['status']) {
                    case OfferApprovalStatus::PENDING->value : $query->whereNull("approved"); break;
                    case OfferApprovalStatus::APPROVED->value : $query->where("approved", 1); break;
                    case OfferApprovalStatus::REJECTED->value : $query->where("approved", 0); break;
                }
            }
        }
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function getOffersByAssetId($clientPackageId)
    {
        return Offer::where("client_package_id", $clientPackageId)->first();
    }

    public function offer($id, $with=[])
    {
        return Offer::with($with)->where("id", $id)->first();
    }

    public function approve($offer)
    {
        $offer->approved = true;
        $offer->update();

        return $offer;
    }

    public function reject($offer, $reason)
    {
        $offer->approved = false;
        $offer->rejected_reason = $reason;
        $offer->update();

        return $offer;
    }
}