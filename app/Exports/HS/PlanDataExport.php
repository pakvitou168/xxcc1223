<?php

namespace App\Exports\HS;

use App\Models\HS\PlanDataDetailView;
use App\Models\HS\Policy;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
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

class PlanDataExport implements FromCollection, WithTitle, WithMapping, WithHeadings, ShouldAutoSize, WithEvents, WithStyles
{
    use Exportable, RegistersEventListeners;
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    public function collection()
    {
        return PlanDataDetailView::whereMasterDataId($this->policy->data_id)->where('lang_code','EN')->orderBy(DB::raw("CASE WHEN schema_type = 'STANDARD' THEN 1 WHEN schema_type = 'OPTIONAL' THEN 2 ELSE 3 END"))->orderBy('schema_detail_code')->get();
    }

    public function title(): string
    {
        return 'Plan Data Detail';
    }

    public function map($row): array
    {
        return [
            $row->schema_type,
            $row->schema_detail_code,
            $row->name,
            $row->name_kh,
            $row->sub,
            $row->amount,
            $row->internal_text,
            $row->display,
            $row->dsiplay_kh,
            $row->clause_code,
            $row->rate,
            $row->discount,
            $row->plan_1,
            $row->plan_2,
            $row->plan_3,
            $row->plan_4,
            $row->plan_5
        ];
    }

    public function headings(): array
    {
        return [
            'plan_type',
            'schema_detail_code',
            'name',
            'name_kh',
            'sub',
            'amount',
            'internal_text',
            'display',
            'dsiplay_kh',
            'clause_code',
            'rate',
            'discount',
            'plan_1',
            'plan_2',
            'plan_3',
            'plan_4',
            'plan_5'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->collection()->count() + 1;
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
}
