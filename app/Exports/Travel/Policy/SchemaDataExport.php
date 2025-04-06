<?php

namespace App\Exports\Travel\Policy;

use App\Models\Travel\Policy\Policy;
use App\Models\Travel\Policy\SchemaDataView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
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

class SchemaDataExport implements FromCollection,WithHeadings,WithMapping,WithTitle,WithStyles,WithEvents,WithColumnWidths
{
    use Exportable,RegistersEventListeners;
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }

    public function collection()
    {
        return SchemaDataView::whereStatus('ACT')->whereLangCode('EN')
        ->whereMasterDataId($this->policy->dataMaster->originMaster()->id)->whereIn('key',['PREMIUM','P_PREMIUM'])->whereMasterDataType('POLICY')->get();
        
        // $schemaData = SchemaDataView::whereStatus('ACT')->whereLangCode('EN')
        // ->whereMasterDataId($this->policy->dataMaster->previous_id)->get()
        // ->transform(function($item) use($originSchema){
        //     if($item->key === 'PREMIUM' && in_array($item->schema_type,['STANDARD','OPTIONAL'])){
        //         return $originSchema->where('key',$item->key)->where('schema_type',$item->schema_type)->first() ?? $item;
        //     }elseif($item->key === 'P_PREMIUM'){
        //         return $originSchema->where('key',$item->key)->where('schema_type',$item->schema_type)->first() ?? $item;
        //     }
        //     return $item;
        // })
        // ;

        // return $schemaData;
    }

    public function title(): string
    {
        return 'Schema Data';
    }

    public function headings(): array
    {
        return [
            'key',
            'age_band',
            'no_female',
            'no_person',
            'rate',
            'plan_1',
            'plan_2',
            'plan_3',
            'plan_4',
            'plan_5',
            'master_data_type',
            'schema_type',
            'schema_detail_code'
        ];
    }

    public function map($row): array
    {
        return [
            $row->key,
            $row->age_band,
            $row->no_female,
            $row->no_person,
            $row->rate,
            $row->plan_1,
            $row->plan_2,
            $row->plan_3,
            $row->plan_4,
            $row->plan_5,
            $row->master_data_type,
            $row->schema_type,
            $row->schema_detail_code
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->collection()->count() + 1;
        $sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle('A1:M' . $totalRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
        // $sheet->getParent()->getActiveSheet()->getProtection()->setSheet(true);
        // $sheet->getStyle('A2:M'.($totalRows+100))->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->getStyle('A1:M1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('9BC2E6');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 23,
            'B' => 12,
            'C' => 15,
            'D' => 15,
            'E' => 10,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 23,
            'L' => 17,
            'M' => 25
        ];
    }
}
