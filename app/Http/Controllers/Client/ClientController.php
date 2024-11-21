<?php

namespace app\Http\Controllers\Client;

use app\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use app\Http\Resources\ClientBriefResource;
use app\Http\Resources\ClientResource;
use app\Http\Resources\ClientNextOfKinResource;

use app\Http\Requests\Client\AddNextOfKin;
use app\Http\Requests\Client\UpdateClient;

use app\Services\ClientService;
use app\Services\FileService;

use app\Models\Client;

use app\Enums\FilePurpose;
use app\Utilities;

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
            $oldPhotoId = null;
            if($request->hasFile('photo')) {
                $purpose = FilePurpose::CLIENT_PROFILE_PHOTO->value;
                if(Auth::guard('client')->user()->photo_id) $oldPhotoId = Auth::guard('client')->user()->photo_id;
                $res = $this->fileService->save($request->file('photo'), 'image', Auth::guard('client')->user()->id, $purpose, Client::$userType, 'client-profile-photos');
                if($res['status'] != 200) return Utilities::error402('Sorry Photo could not be uploaded '.$res['message']);

                $data['photoId'] = $res['file']->id;
                $fileMeta = ["belongsId"=>Auth::guard('client')->user()->id, "belongsType"=>"app\Models\CLient"];
                $this->fileService->updateFileObj($fileMeta, $res['file']);
            }

            $client = $this->clientService->update($data, Auth::guard('client')->user());

            // delete the old photo if it exists
            if($oldPhotoId) $this->fileService->deleteFile($oldPhotoId);

            DB::commit();
            return Utilities::okay("Profile Updated Successfully", new ClientBriefResource($client));
        }catch(\Exception $e){
            DB::rollBack();
            return Utilities::error($e, 'An error occured while trying to Perform this operation, Please try again later or contact support');
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
