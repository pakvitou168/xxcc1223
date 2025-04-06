<?php

namespace App\Exports\Travel;

use App\Enums\TravelInsuredPersonType;
use App\Enums\TravelPackage;
use App\Models\RefEnum\RefEnum;
use App\Models\Travel\DataDetailV;
use App\Models\Travel\DataMaster;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InsuredPersonExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    public function __construct(private DataMaster $master) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return DataDetailV::whereDataId($this->master->id)->orderBy('no');
    }
    public function map($row): array
    {
        return [
            $row->no,
            $row->full_name,
            $row->is_child ? TravelInsuredPersonType::ACCOMPANYING_CHILDREN->value : TravelInsuredPersonType::INSURED_PERSON->value,
            $row->gender,
            $row->date_of_birth,
            $row->passport_no,
            $row->inception_date,
            $row->expiry_date,
            RefEnum::whereEnumId($this->master->package_code)->whereTypeId(TravelPackage::TV_GROUP->value)->value('name'),
            $row->plan,
            $row->premium,
            $row->certificate_no,
        ];
    }

    public function headings(): array
    {
        $tv = DataMaster::find($this->master->id);

        $titleRows = collect([
            'No.',
            'Full Name',
            'Type of Insured Person',
            'Gender',
            'Date of birth',
            'Passport No.',
            'Inception Date',
            'Expiry Date',
            'Group Plan',
            'Plan',
            'Premium(USD)',
            'Policy No.'
        ]);

        return [
            [
                'LIST OF INSURED PERSON',
            ],
            [
                'THE INSURED NAME: ' . @$tv->insured_name,
            ],
            [
                'SUB CLASS: ' . Str::upper(@$tv->product->name)
            ],

            [''],

            $titleRows->toArray()
        ];
    }
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $startRow = 5;
        $endRow = $startRow + $this->query()->count();
       // Global
       $sheet->getStyle('A:O')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
       $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

       // Header
       $sheet->getStyle('A1')->getFont()->setSize(12);
       $sheet->getStyle('A2:A4')->getFont()->setSize(10);
       $sheet->getStyle('A1:A4')->getFont()->setBold(true);
       $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);


       // Table column title
       $sheet->getStyle('A' . $startRow . ':L' . $startRow)->getFont()->setSize(10)->setBold(true);
       $sheet->getStyle('A' . $startRow . ':L' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
       $sheet->getStyle('A' . $startRow . ':L' . $startRow)->getFill()->applyFromArray([
           'fillType' => 'solid',
           'rotation' => 0,
           'color' => ['rgb' => 'D9D9D9'],
       ]);

       $sheet->getStyle('A' . ($startRow + 1) . ':L' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
       $sheet->getStyle('K' . ($startRow + 1) . ':K' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
       $sheet->getStyle('A' . $startRow . ':L' . $endRow)->applyFromArray([
           'borders' => [
               'allBorders' => [
                   'borderStyle' => Border::BORDER_THIN,
                   'color' => ['argb' => '000'],
               ],
           ],
       ]);
       for($i=1; $i < $startRow; $i++){
        $sheet->mergeCells('A'.$i.':L'.$i);
       }
    
       /**
        * Append Row for Total Premium
        */
        $sheet->mergeCells('A'.($endRow + 1).':J'.($endRow + 1));
       $sheet->setCellValue('A' . ($endRow + 1), 'Total');
       $sheet->setCellValue('K' . ($endRow + 1), $this->master->total_premium);
       $sheet->getStyle('A' . ($endRow + 1) . ':K' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
       $sheet->getStyle('A' . ($endRow + 1) . ':L' . ($endRow + 1))->applyFromArray([
           'borders' => [
               'allBorders' => [
                   'borderStyle' => Border::BORDER_THIN,
                   'color' => ['argb' => '000'],
               ],
           ],
       ]);
    }
    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
}
