<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Models\Project;

class ProjectExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $projects;
    protected $headingConfig;

    public function __construct($projects, $headingConfig = null)
    {
        $this->projects = $projects;
        $this->headingConfig = $headingConfig ?? [
            'headings' => [
                'Identifier',
                'Name',
                'Type',
                'Description',
                'Status',
                'Package Count',
                'Created Date'
            ],
            'columns' => [
                'identifier',
                'name',
                'project_type',
                'description',
                'status',
                'package_count',
                'created_at'
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->projects;
    }

    public function headings(): array
    {
        return $this->headingConfig['headings'];
    }

    public function map($project): array
    {
        $mappedData = [];
        foreach ($this->headingConfig['columns'] as $column) {
            switch ($column) {
                case 'project_type':
                    $mappedData[] = $project->projectType->name ?? '';
                    break;
                case 'status':
                    $mappedData[] = $project->active ? 'Active' : 'Inactive';
                    break;
                case 'package_count':
                    $mappedData[] = $project->packages->count();
                    break;
                case 'created_at':
                    $mappedData[] = $project->created_at->format('F j, Y');
                    break;
                default:
                    $mappedData[] = $project->{$column} ?? '';
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
