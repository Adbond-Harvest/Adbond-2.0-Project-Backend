<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\client\AddNextOfKin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Resources\ClientBriefResource;
use App\Http\Resources\ClientNextOfKinResource;

use App\Http\Requests\Client\UpdateClient;

use App\Services\ClientService;

use App\Utilities;

class ClientController extends Controller
{
    private $clientService;

    public function __construct()
    {
        $this->clientService = new ClientService;
    }

    public function update(UpdateClient $request)
    {
        try{
            $data = $request->validated();
            if(count($data) == 0) return Utilities::error402(' enter at least one valid field');
            $client = $this->clientService->update($data, Auth::guard('client')->user());
            return Utilities::okay("Profile Updated Successfully", new ClientBriefResource($client));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function addNextOfKin(AddNextOfKin $request)
    {
        try{
            $data = $request->validated();
            $data['clientId'] = Auth::guard('client')->user()->id;
            $kin = $this->clientService->updateNextOfKin($data);
            return Utilities::okay("next of Kin added Successfully", new ClientNextOfKinResource($kin));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }
}
