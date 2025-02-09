<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\Client\AcceptBid;
use app\Http\Requests\Client\OfferBid;

use app\Http\Resources\OfferBidResource;

use app\Services\OfferBidService;
use app\Services\OfferService;

use app\Utilities;

class OfferBidController extends Controller
{
    private $bidService;
    private $offerService;

    public function __construct()
    {
        $this->bidService = new OfferBidService;
        $this->offerService = new OfferService;
    }

    public function bid(OfferBid $request)
    {
        try{
            $data = $request->validated();
            $offer = $this->offerService->offer($data['offerId']);
            if(!$offer) return Utilities::error402("Offer not found");

            if($offer->client_id == Auth::guard("client")->user()->id) return Utilities::error402("You cannot bid on your own offer");
            $data['clientId'] = Auth::guard("client")->user()->id;

            if($offer->active==0) return Utilities::error402("Offer not active");
            if(!$offer->approved || $offer->approved==0) return Utilities::error402("Offer is not approved");
            if($offer->accepted_bid_id) return Utilities::error402("Bid has been accepted on this offer");
            if($offer->resell_order_id) return Utilities::error402("Bid is not open to offer");
            
            $bid = $this->bidService->save($data);

            return Utilities::ok(new OfferBidResource($bid));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function acceptBid(AcceptBid $request)
    {
        $data = $request->validated();
        $bid = $this->bidService->getBid($data['bidId']);
        if(!$bid) return Utilities::error402("Bid not found");

        if($bid->approved && $bid->approved==1) return Utilities::error402("This bid has already been accepted");
        if($bid->approved && $bid->approved==0) return Utilities::error402("This bid has been rejected");

        if(!$bid?->offer) {
            return Utilities::error402("Bid offer not found");
        }else{
            $offer = $bid->offer;
        }
        if($offer->client_id != Auth::guard("client")->user()->id) return Utilities::error402("You are not authorized to carry out this operation");
        if($offer->accepted_bid_id || $offer->resell_order_id) return Utilities::error402("This bid is no longer open for bidding");
        if($offer->active == 0) return Utilities::error402("This Offer is no longer Active");

        $bid = $this->bidService->accept($bid);
        $offer = $this->offerService->bidAccepted($offer, $bid);

        return Utilities::okay("Bid has been Accepted and Offer Closed for Bidding");
    }

    public function rejectBid(AcceptBid $request)
    {
        $data = $request->validated();
        $bid = $this->bidService->getBid($data['bidId']);
        if(!$bid) return Utilities::error402("Bid not found");

        if($bid->approved && $bid->approved==1) return Utilities::error402("This bid has been accepted");
        if($bid->approved && $bid->approved==0) return Utilities::error402("This bid has already been rejected");

        if(!$bid?->offer) {
            return Utilities::error402("Bid offer not found");
        }else{
            $offer = $bid->offer;
        }
        if($offer->client_id != Auth::guard("client")->user()->id) return Utilities::error402("You are not authorized to carry out this operation");
        if($offer->accepted_bid_id || $offer->resell_order_id) return Utilities::error402("This bid is no longer open for bidding");
        if($offer->active == 0) return Utilities::error402("This Offer is no longer Active");

        $bid = $this->bidService->reject($bid);

        return Utilities::okay("Bid has been Accepted and Offer Closed for Bidding");
    }

    public function myBids(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');

        $this->bidService->filter = $filter;
        $this->bidService->clientId = Auth::guard("client")->user()->id;
    }

    public function getBid($bidId)
    {
        $bid = $this->bidService->getBid($bidId);

        return Utilities::ok(new OfferBidResource($bid));
    }
}
