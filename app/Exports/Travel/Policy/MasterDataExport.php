<?php

namespace App\Exports\Travel\Policy;

use App\Models\Travel\Policy\DataMaster;
use App\Models\Travel\Policy\Policy;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterDataExport implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents,WithColumnFormatting
{
    use Exportable, RegistersEventListeners;
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    public function query()
    {
        return DataMaster::whereId($this->policy->data_id);
    }

    public function title(): string
    {
        return 'Master Data';
    }

    public function headings(): array
    {
        return [
            'data_type',
            'product_code',
            'branch_code',
            'customer_no',
            'remark',
            'joint_status',
            'insured_name',
            'insured_name_kh',
            'insured_name_zh',
            'calc_option',
            'insurance_period_type',
            'effective_date_from',
            'effective_date_to',
            'insurance_period_val',
            'surcharge',
            'discount',
            'short_period_rate'
        ];
    }

    public function map($row): array
    {
        $effective_date_from    = Carbon::parse(date('Y-m-d', strtotime(str_replace('/', ' ', $row->effective_date_from))));
        $effective_date_to      = Carbon::parse(date('Y-m-d', strtotime(str_replace('/', ' ', $row->effective_date_to))));
        return [
            $row->data_type,
            $row->product_code,
            $row->branch_code,
            $row->customer_no,
            trim(strip_tags($row->remark)),
            $row->joint_status,
            $row->insured_name,
            $row->insured_name_kh,
            $row->insured_name_zh,
            $row->calc_option,
            $row->insurance_period_type,
            Date::dateTimeToExcel($effective_date_from),
            Date::dateTimeToExcel($effective_date_to),
            $row->insurance_period_val,
            $row->surcharge,
            $row->discount,
            null
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->query()->count() + 1;
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle('A1:Q' . $totalRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->getStyle('A1:Q1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('9BC2E6');
    }

    public function columnFormats(): array
    {
        return [
            'L' => 'dd-mmm-yyyy',
            'M' => 'dd-mmm-yyyy'
        ];
    }
}
