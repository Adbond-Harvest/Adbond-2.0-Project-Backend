<?php

namespace app\Http\Controllers\User;

use app\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use app\Http\Resources\ProjectTypeResource;

use app\Http\Requests\User\UpdateProjectType;

use app\Services\ProjectTypeService;
use app\Services\FileService;

use app\Enums\FilePurpose;
use app\Utilities;

class ProjectTypeController extends Controller
{
    private $projectTypeService;
    private $fileService;

    public function __construct()
    {
        $this->projectTypeService = new ProjectTypeService;
        $this->fileService = new FileService;
    }

    public function update(UpdateProjectType $request)
    {
        try{
            $data = $request->validated();
            DB::beginTransaction();
            $projectType = $this->projectTypeService->projectType($data['id']);
            if(!isset($projectType)) return Utilities::error402("Project Type not found");
            if(isset($data['photoId'])) {
                $file = $this->fileService->getFile($data['photoId']);
                if(!$file) return Utilities::error402("File not found");
                // dd($file->purpose." == ".FilePurpose::PROJECT_TYPE_PHOTO);
                if($file->purpose != FilePurpose::PROJECT_TYPE_PHOTO->value) return Utilities::error402("Sorry, you are attempting to save the wrong Photo");
                $fileMeta = ["belongsId"=>$data["id"], "belongsType"=>"app\Models\ProjectType"];
            }
            $projectType = $this->projectTypeService->update($data, $projectType);
            if(isset($data['photoId'])) $this->fileService->updateFileObj($fileMeta, $file);

            DB::commit();
            return Utilities::okay("Project Type Updated Successfully", new ProjectTypeResource($projectType));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occurred while trying to process the request, Please try again later or contact support');
        }
    }

    public function projectTypes()
    {
        $projectTypes = $this->projectTypeService->projectTypes();
        return Utilities::ok(ProjectTypeResource::collection($projectTypes));
    }

    public function projectType($id)
    {
        if (!is_numeric($id) || !ctype_digit($id)) return Utilities::error402("Invalid parameter ID");
        $projectType = $this->projectTypeService->projectType($id);
        if(!$projectType) return Utilities::error402("ProjectType not found");
        return Utilities::ok(new ProjectTypeResource($projectType));
    }
}
