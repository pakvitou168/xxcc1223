<?php

namespace App\Exports;

use App\Models\Product\Product;
use App\Models\Renewal\Renewal;
use App\Models\Renewal\RenewalNoticeMainV;
use App\Models\Renewal\RenewalNoticeVehicleV;
use Carbon\Carbon;
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

class VehiclesRenewalExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    private $masterId;
    private $document_no;
    private $vehicleReportData;
    private $renewal;
    public function __construct($masterId, $document_no)
    {
        $this->masterId = $masterId;
        $this->document_no = $document_no;
        $this->renewal = Renewal::find($this->masterId);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $this->vehicleReportData = RenewalNoticeVehicleV::where('renew_policy_id', $this->masterId)->get()->map(function ($item, $key) {
            $item->no = $key + 1;
            $item->make_model = (string)$item->make_model;
            $item->ncd = $item->no_claim_discount;
            $item->coverages = $item->selected_cover_pkg;
            return $item;
        });

        return $this->vehicleReportData;
    }

    public function headings(): array
    {
        $titleRows = collect([
            'No.',
            'Make and Model',
            'Plate No.',
            'Chassis No.',
            'Engine No.',
            'Year of Manufacture',
            'Cubic Capacity',
            'Coverages',
            'Inception Date',
            'Expiry Date',
            'Sum Insured (USD)',
            'No Claim Discount (%)',
            'Discount (%)',
            'Total Claim Amount (USD)',
            'Claim Paid (USD)',
            'Claim Reserve/outstanding (USD)',
            'No. of Claim',
            'Claim Ratio %',
            'Deductible (Each and every claim)',
            'Premium (USD)'
        ]);

        // If is commercial vehicle, add seats/tonnage column
        if ($this->hasPassengerOrTonnage($this->renewal->product_code)) {
            $titleRows->splice(7, 0, ['Seats/Tonnage']);
        }
        $mainRenewal = RenewalNoticeMainV::where('renew_policy_id', $this->masterId)->first();
        return [
            [
                'LIST OF FLEET VEHICLES',
            ],
            [
                'INSURED NAME: ' . $mainRenewal->insured_name,
            ],
            [
                'Renewal Policy NO.: ' . $this->renewal->document_no,
            ],
            [
                'SUB CLASS: ' . Str::upper(Product::where('code', $this->renewal->product_code)->first()?->name)
            ],

            [''],

            $titleRows->toArray()
        ];
    }

    private function hasPassengerOrTonnage($productCode)
    {
        $specification = Product::getProductSpecificationByCode($productCode);

        return $specification === 'TONNAGE' || $specification === 'PASSENGER';
    }

    public function map($vehicle): array
    {
        $deductibleText = str_replace(';', "\r\n", $vehicle->deductible);
        $total = floatval($vehicle->claim_paid) + floatval($vehicle->claim_outstanding);
        $rows = collect([
            $vehicle->no,
            $vehicle->make_model,
            $vehicle->plate_no . ' ', // need to put like that otherwise there will be cases that it results in the value of 0
            $vehicle->chassis_no,
            $vehicle->engine_no,
            $vehicle->manufacturing_year,
            $vehicle->cubic,
            $vehicle->selected_cover_pkg,
            Carbon::parse($vehicle->effective_date_from)->format('d/m/Y'),
            Carbon::parse($vehicle->effective_date_to)->format('d/m/Y'),
            $vehicle->sum_insured,
            $vehicle->ncd,
            $vehicle->discount,
            $this->formatClaimAmount($total),
            $vehicle->claim_paid,
            $vehicle->claim_outstanding,
            $vehicle->claim_request_count,
            $vehicle->claim_ratio,
            $deductibleText,
            $vehicle->premium < 0 ? '(' . number_format(abs($vehicle->premium), 2, '.', ',') . ')' : $vehicle->premium
        ]);

        // If is commercial vehicle, add seats/tonnage column
        if ($this->hasPassengerOrTonnage($this->renewal->product_code)) {
            $passenger_tonnage = $vehicle->passenger ?? $vehicle->tonnage;
            $rows->splice(7, 0, [$passenger_tonnage]);
        }

        return $rows->toArray();
    }

    public function formatClaimAmount($total_value)
    {
        return $total_value < 0
            ? '(' . number_format(abs($total_value), 2, '.', ',') . ')'
            : number_format($total_value, 2, '.', ',');
    }

    public function columnWidths(): array
    {
        $isCommercialVehicle = $this->hasPassengerOrTonnage($this->renewal->product_code);

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


    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->vehicleReportData);
        $isCommercialVehicle = $this->hasPassengerOrTonnage($this->renewal->product_code);

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

        $this->conditionalPolicyStyles($sheet, $startRow, $endRow, $isCommercialVehicle);
    }

    private function conditionalPolicyStyles($sheet, $startRow, $endRow, $isCommercialVehicle)
    {
        if (!$isCommercialVehicle) {
            // Table column title
            $sheet->getStyle('A' . $startRow . ':T' . $startRow)->getFont()->setSize(10)->setBold(true);
            $sheet->getStyle('A' . $startRow . ':T' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $startRow . ':T' . $startRow)->getFill()->applyFromArray([
                'fillType' => 'solid',
                'rotation' => 0,
                'color' => ['rgb' => 'D9D9D9'],
            ]);
            $sheet->getStyle('C:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C:G')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('H')->getAlignment()->setWrapText(true);
            $sheet->getStyle('I:T')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I:T')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $startRow . ':T' . $endRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':T' . $endRow)->applyFromArray([
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

            /**
             * Append Row Total Premium
             */
            $sheet->setCellValue('J' . ($endRow + 1), 'Total');
            $sheet->getStyle('J' . ($endRow + 1))->getFont()->setBold(true);
            $sheet->getStyle('J' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . ($endRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('J' . ($endRow + 1) . ':T' . ($endRow + 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('K' . ($endRow + 1), $this->getTotalSumInsured());
            $sheet->setCellValue('N' . ($endRow + 1), $this->getTotalClaimAmount());
            $sheet->setCellValue('O' . ($endRow + 1), $this->getTotalClaimPaid());
            $sheet->setCellValue('P' . ($endRow + 1), $this->getTotalClaimOutstanding());
            $sheet->setCellValue('Q' . ($endRow + 1), $this->getTotalClaimNo());
            $sheet->setCellValue('R' . ($endRow + 1), $this->getTotalLossRatio());
            $sheet->setCellValue('T' . ($endRow + 1), $this->getTotalPremium());

            $sheet->getStyle('K' . ($endRow + 1) . ':T' . ($endRow + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('K' . ($endRow + 1) . ':T' . ($endRow + 1))->getFont()->setBold(true);
            $sheet->getStyle('K' . ($startRow + 1) . ':T' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle('Q')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('R' . ($endRow + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->getStyle('S')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
            /**
             * If discount inputs of all vehicles are null, remove the discount column
             */
            if (!$this->isDiscountInput()) {
                $sheet->removeColumn('M');
            }
        } else {
            $sheet->getStyle('A' . $startRow . ':U' . $startRow)->getFont()->setSize(10)->setBold(true);
            $sheet->getStyle('A' . $startRow . ':U' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $startRow . ':U' . $startRow)->getFill()->applyFromArray([
                'fillType' => 'solid',
                'rotation' => 0,
                'color' => ['rgb' => 'D9D9D9'],
            ]);

            $sheet->getStyle('C:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C:H')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('I')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J:U')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J:U')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('A' . $startRow . ':U' . $endRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':U' . $endRow)->applyFromArray([
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
            $sheet->setCellValue('K' . ($endRow + 1), 'Total');
            $sheet->getStyle('K' . ($endRow + 1))->getFont()->setBold(true);
            $sheet->getStyle('K' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . ($endRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('K' . ($endRow + 1) . ':U' . ($endRow + 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ]);

            $sheet->setCellValue('L' . ($endRow + 1), $this->getTotalSumInsured());
            $sheet->setCellValue('O' . ($endRow + 1), $this->getTotalClaimAmount());
            $sheet->setCellValue('P' . ($endRow + 1), $this->getTotalClaimPaid());
            $sheet->setCellValue('Q' . ($endRow + 1), $this->getTotalClaimOutstanding());
            $sheet->setCellValue('R' . ($endRow + 1), $this->getTotalClaimNo());
            $sheet->setCellValue('S' . ($endRow + 1), $this->getTotalLossRatio());
            $sheet->setCellValue('U' . ($endRow + 1), $this->getTotalPremium());

            $sheet->getStyle('L' . ($endRow + 1) . ':U' . ($endRow + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('L' . ($startRow + 1) . ':U' . ($endRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('R'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('S'.($endRow+1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->getStyle('T')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            /**
             * If discount inputs of all vehicles are null, remove the discount column
             */
            if (!$this->isDiscountInput()) {
                $sheet->removeColumn('N');
            }
        }
    }

    public function columnFormats(): array
    {
        if ($this->hasPassengerOrTonnage($this->renewal->product_code))
        {
            return [
                'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'M' => NumberFormat::FORMAT_PERCENTAGE_00,
                'N' => NumberFormat::FORMAT_PERCENTAGE_00,
                'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'R' => NumberFormat::FORMAT_NUMBER,
                'S' => NumberFormat::FORMAT_PERCENTAGE_00,
                'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            ];
        }

        return [
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_PERCENTAGE_00,
            'M' => NumberFormat::FORMAT_PERCENTAGE_00,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => NumberFormat::FORMAT_NUMBER,
            'R' => NumberFormat::FORMAT_PERCENTAGE_00,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * check if discount input of all vehicles are not null
     */
    private function isDiscountInput()
    {
        $discountList = RenewalNoticeVehicleV::where('renew_policy_id', $this->masterId)->pluck('discount');
        foreach ($discountList as $discount) {
            if ($discount) {
                return true;
            }
        }
        return false;
    }

    private function getTotalPremium()
    {
        return $this->collection()->sum('premium');
    }
    private function getTotalClaimNo()
    {
        return $this->collection()->sum('claim_request_count');
    }
    private function getTotalLossRatio()
    {
        return $this->getTotalClaimAmount()/$this->getTotalPremium();
    }
    private function getTotalSumInsured()
    {
        return $this->collection()->sum('sum_insured');
    }
    private function getTotalClaimPaid()
    {
        return $this->collection()->sum('claim_paid');
    }
    private function getTotalClaimOutstanding()
    {
        return $this->collection()->sum('claim_outstanding');
    }
    private function getTotalClaimAmount()
    {
        return $this->getTotalClaimPaid() + $this->getTotalClaimOutstanding();
    }
}
