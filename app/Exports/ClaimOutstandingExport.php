<?php

namespace App\Exports;

use App\Models\Claim\ClaimOutstandingReport;
use Carbon\Carbon;
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

class ClaimOutstandingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{

    private $from_date;
    private $to_date;
    private $claimPaidReportData;
    private $claimPaidGroupByYear = [];

    public function __construct($from_date, $to_date)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $this->claimPaidReportData = $this->getClaimPaidReportData();
        // ->map(function ($item, $key) {
        //     $item->no = $key + 1;
        //     return $item;
        // });
        return collect($this->claimPaidReportData);
    }

    public function headings(): array
    {
        if ($this->from_date && $this->to_date)
            $report_period = 'Report Period From ' . Carbon::parse($this->from_date)->format('d/m/Y') . ' to ' . Carbon::parse($this->to_date)->format('d/m/Y');
        else if ($this->from_date && !$this->to_date)
            $report_period = 'Report Period From ' . Carbon::parse($this->from_date)->format('d/m/Y');
        else if (!$this->from_date && $this->to_date)
            $report_period = 'Report Period Until ' . Carbon::parse($this->to_date)->format('d/m/Y');
        else
            $report_period = 'Report Period From All';
        $titleRows = collect([
            'No.',
            'Risk Type',
            'Claim No',
            'Policy No',
            'Insured Name',
            'Plate Number',
            'Loss Location',
            'Type of Loss',
            'Inception Date',
            'Loss Date',
            'Date of Notification',
            //loss after deductible
            'Loss 100%',
            //PGI Retention
            'Cedant Paid',
            'Share %',
            //Mandatory Cession
            'Amount',
            'Share %',
            //Quota Share
            'Amount',
            'Share %',
            //Facultative
            'Amount',
            'Share %',
            //Co-Insurance
            'Amount',
            'Share %',
            'Status',
        ]);

        return [
            [
                'Phillip General Insurance (Cambodia) Plc.'
            ],
            [''],
            ['OUTSTANDING LOSS RISK BORDEREAUX'],
            [''],
            ['CLAIMS OUTSTANDING '],
            [''],
            [
                $report_period
            ],
            [''],
            [
                'Currency : USD'
            ],
            [''],
            $titleRows->toArray(),
            [''],
        ];
    }

    public function map($claim_paid): array
    {
        if (isset($claim_paid['underwriting_year'])) {
            return $claim_paid;
        }
        $rows = collect([
            $claim_paid['no'] ?? '',
            $claim_paid['product_line_code'] ?? '',
            $claim_paid['claim_no'] ?? '',
            $claim_paid['document_no'] ?? '',
            $claim_paid['insured_name'] ?? '',
            $claim_paid['plate_no'] ?? '',
            $claim_paid['incident_location']??'',
            $claim_paid['cause_of_loss_desc']??'',
            $claim_paid['effective_date_from'] ? Carbon::parse($claim_paid['effective_date_from'])->format('d/m/Y') : '',
            $claim_paid['incident_date'] ? Carbon::parse($claim_paid['incident_date'])->format('d/m/Y') : '',
            $claim_paid['notification_date'] ? Carbon::parse($claim_paid['notification_date'])->format('d/m/Y') : '',
            $claim_paid['loss_of_deductible'],
            $claim_paid['pgi_cedant_amount'],
            $claim_paid['pgi_share'],
            $claim_paid['mandatory_cession_amount'],
            $claim_paid['mandatory_cession_share'],
            $claim_paid['quota_amount'],
            $claim_paid['quota_share'],
            $claim_paid['facultative_amount'],
            $claim_paid['facultative_share'],
            $claim_paid['co_insurance_amount'],
            $claim_paid['co_insurance_share'],
            $claim_paid['status'],
        ]);

        return $rows->toArray();
    }

    private function getClaimPaidReportData()
    {
        $claimPaidExport = array();

        if ($this->from_date && $this->to_date) {
            $claimPaidReportData = ClaimOutstandingReport::where('loss_of_deductible','<>',0)->whereBetween('approved_at', [$this->from_date, Carbon::parse($this->to_date)->addDays(1)->format('Y-m-d')])
                ->orderByDESC('status')->orderByDESC('claim_no')->get()
                ->groupBy(function ($item) {
                    if (isset($item->updated_at)) {
                        return $item->updated_at->format('Y');
                    }
                    return $item->created_at->format('Y');
                })->toArray();
        } else if ($this->from_date && !$this->to_date) {
            $claimPaidReportData = ClaimOutstandingReport::where('loss_of_deductible','<>',0)->whereDate('approved_at', '>=', $this->from_date)->orderByDESC('status')->orderByDESC('claim_no')->get()->groupBy(function ($item) {
                if (isset($item->updated_at)) {
                    return $item->updated_at->format('Y');
                }
                return $item->created_at->format('Y');
            })->toArray();
        } else if (!$this->from_date && $this->to_date) {
            $claimPaidReportData = ClaimOutstandingReport::where('loss_of_deductible','<>',0)->whereDate('approved_at', '<=', $this->to_date)->orderByDESC('status')->orderByDESC('claim_no')->get()->groupBy(function ($item) {
                if (isset($item->updated_at)) {
                    return $item->updated_at->format('Y');
                }
                return $item->created_at->format('Y');
            })->toArray();
        } else {
            $claimPaidReportData = ClaimOutstandingReport::where('loss_of_deductible','<>',0)->orderByDESC('status')->orderByDESC('claim_no')->get()->groupBy(function ($item) {
                if (isset($item->updated_at)) {
                    return $item->updated_at->format('Y');
                }
                return $item->created_at->format('Y');
            })->toArray();
        }

        foreach ($claimPaidReportData as $key => $claimPaid) {
            foreach ($claimPaid as $index => $claim) {
                if ($index == 0) {
                    array_push($this->claimPaidGroupByYear, count($claimPaid));
                    array_unshift($claimPaid, ['underwriting_year' => 'MOTOR : Underwriting Year ' . $key]);
                    $claimPaidExport = array_merge($claimPaidExport, array($claimPaid[$index]));
                    //row that was replaced by underwriting year so index plus 1 (go to next row after underwriting year)
                    $claimPaid[$index + 1] = array_merge($claim, ['no' => $index + 1]);
                    $claimPaidExport = array_merge($claimPaidExport, array($claimPaid[$index + 1]));
                }
                if ($index != 0) {
                    $claimPaid[$index] = array_merge($claim, ['no' => $index + 1]);
                    $claimPaidExport = array_merge($claimPaidExport, array($claimPaid[$index]));
                }
            }
        }
        return $claimPaidExport;
    }

    private function sumTotalCol($column_name)
    {
        return
            collect($this->claimPaidReportData)->sum($column_name) < 0 ?
            '(' . number_format(abs(collect($this->claimPaidReportData)->sum($column_name)), 2, '.', ',') . ')' :
            collect($this->claimPaidReportData)->sum($column_name);
    }

    private function conditionalTotalRow($sheet, $endRow)
    {
        $sheet->setCellValue('A' . $endRow, 'TOTAL');
        $sheet->mergeCells('A' . $endRow . ':K' . $endRow);
        $sheet->getStyle('L' . $endRow . ':V' . $endRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('L' . $endRow, $this->sumTotalCol('loss_of_deductible'));
        $sheet->setCellValue('M' . $endRow, $this->sumTotalCol('pgi_cedant_amount'));
        $sheet->setCellValue('O' . $endRow, $this->sumTotalCol('mandatory_cession_amount'));
        $sheet->setCellValue('Q' . $endRow, $this->sumTotalCol('quota_amount'));
        $sheet->setCellValue('S' . $endRow, $this->sumTotalCol('facultative_amount'));
        $sheet->setCellValue('U' . $endRow, $this->sumTotalCol('co_insurance_amount'));
        $sheet->getStyle('A' . $endRow . ':K' . $endRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L' . $endRow . ':W' . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $endRow . ':W' . $endRow)->getFont()->setSize(10)->setBold(true);
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount =  count($this->claimPaidReportData);
        // Cells borders
        $startRow = 11;
        // An extra row to deal with merging rows/cells
        $endRow = $startRow + $rowCount + 2;

        // Global
        $sheet->getStyle('A:W')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A9')->getFont()->setSize(10);
        $sheet->getStyle('A1:A9')->getFont()->setBold(true);
        $sheet->getStyle('A1:A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $this->conditionalStyles($sheet, $startRow, $endRow);

        // Table column title
        $this->conditionalMergeTitle($sheet, $startRow);
        $this->conditionalStyleTitle($sheet, $startRow);
        //total
        $this->conditionalTotalRow($sheet, $endRow);
    }

    private function conditionalStyles($sheet, $startRow, $endRow)
    {
        $sheet->getStyle('A' . $startRow . ':W' . $endRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $startRow . ':W' . $endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
    }

    private function conditionalMergeTitle($sheet, $startRow)
    {
        // Normal cells
        $sheet->mergeCells('A' . $startRow . ':A' . ($startRow + 1));
        $sheet->mergeCells('B' . $startRow . ':B' . ($startRow + 1));
        $sheet->mergeCells('C' . $startRow . ':C' . ($startRow + 1));
        $sheet->mergeCells('D' . $startRow . ':D' . ($startRow + 1));
        $sheet->mergeCells('E' . $startRow . ':E' . ($startRow + 1));
        $sheet->mergeCells('F' . $startRow . ':F' . ($startRow + 1));
        $sheet->mergeCells('G' . $startRow . ':G' . ($startRow + 1));
        $sheet->mergeCells('H' . $startRow . ':H' . ($startRow + 1));
        $sheet->mergeCells('I' . $startRow . ':I' . ($startRow + 1));
        $sheet->mergeCells('J' . $startRow . ':J' . ($startRow + 1));
        $sheet->mergeCells('K' . $startRow . ':K' . ($startRow + 1));
        $sheet->mergeCells('W' . $startRow . ':W' . ($startRow + 1));

        // For Corporate Customer-Local Cells
        $sheet->setCellValue('L' . ($startRow + 1), $sheet->getCell('L' . $startRow));
        $sheet->setCellValue('L' . $startRow, 'Loss after Deductible');

        $sheet->setCellValue('M' . ($startRow + 1), $sheet->getCell('M' . $startRow));
        $sheet->setCellValue('N' . ($startRow + 1), $sheet->getCell('N' . $startRow));
        $sheet->setCellValue('O' . ($startRow + 1), $sheet->getCell('O' . $startRow));
        $sheet->setCellValue('P' . ($startRow + 1), $sheet->getCell('P' . $startRow));
        $sheet->setCellValue('Q' . ($startRow + 1), $sheet->getCell('Q' . $startRow));
        $sheet->setCellValue('R' . ($startRow + 1), $sheet->getCell('R' . $startRow));
        $sheet->setCellValue('S' . ($startRow + 1), $sheet->getCell('S' . $startRow));
        $sheet->setCellValue('T' . ($startRow + 1), $sheet->getCell('T' . $startRow));
        $sheet->setCellValue('U' . ($startRow + 1), $sheet->getCell('U' . $startRow));
        $sheet->setCellValue('V' . ($startRow + 1), $sheet->getCell('V' . $startRow));
        $sheet->setCellValue('W' . ($startRow + 1), $sheet->getCell('W' . $startRow));

        //for PGI Retention
        $sheet->setCellValue('M' . $startRow, 'PGI Retention');
        $sheet->mergeCells('M' . $startRow . ':N' . $startRow);
        //for Mandatory Cession
        $sheet->setCellValue('O' . $startRow, 'Mandatory Cession');
        $sheet->mergeCells('O' . $startRow . ':P' . $startRow);
        //for Quota share
        $sheet->setCellValue('Q' . $startRow, 'Quota Share');
        $sheet->mergeCells('Q' . $startRow . ':R' . $startRow);
        //for Facultative
        $sheet->setCellValue('S' . $startRow, 'Facultative');
        $sheet->mergeCells('S' . $startRow . ':T' . $startRow);
        //for Co-Insurance
        $sheet->setCellValue('U' . $startRow, 'Co Insurance');
        $sheet->mergeCells('U' . $startRow . ':V' . $startRow);
        //for MorTor: Underwriting Year
        if ($this->claimPaidGroupByYear) {
        $sheet->mergeCells('A' . $startRow + 2 . ':W' . $startRow + 2);
            foreach ($this->claimPaidGroupByYear as $i => $group) {
                // $sheet->setCellValue('A' . $startRow + $group, 'MOTOR: Underwriting Year :2020');
                if ($i != count($this->claimPaidGroupByYear) - 1) {
                    $sheet->mergeCells('A' . $startRow + $group + 3 . ':W' . $startRow + $group + 3);
                }
            }
        }
    }

    private function conditionalStyleTitle($sheet, $startRow)
    {
        $sheet->getStyle('A' . $startRow . ':W' . $startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A' . ($startRow + 1) . ':W' . ($startRow + 1))->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A' . $startRow . ':W' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $startRow . ':W' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A' . ($startRow + 1) . ':W' . ($startRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . ($startRow + 1) . ':W' . ($startRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        //for Under writing year
        $sheet->getStyle('A' . $startRow + 2 . ':W' . $startRow + 2)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A' . ($startRow + 2) . ':W' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A' . ($startRow + 2) . ':W' . ($startRow + 2))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $startRow + 2 . ':W' . ($startRow + 2))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'bold' => true,
            'size' => 10,
            'rotation' => 0,
            'color' => ['rgb' => 'FFF2CC'],
        ]);
        if ($this->claimPaidGroupByYear) {
            foreach ($this->claimPaidGroupByYear as $i => $group) {
                $titleAndUnderwritingYear = 3;
                if ($i != count($this->claimPaidGroupByYear) - 1) { //don't need for last.
                    $sheet->getStyle('A' . $startRow + $group + $titleAndUnderwritingYear . ':W' . $startRow + $group + $titleAndUnderwritingYear)->getFont()->setSize(10)->setBold(true);
                    $sheet->getStyle('A' . ($startRow + $group + $titleAndUnderwritingYear) . ':W' . ($startRow + $group + $titleAndUnderwritingYear))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('A' . ($startRow + $group + $titleAndUnderwritingYear) . ':W' . ($startRow + $group + $titleAndUnderwritingYear))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $this->conditionalColorUnderwritingYear($sheet, $startRow, $group, $titleAndUnderwritingYear);
                }
            }
        }
        //end underwriting

    }

    private function conditionalColorUnderwritingYear($sheet, $startRow, $group, $titleAndUnderwritingYear)
    {
        $sheet->getStyle('A' . $startRow + $group + $titleAndUnderwritingYear . ':W' . ($startRow + $group + $titleAndUnderwritingYear))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'bold' => true,
            'size' => 10,
            'rotation' => 0,
            'color' => ['rgb' => 'FFF2CC'],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
            'Q' => 20,
            'R' => 20,
            'S' => 20,
            'T' => 20,
            'U' => 20,
            'V' => 20,
            'W' => 20,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => '#,##0.00_);[Red](#,##0.00)',
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => '0%',
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => '0%',
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => '0%',
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => '0%',
            'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => '0%',
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
