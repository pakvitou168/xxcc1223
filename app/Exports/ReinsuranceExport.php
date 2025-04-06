<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Insurance\ReinsuranceData;
use App\Models\Insurance\PolicyView;
use App\Models\Product\Product;
use App\Models\ReinsuranceConfig\ReinsurancePartner;
use Illuminate\Support\Str;

class ReinsuranceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    private $policyId;
    private $productCode;
    private $reinsuranceDataReport;

    public function __construct($policyId, $productCode)
    {
        $this->policyId = $policyId;
        $this->productCode = $productCode;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->reinsuranceDataReport = $this->getReinsuranceData($this->policyId);
        return collect($this->reinsuranceDataReport);
    }

    private function getReinsuranceData($policyId){
        $reinsuranceExport = array();
        $reinsuranceList = ReinsuranceData::where('policy_id', $policyId)
                            ->where('lvl', 1)
                            ->where('status', 'ACT')
                            ->orderBy('id')
                            ->get()
                            ->map(function($item) use ($policyId) {

                                $subReinsuranceData = ReinsuranceData::where('policy_id', $policyId)
                                    ->where('detail_id', $item->detail_id)
                                    ->where('parent_code', $item->treaty_code)
                                    ->where('lvl', 2)
                                    ->where('status', 'ACT')
                                    ->orderBy('id')
                                    ->get()
                                    ->map(function($item) {
                                        $item->participant = $this->getTreatyName($item->treaty_code);

                                        return $item;
                                    });

                                $item->sub_reinsurance_data = $subReinsuranceData;

                                $item->participant = $this->getTreatyName($item->treaty_code);

                                return $item;
                            })
                            ->groupBy('detail_id')
                            ->toArray();
        foreach(array_values($reinsuranceList) as $key => $reinsuranceVehicle){
            foreach($reinsuranceVehicle as $index => $reinsurance){
                if($index == 0){
                    $reinsuranceVehicle[$index] = array_merge($reinsurance,['no'=> 'Vehicle #'.($key+1)]);
                }
                $reinsuranceExport = array_merge($reinsuranceExport, array($reinsuranceVehicle[$index]));
                if(count($reinsuranceVehicle[$index]['sub_reinsurance_data']) > 0){
                    foreach($reinsuranceVehicle[$index]['sub_reinsurance_data'] as $subInsuranceData){
                        $reinsuranceExport = array_merge($reinsuranceExport, array($subInsuranceData));
                    }
                }
            }
        }
        return $reinsuranceExport;
    }

    private function getTreatyName($treatyCode) {
        return ReinsurancePartner::getPartnerNameByCode($treatyCode);
    }

    public function headings(): array
    {
        $policy = PolicyView::where('status', '<>', 'DEL')->where('id',$this->policyId)->first();
        $productName = Product::where('status', 'ACT')->where('code', $this->productCode)->pluck('name');
        $titleRows = collect([
            'No.',
            'Parent',
            'Child',
            'Share (%)',
            'Sum Insured (USD)',
            'Premium (USD)',
            'RI Commission (%)',
            'RI Commission (USD)',
            'Tax & Fees (%)',
            'Tax & Fees (USD)',
            'Net Premium (USD)',
        ]);

        return [
            [
                'LIST OF POLICY REINSURANCE',
            ],
            [
                'INSURED NAME: '. $policy->name_en,
            ],
            [
                'POLICY NO: '. $policy->document_no,
            ],
            [
                'SUB CLASS: ' . Str::upper($productName[0]),
            ],

            [''],

            $titleRows->toArray()
        ];
    }

    public function map($reinsurance): array
    {
        $rows = collect([
            isset($reinsurance['no']) ? $reinsurance['no'] : '',
            isset($reinsurance['sub_reinsurance_data']) ? $reinsurance['participant'] ?? $reinsurance['treaty_code'] : '',
            isset($reinsurance['sub_reinsurance_data']) ? '' : $reinsurance['participant'],
            $reinsurance['share'],
            $reinsurance['sum_insured'] ?? '0',
            $reinsurance['premium'] ?? '0',
            $reinsurance['ri_commission'] ?? '0',
            $reinsurance['ri_commission_amt'] ?? '0',
            $reinsurance['tax_fee'] ?? '0',
            $reinsurance['tax_fee_amt'] ?? '0',
            $reinsurance['net_premium'] ?? '0',
        ]);

        return $rows->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
        ];
    }

    public function styles(Worksheet $sheet) {
        $rowCount = count($this->reinsuranceDataReport);
        // Cells borders
        $startRow = 6;
        $endRow = $startRow + $rowCount;

        // Global
        $sheet->getStyle('A:K')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A4')->getFont()->setSize(10);
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $this->conditionalStyles($sheet, $startRow, $endRow);

        // Table column title
        $sheet->getStyle('A'.$startRow.':K'.$startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':K'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$startRow.':K'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'D9D9D9'],
        ]);
    }

    private function conditionalStyles($sheet, $startRow, $endRow) {
        $sheet->getStyle('B:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$startRow.':K'.$endRow)->getFont()->setSize(10);
        $sheet->getStyle('A'.$startRow.':K'.$endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER_00,
            'K' => NumberFormat::FORMAT_NUMBER_00
        ];
    }
}
