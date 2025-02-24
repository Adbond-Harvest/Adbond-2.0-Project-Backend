<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Resources\TransactionResource;

use app\Services\TransactionService;
use app\Utilities;

use app\Enums\ProjectType;

class TransactionController extends Controller
{
    private $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService;
    }

    public function transactions(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
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
        $this->transactionService->filters = $filter;

        $transactions = $this->transactionService->transactions(['client'], $offset, $perPage);
        $pending = $transactions->filter(fn($transaction) => $transaction->confirmed === null);
        $successful = $transactions->filter(fn($transaction) => $transaction->confirmed == 1);
        $failed = $transactions->filter(fn($transaction) => $transaction->confirmed == 0);

        $this->transactionService->count = true;
        $transactionsCount = $this->transactionService->transactions();

        return Utilities::paginatedOkay([
            "transactions" => TransactionResource::collection($transactions),
            "pending" => TransactionResource::collection($pending),
            "successful" => TransactionResource::collection($successful),
            "failed" => TransactionResource::collection($failed)
        ], $page, $perPage, $transactionsCount);
    }

    public function transaction($transactionId)
    {
        if (!is_numeric($transactionId) || !ctype_digit($transactionId)) return Utilities::error402("Invalid parameter transactionID");

        $transaction = $this->transactionService->transaction($transactionId);

        return Utilities::ok(new TransactionResource($transaction));
    }
}
