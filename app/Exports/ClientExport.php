<?php

namespace app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Carbon\Carbon;

class ClientExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $clients;
    protected $headingConfig;

    public function __construct($clients, $headingConfig = null)
    {
        $this->clients = $clients;
        $this->headingConfig = $headingConfig ?? [
            'headings' => [
                'Title',
                'Firstname',
                'Surname',
                'Other Names',
                'Email',
                'Phone Number',
                'Gender',
                'Date of Birth',
            ],
            'columns' => [
                'title',
                'firstname',
                'lastname',
                'othernames',
                'email',
                'phone_number',
                'gender',
                'dob'
            ]
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->clients;
    }

    public function headings(): array
    {
        return $this->headingConfig['headings'];
    }

    public function map($client): array
    {
        $mappedData = [];
        foreach ($this->headingConfig['columns'] as $column) {
            switch ($column) {
                case 'title':
                    $mappedData[] = $client->title;
                    break;
                case 'firstname':
                    $mappedData[] = $client->firstname;
                    break;
                case 'lastname':
                    $mappedData[] = $client->lastname;
                    break;
                case 'othernames':
                    $mappedData[] = $client->othernames;
                    break;
                case 'email':
                    $mappedData[] = $client->email;
                    break;
                case 'phone_number':
                    $mappedData[] = $client->phone_number;
                    break;
                case 'gender':
                    $mappedData[] = $client->gender;
                    break;
                case 'dob':
                    $mappedData[] = ($client->dob) ? Carbon::parse($client->dob)->format('jS F Y') : '';
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
