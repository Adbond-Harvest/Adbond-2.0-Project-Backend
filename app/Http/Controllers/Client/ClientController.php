<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Resources\ClientBriefResource;
use App\Http\Resources\ClientNextOfKinResource;

use App\Http\Requests\client\AddNextOfKin;
use App\Http\Requests\Client\UpdateClient;

use App\Services\ClientService;
use App\Services\FileService;

use App\Enums\FilePurpose;
use App\Utilities;

class ClientController extends Controller
{
    private $clientService;
    private $fileService;

    public function __construct()
    {
        $this->clientService = new ClientService;
        $this->fileService = new FileService;
    }

    public function update(UpdateClient $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            if(count($data) == 0) return Utilities::error402(' enter at least one valid field');
            if(isset($data['photoId'])) {
                $file = $this->fileService->getFile($data['photoId']);
                if(!$file) return Utilities::error402("File not found");
                if($file->purpose != FilePurpose::CLIENT_PROFILE_PHOTO) return Utilities::error402("Sorry, you are attempting to save the wrong Photo");
                $fileMeta = ["belongsId"=>Auth::guard('client')->user()->id, "belongsType"=>"App\Models\CLient"];
            }

            $client = $this->clientService->update($data, Auth::guard('client')->user());
            if(isset($data['photoId'])) $this->fileService->updateFileObj($fileMeta, $file);

            DB::commit();
            return Utilities::okay("Profile Updated Successfully", new ClientBriefResource($client));
        }catch(\Exception $e){
            DB::rollBack();
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
