<?php

namespace App\Exports\Travel\Policy;

use App\Models\Travel\Policy\Policy;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class NameListExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles, WithEvents, WithColumnWidths, WithColumnFormatting
{
    use Exportable, RegistersEventListeners;
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    public function query()
    {
        return $this->policy->insuredPersonsV()->where(function ($q) {
            $q->whereNull('endorsement_stage')->orWhere('endorsement_state', '<>', 'DELETION');
        })->whereLangCode('EN')->orderBy('id');
    }

    public function title(): string
    {
        return 'Policy Name List';
    }

    public function headings(): array
    {
        return [
            'insured_id',
            'name',
            'occupation',
            'gender',
            'date_of_birth',
            'age',
            'standard_plan',
            'optional_plan',
            'remark',
            'other_benefit',
            'schema_type',
            'transaction_type',
            'endorsement_remark',
            'endorsement_effective_date',
            'days_of_endorsement',
            'claim',
            'total_premium_per_person',
            'endorsement_premium'
        ];
    }

    public function map($row): array
    {
        $dob = Carbon::parse(date('Y-m-d', strtotime(str_replace('/', ' ', $row->date_of_birth))));
        $age = $dob->age;
        return [
            $row->id,
            $row->name,
            $row->occupation,
            in_array(strtolower($row->gender), ['m', 'male']) ? 'M' : 'F',
            Date::dateTimeToExcel($dob),
            $age,
            $row->standard_plan_code,
            $row->optional_plan_code,
            $row->remark,
            $row->other_benefit,
            null,
            null,
            null,
            null,
            null,
            $row->claim_request_count,
            $row->premium_amt_bf_refund,
            null
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->query()->count() + 1;

        if ($this->policy->dataMaster->endorsement_type === 'GENERAL') {
            $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
            $sheet->getStyle('A1:E' . $totalRows)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000']
                    ],
                ],
            ]);
            $sheet->removeColumnByIndex(6,13);
        } else {
            $sheet->getStyle('A1:R1')->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
            $sheet->getStyle('A1:R' . $totalRows)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000']
                    ],
                ],
            ]);
        }
    }

    public function registerEvents(): array
    {
        if($this->policy->dataMaster->endorsement_type === 'GENERAL'){
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->getDelegate()->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('9BC2E6');
                }
                
            ];
        }
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('9BC2E6');
                $event->sheet->getDelegate()->getStyle('L1:R1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFE5');
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 10,
            'G' => 20,
            'H' => 20,
            'I' => 50,
            'J' => 70,
            'K' => 25,
            'L' => 25,
            'M' => 30,
            'N' => 35,
            'O' => 30,
            'P' => 10,
            'Q' => 35,
            'R' => 30
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => 'dd-mmm-yyyy',
            'N' => 'dd-mmm-yyyy'
        ];
    }
}
