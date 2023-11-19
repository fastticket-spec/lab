<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAttendees implements FromCollection, WithHeadings, WithStyles, WithChunkReading
{
    private ?string $event_id;
    private ?Collection $event_ids;

    public function __construct(?string $event_id = null, ?Collection $event_ids = null, private $attendees, private ?array $questions)
    {
        $this->event_id = $event_id;
        $this->event_ids = $event_ids;
    }

    public function headings(): array
    {
        return [
            "EVENT TITLE",
            "FIRST NAME",
            "LAST NAME",
            "EMAIL",
            "REFERENCE",
            "DOWNLOADS",
            "DATE CREATED",
            "PRINTED",
            "COLLECTED",
            "ZONES",
            ...$this->questions
        ];

    }


    public function collection(): \Illuminate\Support\Collection
    {
        return collect($this->attendees);
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
