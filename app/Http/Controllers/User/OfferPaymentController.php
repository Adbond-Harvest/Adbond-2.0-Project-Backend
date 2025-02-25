<?php

namespace app\Http\Controllers\User;

use Illuminate\Http\Request;
use app\Http\Controllers\Controller;

use app\Http\Resources\OfferPaymentResource;

use app\Services\PaymentService;

use app\Utilities;

class OfferPaymentController extends Controller
{
    private $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService;
    }

    public function payments(Request $request)
    {
        $page = ($request->query('page')) ?? 1;
        $perPage = ($request->query('perPage'));
        if(!is_int((int) $page) || $page <= 0) $page = 1;
        if(!is_int((int) $perPage) || $perPage==null) $perPage = env('PAGINATION_PER_PAGE');
        $offset = $perPage * ($page-1);

        $filter = [];
        if($request->query('status')) {
            $validStatuses = ['pending', 'successful', 'failed'];
            $validStatusesString = '';
            foreach($validStatuses as $valid) $validStatusesString .= $valid.', ';
            if(!in_array($request->query('status'), $validStatuses)) return Utilities::error402("Valid Statuses are: ".$validStatusesString);
            switch($request->query('status')) {
                case "pending" : $filter['status'] = null; break;
                case "successful" : $filter['status'] = 1; break;
                case "failed" : $filter['status'] = 0; break;
            }
        }
        $this->paymentService->filters = $filter;

        $payments = $this->paymentService->offerPayments(['purchase'], $offset, $perPage);
        $this->paymentService->count = true;

        $paymentsCount = $this->paymentService->offerPayments();

        $this->paymentService->filters = ['status'=>1];
        $successfulCount = $this->paymentService->offerPayments();

        $this->paymentService->filters = ['status'=>0];
        $failedCount = $this->paymentService->offerPayments();

        $this->paymentService->filters = ['status'=>null];
        $pendingCount = $this->paymentService->offerPayments();

        if(isset($filter['status'])) {
            // $defaultTotal = ($filter['status'] == null) ? $pendingCount : ($filter['status'] === 1) ? $successfulCount : $failedCount;
            switch($filter['status']) {
                case null : $defaultTotal = $pendingCount; break;
                case 1 : $defaultTotal = $successfulCount; break;
                case 0 : $defaultTotal = $failedCount; break;
            }
        }else{
            $defaultTotal = $paymentsCount;
        }
        // $successful = $payments->filter(fn($payment) => $payment->confirmed == 1 && $payment?->purchase?->completed == 0);
        // $pending = $payments->filter(fn($payment) => $payment->confirmed === null && $payment?->purchase?->completed == 0);
        // $failed = $payments->filter(fn($payment) => $payment->confirmed === 0);

        return Utilities::paginatedOkay([
            "payments" => OfferPaymentResource::collection($payments),
            "paymentsCount" => $paymentsCount,
            "successfulCount" => $successfulCount,
            "pendingCount" => $pendingCount,
            "failedCount" => $failedCount
        ], $page, $perPage, $defaultTotal);

    }
}
