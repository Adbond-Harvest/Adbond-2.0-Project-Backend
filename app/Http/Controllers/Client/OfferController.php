<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Requests\Client\CreateOffer;
use app\Http\Requests\Client\UpdateOffer;

use app\Http\Resources\OfferResource;

use app\Services\OfferService;
use app\Services\ResellOrderService;
use app\Services\ClientPackageService;
use app\Services\NotificationService;

use app\Enums\ClientPackageOrigin;
use app\Enums\PackageType;
use app\Enums\OfferApprovalStatus;
use app\Enums\NotificationType;

use app\Utilities;

class OfferController extends Controller
{
    private $offerService;
    private $clientPackageService;
    private $resellOrderService;
    private $notificationService;

    public function __construct()
    {
        $this->offerService = new OfferService;
        $this->clientPackageService = new ClientPackageService;
        $this->resellOrderService = new ResellOrderService;
        $this->notificationService = new NotificationService;
    }

    public function create(CreateOffer $request)
    {
        try{
            $data = $request->validated();
            $clientPackage = $this->clientPackageService->clientPackage($data['assetId']);
            if(!$clientPackage) return Utilities::error402("Asset not found");

            if($clientPackage->client_id != Auth::guard("client")->user()->id) return Utilities::error402("You are not authorized to create an offer on an asset you don't own");

            if($clientPackage->sold == 1) return Utilities::error402("This asset is already sold");

            $offer = $this->offerService->getOffersByAssetId($data['assetId']);
            if($offer && $offer->approved != 0) return Utilities::error402("This asset is already on offer");

            if($clientPackage->purchase_complete == 0) return Utilities::error402("This asset purchase is nor complete yet");

            if($clientPackage->origin == ClientPackageOrigin::INVESTMENT->value) {
                $investment = $clientPackage->purchase;
                if(!$investment) return Utilities::error402("We can't find the investment that produced this asset");
                if($investment->ended == 0) return Utilities::error402("This is an ongoing investment asset");
            }

            if(!$clientPackage?->package) return Utilities::error402("There was a problem getting the package for this asset");
            if($clientPackage->package->type == PackageType::INVESTMENT->value) return Utilities::error402("This is an investment package and not sellable");

            $data['packageId'] = $clientPackage->package_id;
            $data['clientId'] = $clientPackage->client_id;
            $data['units'] = $clientPackage->units;
            $data['projectId'] = $clientPackage->package->project_id;
            $data['packagePrice'] = $clientPackage->package->amount;
            if(isset($data['resellOrderId'])) {
                $resellOrder = $this->resellOrderService->resellOrder($data['resellOrderId']);
                if(!$resellOrder) return Utilities::error402("Resell Order not found");
                $data['price'] = ($resellOrder->percentage/100) * ($clientPackage->package->amount * $data['units']);
                $data['active'] = 0;
            }

            $offer = $this->offerService->save($data);

            $this->notificationService->save($offer, NotificationType::NEW_OFFER_APPROVAL_REQ->value);

            return Utilities::ok(new OfferResource($offer));

        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to perform this operation, Please try again later or contact support');
        }
    }

    public function update(UpdateOffer $request)
    {
        try{
            $data = $request->validated();
            $offer = $this->offerService->offer($data['offerId']);
            if(!$offer) return Utilities::error402("Offer not found");

            if($offer->completed == 1) return Utilities::error402("Offer has been completed");

            $offer = $this->offerService->update($data, $offer);

            return Utilities::ok(new OfferResource($offer));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function offers(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');
        
        $this->offerService->filter = $filter;
        $this->offerService->clientId = Auth::guard("client")->user()->id;

        $this->offerService->sales = false;

        $offers = $this->offerService->offers([], $offset, $perPage);

        $this->offerService->count = true;
        $offersCount = $this->offerService->offers();

        return Utilities::paginatedOkay(OfferResource::collection($offers), $page, $perPage, $offersCount);
    }

    public function saleOffers(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');
        
        $this->offerService->filter = $filter;
        $this->offerService->clientId = Auth::guard("client")->user()->id;

        $this->offerService->sales = true;

        $offers = $this->offerService->offers(['bids'], $offset, $perPage);

        $this->offerService->count = true;
        $offersCount = $this->offerService->offers();

        return Utilities::paginatedOkay(OfferResource::collection($offers), $page, $perPage, $offersCount);
    }

    public function activeOffers(Request $request)
    {
        //Return only active offers that does not belong to the logged in client
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');
        $filter["status"] = OfferApprovalStatus::APPROVED->value;
        
        $this->offerService->filter = $filter;

        $this->offerService->sales = false;
        $this->offerService->clientId = Auth::guard("client")->user()->id;
        $this->offerService->mine = false;

        $offers = $this->offerService->offers(['bids'], $offset, $perPage);

        $this->offerService->count = true;
        $offersCount = $this->offerService->offers();

        return Utilities::paginatedOkay(OfferResource::collection($offers), $page, $perPage, $offersCount);
    }

    public function readyOffers(Request $request)
    {
        $this->offerService->clientId = Auth::guard("client")->user()->id;
        $this->offerService->mine = false;
        $offers = $this->offerService->readyOffers();

        return Utilities::ok(OfferResource::collection($offers));
    }

    public function myReadyOffers(Request $request)
    {
        $this->offerService->clientId = Auth::guard("client")->user()->id;
        $offers = $this->offerService->readyOffers();

        return Utilities::ok(OfferResource::collection($offers));
    }

    public function offer($offerId, Request $request)
    {
        if ($offerId && (!is_numeric($offerId) || !ctype_digit($offerId))) return Utilities::error402("Invalid parameter offerID");
        $offer = $this->offerService->offer($offerId, ['bids']);
        if(!$offer) return Utilities::error402("Offer not found");

        if($offer->client_id != Auth::guard("client")->user()->id) return Utilities::error402("You are not authorized to see an offer you did not create");

        return Utilities::ok(new OfferResource($offer));
    }
}
