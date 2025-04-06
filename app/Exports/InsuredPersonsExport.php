<?php

namespace App\Exports;

use App\Models\Travel\DataMaster;
use App\Models\Travel\Policy\InsuredPersonView;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class InsuredPersonsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, WithColumnFormatting, WithStrictNullComparison, WithColumnWidths
{
    private $masterId;
    private $insuredPersonReportData;
    private $documentNo, $isGeneral, $forAll;

    public function __construct($masterId, $document_no, $all = false, $isGeneral = false)
    {
        $this->masterId = $masterId;
        $this->documentNo = $document_no;
        $this->isGeneral = $isGeneral;
        $this->forAll = $all;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $result = InsuredPersonView::whereDataId($this->masterId)
            ->when(!$this->forAll, function ($q) {
                $q->where('endorsement_stage', $this->documentNo);
            })
            ->orderBy('data_detail_id')
            ->get()
            ->map(function ($item, $key) {
                $item->no = $key + 1;
                return $item;
            });
        /*dd($this->masterId, !$this->forAll, $this->documentNo ,$result);*/
        $this->insuredPersonReportData = $result;

        return $this->insuredPersonReportData;
    }

    public function headings(): array
    {
        $travel = DataMaster::find($this->masterId);

        $titleRows = collect([
            'No.',
            'Insured Person',
            'Occupation',
            'Sex',
            'Date of Birth',
            'Inception Date',
            'Expiry Date',
            'Endorsement Effective Date',
            'IPD Plan',
            'OPD Plan',
            'IPD Premium (USD)',
            'OPD Premium (USD)',
            'Total Premium (USD)',
            'Transaction Type (Policy/Addition/Deletion/Others)',
            'Policy/Endorsement No.'
        ]);

        return [
            [
                'LIST OF INSURED PERSON',
            ],
            [
                'THE INSURED NAME: ' . @$travel->insured_name,
            ],
            [
                'SUB CLASS: ' . Str::upper(@$travel->product->name)
            ],

            [''],

            $titleRows->toArray()
        ];
    }

    public function map($insuredPerson): array
    {
        //dd($insuredPerson);
        $rows = collect([
            $insuredPerson->no,
            $insuredPerson->full_name,
            $insuredPerson->occupation,
            $insuredPerson->gender,
            $insuredPerson->date_of_birth,
            $insuredPerson->inception_date,
            $insuredPerson->expiry_date,
            in_array($insuredPerson->transaction_type,['ADDITION','AMENDMENT','DELETION']) ? $insuredPerson->endorsement_effective_date : null,
            $insuredPerson->standard_plan,
            $insuredPerson->optional_plan,
            $insuredPerson->standard_premium,
            $insuredPerson->premium,
            $insuredPerson->transaction_type,
            $insuredPerson->endorsement_stage
        ]);

        return $rows->toArray();
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->insuredPersonReportData);
        // Cells borders
        $startRow = 5;
        $endRow = $startRow + $rowCount;

        // Global
        $sheet->getStyle('A:O')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A4')->getFont()->setSize(10);
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);


        // Table column title
        $sheet->getStyle('A' . $startRow . ':O' . $startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A' . $startRow . ':O' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $startRow . ':O' . $startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'D9D9D9'],
        ]);

        $sheet->getStyle('E' . ($startRow + 1) . ':H' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I' . ($startRow + 1) . ':J' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('N' . ($startRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('K' . ($startRow + 1) . ':M' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $startRow . ':O' . $endRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $startRow . ':O' . $endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ]);
        /**
         * Append Row for Total Premium
         */
        $sheet->setCellValue('J' . ($endRow + 1), 'Total');
        $sheet->getStyle('J' . ($endRow + 1) . ':M' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('J' . ($endRow + 1) . ':M' . ($endRow + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ]);

        $sheet->setCellValue('K' . ($endRow + 1), $this->getTotalPremium('standard_premium'));
        $sheet->setCellValue('L' . ($endRow + 1), $this->getTotalPremium('optional_premium'));
        $sheet->setCellValue('M' . ($endRow + 1), $this->getTotalPremium('premium'));
        $sheet->getStyle('K' . ($endRow + 1) . ':M' . ($endRow + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('J' . ($endRow + 1) . ':M' . ($endRow + 1))->getFont()->setBold(true);

    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_TEXT,
        ];
    }

    private function getTotalPremium($key)
    {
        return $this->collection()->sum($key);
    }

}
