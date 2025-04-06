<?php

namespace App\Exports;

use App\Models\Cover\Cover;
use App\Models\Deductible\DeductibleDetail;
use App\Models\Insurance\Auto;
use App\Models\Insurance\AutoDetail;
use App\Models\Make\Make;
use App\Models\Make\MakeModel;
use App\Models\Product\Product;
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
use App\Scopes\ActiveScope;

class VehiclesEndorsementExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    private $masterId;
    private $document_no;
    private $isListForAll;
    private $isGeneralInfoVehicle;
    private $vehicleReportData;

    public function __construct($masterId, $document_no, $isListForAll = false, $isGeneralInfoVehicle = false)
    {
        $this->masterId = $masterId;
        $this->document_no = $document_no;
        $this->isListForAll = $isListForAll;
        $this->isGeneralInfoVehicle = $isGeneralInfoVehicle;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $export = AutoDetail::withoutGlobalScopes([ActiveScope::class])->with('auto:id,effective_date_from,effective_date_to')->where('master_data_id', $this->masterId);

        // Query for sepecific endorsement
        if (!$this->isListForAll) {
            $export = $export->where('endorsement_stage', $this->document_no);
        }

        if($this->isGeneralInfoVehicle){
            $export = $export->where('status', 'ACT');
        }

        $this->vehicleReportData = $export->orderBy('id')->get()->map(function($item, $key) {

            $model = MakeModel::find($item->model_id);
            $make = Make::find($model ? $model->make_id : null);

            $deductibles = $this->getDeductibles($item->id);
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

    private function getDeductibles($autoDetailId) {
        return DeductibleDetail::select('product_code', 'comp_code', 'value')
            ->where('detail_id', $autoDetailId)
            ->orderBy('seq')
            ->get()
            ->map(function($item) {
                $cover = Cover::select('name', 'deductible_label')
                    ->where('code', $item->comp_code)
                    ->where('product_code', $item->product_code)
                    ->first();

                $item->cover = $cover;
                return $item;
            });
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
            'Cubic Capacity/Engine Power',
            'Coverages',
            'Inception Date',
            'Expiry Date',
            'Endorsement Effective Date',
            'Sum Insured (USD)',
            'No Claim Discount (%)',
            'Discount (%)',
            'Deductible (Each and every claim)',
            'Premium (USD)',
            'Remarks',
            'Transaction Type'
        ]);

        // If product has specification, add seats/tonnage column
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
                ($this->isListForAll && !$this->isGeneralInfoVehicle ? 'POLICY NO: ' : 'ENDORSEMENT NO: ') . $this->document_no,
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
            \Carbon\Carbon::parse($vehicle->auto->effective_date_from)->format('d/m/Y'),
            \Carbon\Carbon::parse($vehicle->auto->effective_date_to)->format('d/m/Y'),
            \Carbon\Carbon::parse($vehicle->endorsement_e_date)->format('d/m/Y'),
            $vehicle->vehicle_value,
            $vehicle->ncd ?? '0',
            $vehicle->discount ?? '0',
            $vehicle->deductible,
            $vehicle->premium < 0 ? '('. number_format(abs($vehicle->premium), 2,'.', ',') .')' : $vehicle->premium,
            $vehicle->remark,
            $vehicle->endorsement_state,
        ]);

        // If product has specification, add seats/tonnage column
        if ($this->hasPassengerOrTonnage($vehicle->product_code)) {
            $rows->splice(6, 0, [$vehicle->passenger_tonnage]);
        }

        return $rows->toArray();
    }

    public function columnWidths(): array
    {
        $isCommercialVehicle = $this->hasPassengerOrTonnage($this->getAutoMaster()->product_code);

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

    private function getAutoMaster() {
        return Auto::select('product_code')->find($this->masterId);
    }

    public function styles(Worksheet $sheet) {
        $rowCount = count($this->vehicleReportData);

        $isCommercialVehicle = $this->hasPassengerOrTonnage($this->getAutoMaster()->product_code);

        // Cells borders
        $startRow = 5;
        $endRow = $startRow + $rowCount;

        // Global
        $sheet->getStyle('A:M')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A4')->getFont()->setSize(10);
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Table column title
        $sheet->getStyle('A'.$startRow.':S'.$startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':S'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$startRow.':S'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'D9D9D9'],
        ]);
        $this->conditionalStyles($sheet, $startRow, $endRow, $isCommercialVehicle, $this->getTotalPremium());
    }

    private function conditionalStyles($sheet, $startRow, $endRow, $isCommercialVehicle, $totalPremium){
        if (!$isCommercialVehicle) {
            $sheet->getStyle('C:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C:G')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('H')->getAlignment()->setWrapText(true);
            $sheet->getStyle('I:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I:N')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('O')->getAlignment()->setWrapText(true);
            $sheet->getStyle('P:R')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('P:R')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A'.$startRow.':R'.$endRow)->getFont()->setSize(10);
            $sheet->getStyle('A'.$startRow.':R'.$endRow)->applyFromArray([
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
            $sheet->removeColumn('S');

            /**
             * Append Row Total Premium
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
                $sheet->removeColumn('R');

                if($this->isGeneralInfoVehicle){
                    $sheet->removeColumn('Q');
                }
            } else {
                if($this->isGeneralInfoVehicle){
                    $sheet->removeColumn('R');
                }
            }

        } else {
            $sheet->getStyle('C:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C:H')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('I')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J:O')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J:O')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('P')->getAlignment()->setWrapText(true);
            $sheet->getStyle('Q:S')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Q:S')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A'.$startRow.':S'.$endRow)->getFont()->setSize(10);
            $sheet->getStyle('A'.$startRow.':S'.$endRow)->applyFromArray([
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
            $sheet->setCellValue('P'.($endRow+1), 'Total');
            $sheet->getStyle('P'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('P'.($endRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('P'.($endRow+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('P'.($endRow+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('Q'.($endRow+1), $totalPremium);
            $sheet->getStyle('Q'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('Q'.($endRow+1))->getFont()->setBold(true);
            $sheet->getStyle('Q'.($endRow+1))->applyFromArray([
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
                $sheet->removeColumn('O');
                $sheet->removeColumn('S');

                if($this->isGeneralInfoVehicle){
                    $sheet->removeColumn('Q');
                }
            } else {
                if($this->isGeneralInfoVehicle){
                    $sheet->removeColumn('S');
                }
            }
        }
    }


    public function columnFormats(): array{
        $auto = $this->getAutoMaster();

            if ($this->hasPassengerOrTonnage($auto->product_code))
                return [
                    'C' => NumberFormat::FORMAT_TEXT,
                    'D' => NumberFormat::FORMAT_TEXT,
                    'E' => NumberFormat::FORMAT_TEXT,
                    'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'R' => NumberFormat::FORMAT_TEXT,
                    'S' => NumberFormat::FORMAT_TEXT,
                ];

            return [
                'C' => NumberFormat::FORMAT_TEXT,
                'D' => NumberFormat::FORMAT_TEXT,
                'E' => NumberFormat::FORMAT_TEXT,
                'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'Q' => NumberFormat::FORMAT_TEXT,
                'R' => NumberFormat::FORMAT_TEXT,
            ];
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

    public function getTotalPremium() {
        $endorsementPremium = $this->vehicleReportData->sum('premium');

        if ($endorsementPremium < 0)
            return '(' . number_format(abs($endorsementPremium), 2,'.', ',') . ')';

        return number_format($endorsementPremium, 2,'.', ',');
    }
}
