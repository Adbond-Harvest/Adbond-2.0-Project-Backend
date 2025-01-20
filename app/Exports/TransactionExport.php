<?php

namespace app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use app\Models\Transaction;
use app\Models\Order;

use app\Enums\PackagePaymentOption;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $transactions;
    protected $headingConfig;

    public function __construct($transactions, $headingConfig = null)
    {
        $this->transactions = $transactions;
        $this->headingConfig = $headingConfig ?? [
            'headings' => [
                'RefId',
                'Amount',
                'Status',
                'Date & Time',
                'Project',
                'Project Type',
                'Package',
                'Method',
                'Payment Plan'
            ],
            'columns' => [
                'receipt_no',
                'amount',
                'status',
                'created_at',
                'project',
                'project type',
                'package',
                'method',
                'payment plan'
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return $this->headingConfig['headings'];
    }

    public function map($transaction): array
    {
        $mappedData = [];
        foreach ($this->headingConfig['columns'] as $column) {
            switch ($column) {
                case 'amount':
                    $mappedData[] = 'N'.number_format($transaction->amount);
                    break;
                case 'status':
                    $mappedData[] = ($transaction->confirmed == 1) ? "Successful" : (($transaction->success === 0) ? "Failed" : "Pending");
                    break;
                case 'project':
                    $mappedData[] = $transaction->purchase?->package?->project?->name;
                    break;
                case 'project type':
                    $mappedData[] = $transaction->purchase?->package?->project?->projectType?->name;
                    break;
                case 'package':
                    $mappedData[] = $transaction->purchase?->package?->name;
                    break;
                case 'method':
                    $mappedData[] = $transaction->paymentMode?->name;
                    break;
                case 'receipt_no':
                    $mappedData[] = $transaction->receipt_no;
                    break;
                case 'payment plan':
                    $mappedData[] = ($transaction->purchase && $transaction->purchase_type==Order::$type && $transaction->purchase?->is_installment==1) ?  PackagePaymentOption::INSTALLMENT->value : PackagePaymentOption::ONE_OFF->value;
                    break;
                case 'created_at':
                    $mappedData[] = $transaction->created_at->format('F j, Y');
                    break;
                default:
                    $mappedData[] = $package->{$column} ?? '';
            }
        }
        return $mappedData;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E4E4E4']
                ]
            ]
        ];
    }
}
