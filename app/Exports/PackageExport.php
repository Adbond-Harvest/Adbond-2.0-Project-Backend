<?php

namespace appExports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use appModels\Package;

class PackageExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $packages;
    protected $headingConfig;

    public function __construct($packages, $headingConfig = null)
    {
        $this->packages = $packages;
        $this->headingConfig = $headingConfig ?? [
            'headings' => [
                'Name',
                'Description',
                'location',
                'units',
                'Size',
                'Amount',
                'Status',
                'Created Date'
            ],
            'columns' => [
                'name',
                'description',
                'location',
                'units',
                'size',
                'amount',
                'status',
                'created_at'
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->packages;
    }

    public function headings(): array
    {
        return $this->headingConfig['headings'];
    }

    public function map($package): array
    {
        $mappedData = [];
        foreach ($this->headingConfig['columns'] as $column) {
            switch ($column) {
                case 'location':
                    $mappedData[] = $package->address." ".$package->state?->name ?? '';
                    break;
                case 'size':
                    $mappedData[] = $package->size.'Sqm';
                    break;
                case 'amount':
                    $mappedData[] = 'N'.number_format($package->active);
                    break;
                case 'status':
                    $mappedData[] = $package->active ? 'Active' : 'Inactive';
                    break;
                case 'created_at':
                    $mappedData[] = $package->created_at->format('F j, Y');
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
