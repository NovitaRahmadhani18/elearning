<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Services\ClassroomService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RekapNilaiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStrictNullComparison
{
    protected Classroom $classroom;
    protected array $rekapNilai;
    protected array $contentTitles;

    public function __construct(Classroom $classroom)
    {
        $this->classroom = $classroom;
        $service = new ClassroomService();
        $this->rekapNilai = $service->getRekapNilaiClassroom($this->classroom);

        $this->contentTitles = [];
        if (!empty($this->rekapNilai) && isset($this->rekapNilai[0]['scores'])) {
            $this->contentTitles = collect($this->rekapNilai[0]['scores'])->pluck('content_title')->toArray();
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->rekapNilai);
    }

    public function headings(): array
    {
        return array_merge(
            ['#', 'Student Name'],
            $this->contentTitles,
            ['Total Score', 'Average Score']
        );
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        $scores = collect($row['scores'])->pluck('score')->map(function ($score) {

            if ($score === null) {
                return 0;
            }

            return $score ?? 0;
        })->toArray();

        return [
            $i,
            $row['student']->name,
            ...$scores,
            $row['total_score'] ?? 0,
            $row['average_score'] ?? 0,
        ];
    }
}
