<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;

use app\Http\Requests\VirtualTeamApplication;

use app\Http\Resources\VirtualTeamApplicationResource;

use app\Services\VirtualTeamApplicationService;

use app\Utilities;

class VirtualTeamApplicationController extends Controller
{
    private $applicationService;

    public function __construct()
    {
        $this->applicationService = new VirtualTeamApplicationService;
    }

    public function apply(VirtualTeamApplication $request)
    {
        try{
            $data = $request->validated();
            $application = $this->applicationService->save($data);

            return Utilities::ok(new VirtualTeamApplicationResource($application));
        }catch (\Exception $e) {
            return Utilities::error($e, 'An error occurred while attempting to carry out this operation');
        }
    }

    public function applications(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $applications = $this->applicationService->applications($offset, $perPage);

        $this->applicationService->count = true;
        $total = $this->applicationService->applications();

        return Utilities::paginatedOkay(VirtualTeamApplicationResource::collection($applications), $page, $perPage, $total);
    }

    public function application($applicationId)
    {
        if ($applicationId && (!is_numeric($applicationId) || !ctype_digit($applicationId))) return Utilities::error402("Invalid parameter applicationID");

        $application = $this->applicationService->application($applicationId);

        return Utilities::ok(new VirtualTeamApplicationResource($application));
    }
}
