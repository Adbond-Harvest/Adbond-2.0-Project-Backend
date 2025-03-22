<?php

namespace app\Http\Controllers\User;

use app\Http\Controllers\Controller;
use Illuminate\Http\Request;

use app\Http\Resources\ProjectResource;
use app\Http\Resources\ProjectTypeResource;
use app\Http\Resources\TransactionResource;

use app\Services\ProjectTypeService;
use app\Services\ProjectService;
use app\Services\ClientService;
use app\Services\ClientPackageService;
use app\Services\PurchaseService;
use app\Services\TransactionService;

use app\Models\ClientPurchasesSummaryView;

use app\Enums\ProjectFilter;
use app\Enums\PurchaseSummaryDuration;

use app\Utilities;
use app\EnumClass;

class IndexController extends Controller
{
    private $projectTypeService;
    private $projectService;
    private $clientService;
    private $clientPackageService;
    private $purchaseService;
    private $transactionService;

    public function __construct()
    {
        $this->projectTypeService = new ProjectTypeService;
        $this->projectService = new ProjectService;
        $this->clientService = new ClientService;
        $this->clientPackageService = new ClientPackageService;
        $this->purchaseService = new PurchaseService;
        $this->transactionService = new TransactionService;
    }

    public function dashboard(Request $request)
    {
        $projectTypes = $this->projectTypeService->projectTypes();
        $projectTypeObj = null;

        $this->purchaseService->summaryDuration = PurchaseSummaryDuration::WEEK->value;

        $purchaseChart = $this->purchaseService->clientPurchaseSummary();
        $purchaseTotal = 0;
        if($purchaseChart->count() > 0) {
            foreach($purchaseChart as $purchase) {
                $purchaseTotal += $purchase->total_amount;
            }
        }

        $projectTypesCounts = [];
        if (count($projectTypes) > 0) {
            foreach ($projectTypes as $projectType) {
                if(!$projectTypeObj) $projectTypeObj = $projectType;
                $projectTypesCounts[$projectType->name] = [
                    "totalProjects" => $projectType->projects->count(),
                    "activeProjects" => $projectType->activeProjects->count(),
                    "inactiveProjects" => $projectType->inactiveProjects->count()
                ];
            }
        }
        unset($projectTypeObj['projects']);

        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = null;
        $offset = $perPage * ($page-1);
        
        $this->projectService->typeId = $projectTypeObj->id;
        $projects = $this->projectService->projects([], $offset, $perPage);

        $this->projectService->typeId = null;

        $this->projectService->count = true;
        $projectsCount = $this->projectService->projects();
        
        $this->projectService->status = ProjectFilter::ACTIVE->value;
        $projectsActiveCount = $this->projectService->projects();

        $this->clientService->active = true;
        $this->clientService->count = true;
        $activeClientsCount = $this->clientService->clients();

        $this->clientPackageService->count = true;
        $assetsCount = $this->clientPackageService->assets();

        $this->clientPackageService->active = true;
        $activeAssetsCount = $this->clientPackageService->assets();

        $summary = [
            "projectsCount" => $projectsCount,
            "activeProjectsCount" => $projectsActiveCount,
            "activeClientsCount" => $activeClientsCount,
            "assetsCount" => $assetsCount,
            "activeAssetsCount" => $activeAssetsCount
        ];

        // transactions
        $perPage = 10;
        $offset = 0;

        $transactions = $this->transactionService->transactions(['client'], $offset, $perPage);

        return Utilities::ok([
            "summary" => $summary,
            "purchaseTotal" => $purchaseTotal,
            "purchaseChart" => $purchaseChart,
            "projectTypes" => $projectTypesCounts,
            "projects" => ProjectResource::collection($projects),
            "activeProjectType" => new ProjectTypeResource($projectTypeObj),
            "transactions" => TransactionResource::collection($transactions)
        ]);
    }

    public function purchaseSummary(Request $request)
    {
        $summaryDuration = ($request->query('summaryDuration')) ?? PurchaseSummaryDuration::WEEK->value;
        if(!in_array($summaryDuration, EnumClass::purchaseSummaryDurations())) return Utilities::error402("Invalid Summary Duration");

        $this->purchaseService->summaryDuration = $summaryDuration;
        if($summaryDuration == PurchaseSummaryDuration::CUSTOM->value) {
            $start = $request->query('start');
            $end = $request->query('end');

            if(!$start) return Utilities::error402("Custom Start Date is required");

            $this->purchaseService->start = $start;
            if($end) $this->purchaseService->end = $end;
        }

        $this->purchaseService->summaryDuration = $summaryDuration;

        $purchaseChart = $this->purchaseService->clientPurchaseSummary();
        $purchaseTotal = 0;
        if($purchaseChart->count() > 0) {
            foreach($purchaseChart as $purchase) {
                $purchaseTotal += $purchase->total_amount;
            }
        }

        return Utilities::ok(["purchaseTotal" => $purchaseTotal, "purchaseChart" => $purchaseChart]);
    }
}
