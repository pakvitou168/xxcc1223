<?php

namespace App\Exports;

use App\Models\Deductible\DeductibleDetail;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Make\Make;
use App\Models\Make\MakeModel;
use App\Models\Product\Product;
use Illuminate\Support\Str;
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
class VehiclesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    private $masterId;
    private $document_no;
    private $vehicleReportData;

    public function __construct($masterId, $document_no)
    {
        $this->masterId = $masterId;
        $this->document_no = $document_no;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->vehicleReportData = AutoDetail::with('auto:id,effective_date_from,effective_date_to')->where('master_data_id', $this->masterId)->orderBy('id')->get()->map(function($item, $key) {

            $model = MakeModel::find($item->model_id);
            $make = Make::find($model ? $model->make_id : null);

            $deductibles = $this->getDeductibles($item->id, $item->product_code);
            $deductibleText = $deductibles->map(function($item) {
                return ($item->cover ? $item->cover->deductible_label : "") . ": " . $item->value;
            })->join("\r\n");

            $coveragesText = $item->selected_cover_pkg;

            $item->no = $key + 1;
            $item->make_model = $make->make . ' ' . $model->model;
            $item->ncd =$item->ncd;
            $item->coverages = $coveragesText;
            $item->deductible = $deductibleText;
            $item->passenger_tonnage = $item->passenger ?? $item->tonnage;

            return $item;
        });

        return $this->vehicleReportData;
    }

    private function getDeductibles($autoDetailId, $productCode) {
        return DeductibleDetail::with(['cover' => function($query) use ($productCode) {
            $query->select(
                'code',
                'name',
                'deductible_label',
                'seq',
            )
            ->where('product_code', $productCode)
            ->where('status', 'ACT');
        }])
            ->select('product_code', 'comp_code', 'value')
            ->where('detail_id', $autoDetailId)
            ->get()
            ->sortBy('cover.seq')
            ->values();
    }

    public function headings(): array
    {
        $auto = Auto::find($this->masterId);

        $titleRows = collect([
            'No.',
            'Make and Model',
            'Plate No.',
            'Chassis No.',
            'Engine No.',
            'Year of Manufacture',
            'Cubic Capacity',
            'Coverages',
            'Sum Insured (USD)',
            'No Claim Discount (%)',
            'Discount (%)',
            'Deductible (Each and every claim)',
            'Premium (USD)',
            'Remarks'
        ]);

        // If it is for policy vehicle exports, add Inception Date & Expiry Date columns
        if($auto->data_type === 'POLICY'){
            $titleRows->splice(8, 0, ['Inception Date']);
            $titleRows->splice(9, 0, ['Expiry Date']);
        }

        // If is commercial vehicle, add seats/tonnage column
        if ($this->hasPassengerOrTonnage($auto->product_code)) {
            $titleRows->splice(6, 0, ['Seats/Tonnage']);
        }
        return [
            [
                'LIST OF FLEET VEHICLES',
            ],
            [
                'INSURED NAME: ' . $auto->insured_name,
            ],
            [
                $auto->quotation ? 'QUOTATION NO: ' . $auto->quotation->document_no : 'POLICY NO: ' . $this->document_no,
            ],
            [
                'SUB CLASS: ' . Str::upper($auto->product->name)
            ],

            [''],

            $titleRows->toArray()
        ];
    }

    private function hasPassengerOrTonnage($productCode) {
        $specification = Product::getProductSpecificationByCode($productCode);

        return $specification === 'TONNAGE' || $specification === 'PASSENGER';
    }

    public function map($vehicle): array
    {
        $rows = collect([
            $vehicle->no,
            $vehicle->make_model,
            $vehicle->plate_no.' ', // need to put like that otherwise there will be cases that it results in the value of 0
            $vehicle->chassis_no,
            $vehicle->engine_no,
            $vehicle->manufacturing_year,
            $vehicle->cubic,
            $vehicle->coverages,
            $vehicle->vehicle_value,
            $vehicle->ncd ?? '0',
            $vehicle->discount ?? '0',
            $vehicle->deductible,
            $vehicle->premium < 0 ? '('. number_format(abs($vehicle->premium), 2,'.', ',') .')' : $vehicle->premium,
            $vehicle->remark
        ]);

        // If it is for policy vehicle exports
        if($vehicle->master_data_type === 'POLICY'){
            $rows->splice(8, 0, [\Carbon\Carbon::parse($vehicle->auto->effective_date_from)->format('d/m/Y')]);
            $rows->splice(9, 0, [\Carbon\Carbon::parse($vehicle->auto->effective_date_to)->format('d/m/Y')]);
        }

        // If is commercial vehicle, add seats/tonnage column
        if ($this->hasPassengerOrTonnage($vehicle->product_code)) {
            $rows->splice(6, 0, [$vehicle->passenger_tonnage]);
        }

        return $rows->toArray();
    }

    public function columnWidths(): array
    {
        $isCommercialVehicle = $this->hasPassengerOrTonnage($this->getAutoMaster()->product_code);
        $isPolicy = $this->getDataType()->data_type === 'POLICY';

        if($isPolicy){
            if ($isCommercialVehicle)
                return [
                    'A' => 10,
                    'I' => 40,
                ];

            return [
                'A' => 10,
                'H' => 40,
            ];
        } else {
            if ($isCommercialVehicle)
                return [
                    'A' => 10,
                    'I' => 40,
                ];

            return [
                'A' => 10,
                'H' => 40,
            ];
        }
    }

    private function getAutoMaster() {
        return Auto::select('product_code')->find($this->masterId);
    }

    private function getDataType(){
        return Auto::select('data_type')->find($this->masterId);
    }

    public function styles(Worksheet $sheet) {
        $rowCount = count($this->vehicleReportData);
        $isCommercialVehicle = $this->hasPassengerOrTonnage($this->getAutoMaster()->product_code);
        $isPolicy = $this->getDataType()->data_type === 'POLICY';

        // Cells borders
        $startRow = 6;
        $endRow = $startRow + $rowCount;

        // Global
        $sheet->getStyle('A:M')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A4')->getFont()->setSize(10);
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        if($isPolicy){
            // Table column title
            $sheet->getStyle('A'.$startRow.':Q'.$startRow)->getFont()->setSize(10)->setBold(true);
            $sheet->getStyle('A'.$startRow.':Q'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A'.$startRow.':Q'.$startRow)->getFill()->applyFromArray([
                'fillType' => 'solid',
                'rotation' => 0,
                'color' => ['rgb' => 'D9D9D9'],
            ]);
            $this->conditionalPolicyStyles($sheet, $startRow, $endRow, $isCommercialVehicle, $this->getTotalPremium());
        } else {
            // Table column title
            $sheet->getStyle('A'.$startRow.':O'.$startRow)->getFont()->setSize(10)->setBold(true);
            $sheet->getStyle('A'.$startRow.':O'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A'.$startRow.':O'.$startRow)->getFill()->applyFromArray([
                'fillType' => 'solid',
                'rotation' => 0,
                'color' => ['rgb' => 'D9D9D9'],
            ]);
            $this->conditionalQuotationStyles($sheet, $startRow, $endRow, $isCommercialVehicle, $this->getTotalPremium());
        }
    }

    private function conditionalPolicyStyles($sheet, $startRow, $endRow, $isCommercialVehicle, $totalPremium){
        if (!$isCommercialVehicle) {
            $sheet->getStyle('C:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C:G')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('H')->getAlignment()->setWrapText(true);
            $sheet->getStyle('I:M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I:M')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('N')->getAlignment()->setWrapText(true);
            $sheet->getStyle('O:P')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O:P')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A'.$startRow.':P'.$endRow)->getFont()->setSize(10);
            $sheet->getStyle('A'.$startRow.':P'.$endRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);
            /**
             * Remove last column if it is not a commercial vehicle
             */
            $sheet->removeColumn('Q');

            /**
             * Append Row Total Premium
             */
            $sheet->setCellValue('N'.($endRow+1), 'Total');
            $sheet->getStyle('N'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('N'.($endRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('N'.($endRow+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('N'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('O'.($endRow+1), $totalPremium);
            $sheet->getStyle('O'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('O'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('O'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);
            /**
             * If discount inputs of all vehicles are null, remove the discount column
             */
            if(!$this->isDiscountInput()){
                $sheet->removeColumn('M');
                $sheet->removeColumn('P');
            }
        } else {
            $sheet->getStyle('C:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C:H')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('I')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J:N')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('O')->getAlignment()->setWrapText(true);
            $sheet->getStyle('P:Q')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('P:Q')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A'.$startRow.':Q'.$endRow)->getFont()->setSize(10);
            $sheet->getStyle('A'.$startRow.':Q'.$endRow)->applyFromArray([
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
            $sheet->setCellValue('O'.($endRow+1), 'Total');
            $sheet->getStyle('O'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('O'.($endRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O'.($endRow+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('O'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('P'.($endRow+1), $totalPremium);
            $sheet->getStyle('P'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('P'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('P'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);
            /**
             * If discount inputs of all vehicles are null, remove the discount column
             */
            if(!$this->isDiscountInput()){
                $sheet->removeColumn('N');
                $sheet->removeColumn('Q');
            }
        }
    }

    private function conditionalQuotationStyles($sheet, $startRow, $endRow, $isCommercialVehicle, $totalPremium){
        if (!$isCommercialVehicle) {
            $sheet->getStyle('C:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H')->getAlignment()->setWrapText(true);
            $sheet->getStyle('I:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L')->getAlignment()->setWrapText(true);
            $sheet->getStyle('M:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A'.$startRow.':N'.$endRow)->getFont()->setSize(10);
            $sheet->getStyle('A'.$startRow.':N'.$endRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);
            /**
             * Remove last column if it is not a commercial vehicle
             */
            $sheet->removeColumn('O');

            /**
             * Append Row Total Premium
             */
            $sheet->setCellValue('L'.($endRow+1), 'Total');
            $sheet->getStyle('L'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('L'.($endRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('M'.($endRow+1), $totalPremium);
            $sheet->getStyle('M'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('M'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('M'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);
            /**
             * If discount inputs of all vehicles are null, remove the discount column
             */
            if(!$this->isDiscountInput()){
                $sheet->removeColumn('K');
                $sheet->removeColumn('O');
            }
        } else {
            $sheet->getStyle('C:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J:L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M')->getAlignment()->setWrapText(true);
            $sheet->getStyle('N:O')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A'.$startRow.':O'.$endRow)->getFont()->setSize(10);
            $sheet->getStyle('A'.$startRow.':O'.$endRow)->applyFromArray([
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
            $sheet->setCellValue('M'.($endRow+1), 'Total');
            $sheet->getStyle('M'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('M'.($endRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('N'.($endRow+1), $totalPremium);
            $sheet->getStyle('N'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('N'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('N'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);
            /**
             * If discount inputs of all vehicles are null, remove the discount column
             */
            if(!$this->isDiscountInput()){
                $sheet->removeColumn('L');
                $sheet->removeColumn('O');
            }
        }
    }

    public function columnFormats(): array{
        $auto = $this->getAutoMaster();
        $isPolicy = $this->getDataType()->data_type === 'POLICY';

        if($isPolicy){
            if ($this->hasPassengerOrTonnage($auto->product_code))
                return [
                    'C' => NumberFormat::FORMAT_TEXT,
                    'D' => NumberFormat::FORMAT_TEXT,
                    'E' => NumberFormat::FORMAT_TEXT,
                    'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'Q' => NumberFormat::FORMAT_TEXT,
                ];

            return [
                'C' => NumberFormat::FORMAT_TEXT,
                'D' => NumberFormat::FORMAT_TEXT,
                'E' => NumberFormat::FORMAT_TEXT,
                'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'P' => NumberFormat::FORMAT_TEXT,
            ];
        } else {
            if ($this->hasPassengerOrTonnage($auto->product_code))
                return [
                    'C' => NumberFormat::FORMAT_TEXT,
                    'D' => NumberFormat::FORMAT_TEXT,
                    'E' => NumberFormat::FORMAT_TEXT,
                    'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'O' => NumberFormat::FORMAT_TEXT,
                ];

            return [
                'C' => NumberFormat::FORMAT_TEXT,
                'D' => NumberFormat::FORMAT_TEXT,
                'E' => NumberFormat::FORMAT_TEXT,
                'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'N' => NumberFormat::FORMAT_TEXT,
            ];
        }
    }
    /**
     * check if discount input of all vehicles are not null
     */
    private function isDiscountInput(){
        $discountList = AutoDetail::where('master_data_id', $this->masterId)->pluck('discount');
        foreach($discountList as $discount){
            if($discount){
                return true;
            }
        }
        return false;
    }

    private function getTotalPremium(){
        $totalPremium = 0;
        $premiumList = AutoDetail::select('premium')->where('master_data_id', $this->masterId)->get();
        foreach($premiumList as $premium){
            $totalPremium += $premium->premium;
        }

        return $totalPremium;
    }
}
