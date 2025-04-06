<?php

namespace App\Exports\PA;

use App\Models\PA\DataMaster;
use App\Models\PA\InsuredPersonReportV;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InsuredPersonExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private $index = 0;
    public function __construct(private DataMaster $master)
    {

    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return InsuredPersonReportV::whereDataId($this->master->id);
    }
    public function map($row): array
    {
        return [
            ++$this->index,
            $row->insured_person,
            $row->relationship,
            $row->occupation,
            $row->gender,
            $row->date_of_birth->format('d-M-Y'),
            $row->inception_date,
            $row->expiry_date,
            $row->endorsement_effective_date,
            $row->sum_insured,
            $row->medical_expense_amount,
            $row->working_class_code,
            $row->premium,
            $row->transaction_type,
            $row->endorsement_stage
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'Insured person',
            'Relationship(staff/dependent)',
            'Occupation',
            'Sex',
            'Date of birth',
            'Inception Date',
            'Expiry Date',
            'Endorsement Effective Date',
            'Sum insured',
            'Medical Expense (USD)',
            'Class',
            'Premium (USD)',
            'Transaction Type (Policy/ Addition/ Deletion/ Others)',
            'Policy/ Endorsement No.'
        ];
    }
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $totalRow = $this->query()->count() + 1;
        $sheet->getStyle('A1:K' . $totalRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
        $sheet->getStyle('A1:K1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2']
            ],
            'font' => [
                'size' => 12,
                'bold' => true
            ]
        ]);
    }
    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }
}
