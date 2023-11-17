<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportCheckins implements FromCollection, WithHeadings, WithStyles, WithChunkReading
{
    public function __construct(private $checkins)
    {
    }

    public function headings(): array
    {
        return [
            "FIRST NAME",
            "LAST NAME",
            "CHECKIN TIME",
            "CHECKIN USER"
        ];

    }


    public function collection(): \Illuminate\Support\Collection
    {
        return collect($this->checkins);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true ]],
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
