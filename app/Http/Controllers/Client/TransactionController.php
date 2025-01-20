<?php

namespace app\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Http\Controllers\Controller;

use app\Http\Resources\TransactionResource;

use app\Services\TransactionService;

use app\Enums\ProjectType;

use App\Utilities;

class TransactionController extends Controller
{
    private $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService;
    }

    // public function index(Request $request)
    // {
    //     $page = ($request->query('page')) ?? 1;
    //     $perPage = ($request->query('perPage'));
    //     if(!is_int((int) $page) || $page <= 0) $page = 1;
    //     if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
    //     $offset = $perPage * ($page-1);

    //     $clientId = Auth::guard("client")->user()->id;

    //     $this->transactionService->clientId = $clientId;
    //     $transactions = $this->transactionService->transactions([], $offset, $perPage);

    //     $this->transactionService->count = true;
    //     $transactionsCount = $this->transactionService->transactions();

    //     return Utilities::paginatedOkay(TransactionResource::collection($transactions), $page, $perPage, $transactionsCount);
    // }

    public function index(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = config('pagination.PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('text')) $filter["text"] = $request->query('text');
        if($request->query('date')) $filter["date"] = $request->query('date');
        if($request->query('projectType')) {
            $validTypes = [ProjectType::LAND->value, ProjectType::AGRO->value, ProjectType::HOMES->value];
            $validTypesString = '';
            foreach($validTypes as $valid) $validTypesString .= $valid.', ';
            if(!in_array($request->query('projectType'), $validTypes)) return Utilities::error402("Valid Project Types are: ".$validTypesString);
            $filter["projectType"] = $request->query('projectType');
        }


        $transactions = $this->transactionService->filter($filter, [], $offset, $perPage);

        $this->transactionService->count = true;
        $transactionsCount = $this->transactionService->filter($filter);

        $meta = [
            "page" => $page,
            "perPage" => $perPage,
            "pages" => ceil($transactionsCount/$perPage),
            "total" => $transactionsCount
        ];

        return Utilities::ok([
            "projects" => TransactionResource::collection($transactions),
            "meta" => $meta
        ]);
    }

    public function transaction($transactionId)
    {
        if (!is_numeric($transactionId) || !ctype_digit($transactionId)) return Utilities::error402("Invalid parameter transactionID");

        $transaction = $this->transactionService->transaction($transactionId);

        if(!$transaction) return Utilities::error402("Transaction nor found");

        return Utilities::ok(new TransactionResource($transaction));
    }

    public function export(Request $request, $transactionId)
    {
        try {
            if (!is_numeric($transactionId) || !ctype_digit($transactionId)) return Utilities::error402("Invalid parameter transactionID");
            $transaction = $this->transactionService->transaction($transactionId);

            if(!$transaction) return Utilities::error402("Transaction nor found");

            $transactions = [$transaction];
            $transactions = collect($transactions);

            return $this->transactionService->exportToPDF($transactions, Auth::guard("client")->user()->full_name);

        } catch (\Exception $e) {
            return Utilities::error($e, 'An error occurred during export');
        }
    }
}
