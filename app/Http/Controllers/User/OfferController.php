<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Requests\User\ApproveOffer;
use app\Http\Requests\User\RejectOffer;

use app\Http\Resources\OfferResource;

use app\Services\OfferService;

use app\Enums\OfferApprovalStatus;

use app\Utilities;

class OfferController extends Controller
{
    private $offerService;

    public function __construct()
    {
        $this->offerService = new OfferService;
    }

    public function offers(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter['status'] = OfferApprovalStatus::PENDING->value;
        $this->offerService->filter = $filter;

        $offers = $this->offerService->offers(['client'], $offset, $perPage);

        $this->offerService->count = true;
        $offersCount = $this->offerService->offers();

        return Utilities::paginatedOkay(OfferResource::collection($offers), $page, $perPage, $offersCount);
    }

    public function approve(ApproveOffer $request)
    {
        try{
            $data = $request->validated();
            $offer = $this->offerService->offer($data['offerId']);
            if(!$offer) return Utilities::error402("Offer not found");

            if($offer->approved && $offer->approved==0) return Utilities::error402("This offer has been rejected");

            if($offer->approved && $offer->approved==1) return Utilities::error402("This offer has already been Approved");

            $offer = $this->offerService->approve($offer);

            return Utilities::okay("Offer approved successfully");

        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function reject(RejectOffer $request)
    {
        try{
            $data = $request->validated();
            $offer = $this->offerService->offer($data['offerId']);
            if(!$offer) return Utilities::error402("Offer not found");

            if($offer->approved && $offer->approved==0) return Utilities::error402("This offer has already been rejected");

            if($offer->approved && $offer->approved==1) return Utilities::error402("This offer has been Approved");

            $offer = $this->offerService->reject($offer, $data['reason']);

            return Utilities::okay("Offer Rejected");

        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }
}
