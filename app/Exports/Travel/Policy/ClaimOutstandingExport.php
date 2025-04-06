<?php
namespace App\Exports\Travel\Policy;

use Carbon\Carbon;
use App\Models\Travel\Policy\Claim\ClaimReportV;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Claim\ClaimOutstandingReport;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

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
            $report_period = 'Report Period : From ' . Carbon::parse($this->from_date)->format('d/m/Y') . ' to ' . Carbon::parse($this->to_date)->format('d/m/Y');
        else if ($this->from_date && !$this->to_date)
            $report_period = 'Report Period : From ' . Carbon::parse($this->from_date)->format('d/m/Y');
        else if (!$this->from_date && $this->to_date)
            $report_period = 'Report Period : Until ' . Carbon::parse($this->to_date)->format('d/m/Y');
        else
            $report_period = 'Report Period : From All';

        $titleRows = collect([
            'No.',
            'Accounting Month',
            'Account Code',
            'LOB',
            'U/W YEAR',
            'Claims No.',
            'Policy No.',
            'Company/Insured',
            'Loss of Location',
            'Loss of Description',
            'Cause of loss',
            'Inception Date',
            'Expired Date',
            //loss after deductible
            'Loss Date',
            //PGI Retention
            'FNOL Date',
            'Date of Registration',
            //Mandatory Cession
            'Date of Completed Documents',
            'Settlement Date',
            //Quota Share
            'Submission Date to ACCOUNT',
            'Check Received date from Account',
            //Facultative
            'Consultation',
            'Medication',
            //Co-Insurance
            'Lab Test',
            'Additional title',
            'ADD Amount',
            'Daily Room and Board',
            'Intensive Care Unit',
            'Hospital Miscellaneous Expenses',
            "In-Hospital Physician's Visit",
            "Pre-Hospitalization Consultation and Diagnostic Services",
            "Post-Hopitalization",
            "Ambulance Fee",
            "Special Grant",
            "Surgical Fee",
            "Emergency Accidental Outpatient Treatment",
            "Accidental Miscarriage",
            "Complications of Pregnancy",
            "Daily Hospital Cash Income",
            "Dental",
            "Optical",
            "Vaccination & Check-Up",
            "Prenancy Benefit",
            "Reserved Amount (A)",
            "Paid Amount (B)",
            "Claims Incurred (A+B)",
            "Name of Clinic/Hospital",
            "Panel / Non-Panel",
            "Type of Treatment",
            "Type of Sickness/Injury(IPD,OPD,ADD,OPT)",
            "Claimant's Name",
            "Claims Status",
            "Remarks",
            "Claims Handler",
            "Account Handler",
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
            // $aboveHeader->toArray(),
            [''],
            $titleRows->toArray(),
            // [''],
        ];
    }

    public function map($claim_paid): array
    {
        if (isset($claim_paid['underwriting_year'])) {
            return $claim_paid;
        }
        $rows = collect([
            $claim_paid['no'] ?? '',
            $claim_paid['accounting_month'] ?? '',
            $claim_paid['account_code'] ?? '',
            $claim_paid['lob'] ?? '',
            $claim_paid['uw_year'] ?? '',
            ($claim_paid['claim_status'] == 'Outstanding' && $claim_paid['claim_detail_apv_status'] == 'REJ') ? '' : $claim_paid['claim_no'] ?? '',
            $claim_paid['document_no'] ?? '', // policy_no
            $claim_paid['insured_name'] ?? '', 
            $claim_paid['location_of_loss'] ?? '',
            $claim_paid['loss_description'] ?? '',
            $claim_paid['cause_of_loss'] ?? '',
            $claim_paid['inception_date'] ? Carbon::parse($claim_paid['inception_date'])->format('d/m/Y') : '',
            $claim_paid['expired_date'] ? Carbon::parse($claim_paid['expired_date'])->format('d/m/Y') : '',
            $claim_paid['loss_date'] ? Carbon::parse($claim_paid['loss_date'])->format('d/m/Y') : '',
            $claim_paid['fnol_date'] ? Carbon::parse($claim_paid['fnol_date'])->format('d/m/Y') : '',
            $claim_paid['date_of_registration'] ? Carbon::parse($claim_paid['date_of_registration'])->format('d/m/Y') : '',
            $claim_paid['date_of_completed_doc'] ? Carbon::parse($claim_paid['date_of_completed_doc'])->format('d/m/Y') : '', // Date of Completed Documents
            $claim_paid['settlement_date'] ? Carbon::parse($claim_paid['settlement_date'])->format('d/m/Y') : '',
            '', // Submission Date to ACCOUNT
            '', // Check Received date from Account
            '', // Consultation
            $claim_paid['out_patient_benefit'] ?? '',
            '', // Lab Test
            $claim_paid['additional_title'] ?? '', // additional title
            $claim_paid['additional_amount'] ?? 0,
            $claim_paid['ordinary_room'] ?? 0,
            $claim_paid['intensive_care_unit'] ?? 0,
            $claim_paid['hospital_miscellaneous_expense'] ?? 0,
            $claim_paid['in_hospital_physician_visit'] ?? 0,
            $claim_paid['pre_hospitalisation_consultation_and_diagnostic_services'] ?? 0,
            $claim_paid['post_hospitalisation_treatment'] ?? 0,
            $claim_paid['ambulance_fee'] ?? 0,
            $claim_paid['special_grant'] ?? 0,
            $claim_paid['surgical_fee'] ?? 0,
            $claim_paid['emergency_accident_outpatient_treatment'] ?? 0,
            $claim_paid['accidental_miscarriage'] ?? 0,
            $claim_paid['complication_of_pregnancy'] ?? 0,
            $claim_paid['daily_hospital_cash_income'] ?? 0,
            $claim_paid['dental'] ?? 0, // DENTAL BENEFIT
            $claim_paid['optical'] ?? 0, // OPTICAL BENEFIT
            $claim_paid['vaccination_check_up'] ?? 0, // HEALTH CHECK UP AND VACCINATION
            $claim_paid['pregnancy_benefit'] ?? 0, // PREGNANCY BENEFIT
            $claim_paid['reserved_amount'] ?? 0, 
            $claim_paid['paid_amount'] ?? 0,
            $claim_paid['claim_incurred'] ?? 0,
            $claim_paid['clinic_name'] ?? '',
            $claim_paid['type'] ?? '',
            $claim_paid['type_of_treatment'] ?? '',
            $claim_paid['type_of_sickness'] ?? '',
            $claim_paid['claimant_name'] ?? '',
            $claim_paid['claim_status'] ?? '',
            $claim_paid['remark'] ?? '',
            $claim_paid['claim_handler'] ?? '',
            $claim_paid['account_handler'] ?? '',
        ]);

        return $rows->toArray();
    }

    private function getClaimPaidReportData()
    {
        $claimPaidExport = array();

        if ($this->from_date && $this->to_date) {
            $claimPaidReportData = ClaimReportV::whereBetween('claim_date', [$this->from_date, Carbon::parse($this->to_date)->addDays(1)->format('Y-m-d')])
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->claim_date)->format('Y');
                })->toArray();
        } else if ($this->from_date && !$this->to_date) {
            $claimPaidReportData = ClaimReportV::whereDate('claim_date', '>=', $this->from_date)
                ->get()->groupBy(function ($item) {
                        return Carbon::parse($item->claim_date)->format('Y');
                })->toArray();
        } else if (!$this->from_date && $this->to_date) {
            $claimPaidReportData = ClaimReportV::whereDate('claim_date', '<=', $this->to_date)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->claim_date)->format('Y');
                })->toArray();
        } else {
            $claimPaidReportData = ClaimReportV::get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->claim_date)->format('Y');
                })->toArray();
        }
        
        // return $claimPaidReportData;
        foreach ($claimPaidReportData as $key => $claimPaid) {
            foreach (($claimPaid) as $index => $claim) {
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
        $sheet->mergeCells('A' . $endRow . ':X' . $endRow);
        $sheet->getStyle('A' . $endRow . ':X' . $endRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        // $sheet->getStyle('L' . $endRow . ':X' . $endRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('Y' . $endRow, $this->sumTotalCol('additional_amount'));
        $sheet->setCellValue('Z' . $endRow, $this->sumTotalCol('ordinary_room'));
        $sheet->setCellValue('AA' . $endRow, $this->sumTotalCol('intensive_care_unit'));
        $sheet->setCellValue('AB' . $endRow, $this->sumTotalCol('hospital_miscellaneous_expense'));
        $sheet->setCellValue('AC' . $endRow, $this->sumTotalCol('in_hospital_physician_visit'));
        $sheet->setCellValue('AD' . $endRow, $this->sumTotalCol('pre_hospitalisation_consultation_and_diagnostic_services'));
        $sheet->setCellValue('AE' . $endRow, $this->sumTotalCol('post_hospitalisation_treatment'));
        $sheet->setCellValue('AF' . $endRow, $this->sumTotalCol('ambulance_fee'));
        $sheet->setCellValue('AG' . $endRow, $this->sumTotalCol('special_grant'));
        $sheet->setCellValue('AH' . $endRow, $this->sumTotalCol('surgical_fee'));
        $sheet->setCellValue('AI' . $endRow, $this->sumTotalCol('emergency_accident_outpatient_treatment'));
        $sheet->setCellValue('AJ' . $endRow, $this->sumTotalCol('accidental_miscarriage'));
        $sheet->setCellValue('AK' . $endRow, $this->sumTotalCol('complication_of_pregnancy'));
        $sheet->setCellValue('AL' . $endRow, $this->sumTotalCol('daily_hospital_cash_income'));
        $sheet->setCellValue('AM' . $endRow, $this->sumTotalCol('dental'));
        $sheet->setCellValue('AN' . $endRow, $this->sumTotalCol('optical'));
        $sheet->setCellValue('AO' . $endRow, $this->sumTotalCol('vaccination_check_up'));
        $sheet->setCellValue('AP' . $endRow, $this->sumTotalCol('pregnancy_benefit'));
        $sheet->setCellValue('AQ' . $endRow, $this->sumTotalCol('reserved_amount'));
        $sheet->setCellValue('AR' . $endRow, $this->sumTotalCol('paid_amount'));
        $sheet->setCellValue('AS' . $endRow, $this->sumTotalCol('claim_incurred'));
        $sheet->getStyle('A' . $endRow . ':BB' . $endRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A' . $endRow . ':BB' . $endRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'P' => 20,
            'Q' => 30,
            'R' => 20,
            'S' => 30,
            'T' => 30,
            'U' => 20,
            'V' => 20,
            'W' => 20,
            'X' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount =  count($this->claimPaidReportData);
        // Cells borders
        $startRow = 11;
        // An extra row to deal with merging rows/cells
        $endRow = $startRow + $rowCount + 1;

        // Global
        $sheet->getStyle('A:BB')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);    
       
        //number align right
        $sheet->getStyle('Y:AS')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        //text center
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AW')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AY')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('P')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('Q')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('R')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('S')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('T')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A9')->getFont()->setSize(10);
        $sheet->getStyle('A1:A9')->getFont()->setBold(true);
        $sheet->getStyle('A1:A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        //OPD
        // $sheet->mergeCells('U10:X10');
        // $sheet->getStyle('U10:X10')->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => Border::BORDER_THIN, // or Border::BORDER_MEDIUM, BORDER_DASHED, etc.
        //             'color' => ['argb' => '000'], // black color
        //         ],
        //     ],
        // ]);
        // $sheet->getStyle('U10:X10')->getFont()->setBold(true);
        // $sheet->getStyle('U10:X10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('U10:X10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        //IPD
        // $sheet->mergeCells('Y10:AP10');
        // $sheet->getStyle('Y10:AP10')->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'fillType' => 'solid',
        //             'borderStyle' => Border::BORDER_THIN, // or Border::BORDER_MEDIUM, BORDER_DASHED, etc.
        //             'color' => ['argb' => '000'], // black color
        //         ],
        //     ],
        // ]);
        // $sheet->getStyle('Y10:AP10')->getFont()->setBold(true);
        // $sheet->getStyle('Y10:AP10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('Y10:AP10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $this->conditionalStyles($sheet, $startRow, $endRow);

        // Table column title
        // $this->conditionalMergeTitle($sheet, $startRow);
        $this->conditionalStyleTitle($sheet, $startRow);
        //total
        $this->conditionalTotalRow($sheet, $endRow);
    }

    private function conditionalStyles($sheet, $startRow, $endRow)
    {
        $sheet->getStyle('A' . $startRow . ':BB' . $endRow)->getFont()->setSize(10);
        $sheet->getStyle('A' . $startRow . ':BB' . $endRow)->applyFromArray([
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
        $sheet->mergeCells('L' . $startRow . ':L' . ($startRow + 1));
        $sheet->mergeCells('M' . $startRow . ':M' . ($startRow + 1));
        $sheet->mergeCells('Y' . $startRow . ':Y' . ($startRow + 1));

        // For Corporate Customer-Local Cells
        $sheet->setCellValue('N' . ($startRow + 1), $sheet->getCell('N' . $startRow));
        $sheet->setCellValue('N' . $startRow, 'OPD');

        // For For Corporate Customer-Abroad Cells
        // $sheet->setCellValue('O' . ($startRow + 1), $sheet->getCell('O' . $startRow));
        // $sheet->setCellValue('P' . ($startRow + 1), $sheet->getCell('P' . $startRow));
        // $sheet->setCellValue('Q' . ($startRow + 1), $sheet->getCell('Q' . $startRow));
        // $sheet->setCellValue('R' . ($startRow + 1), $sheet->getCell('R' . $startRow));
        // $sheet->setCellValue('S' . ($startRow + 1), $sheet->getCell('S' . $startRow));
        // $sheet->setCellValue('T' . ($startRow + 1), $sheet->getCell('T' . $startRow));
        // $sheet->setCellValue('U' . ($startRow + 1), $sheet->getCell('U' . $startRow));
        // $sheet->setCellValue('V' . ($startRow + 1), $sheet->getCell('V' . $startRow));
        // $sheet->setCellValue('W' . ($startRow + 1), $sheet->getCell('W' . $startRow));
        // $sheet->setCellValue('X' . ($startRow + 1), $sheet->getCell('X' . $startRow));

        // //for PGI Retention
        // $sheet->setCellValue('O' . $startRow, 'PGI Retention');
        // $sheet->mergeCells('O' . $startRow . ':P' . $startRow);
        // //for Mandatory Cession
        // $sheet->setCellValue('Q' . $startRow, 'Mandatory Cession');
        // $sheet->mergeCells('Q' . $startRow . ':R' . $startRow);
        // //for Quota share
        // $sheet->setCellValue('S' . $startRow, 'Quota Share');
        // $sheet->mergeCells('S' . $startRow . ':T' . $startRow);
        // //for Facultative
        // $sheet->setCellValue('U' . $startRow, 'Facultative');
        // $sheet->mergeCells('U' . $startRow . ':V' . $startRow);
        // //for Co-Insurance
        // $sheet->setCellValue('W' . $startRow, 'Co Insurance');
        // $sheet->mergeCells('W' . $startRow . ':X' . $startRow);
        // //for MorTor: Underwriting Year
        // if ($this->claimPaidGroupByYear) {
        // $sheet->mergeCells('A' . $startRow + 2 . ':Y' . $startRow + 2);
        //     foreach ($this->claimPaidGroupByYear as $i => $group) {
        //         // $sheet->setCellValue('A' . $startRow + $group, 'MOTOR: Underwriting Year :2020');
        //         if ($i != count($this->claimPaidGroupByYear) - 1) {
        //             $sheet->mergeCells('A' . $startRow + $group + 3 . ':Y' . $startRow + $group + 3);
        //         }
        //     }
        // }
    }

    private function conditionalStyleTitle($sheet, $startRow)
    {
        $sheet->getStyle('A' . $startRow . ':BB' . $startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A' . $startRow . ':BB' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $startRow . ':BB' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($startRow)->setRowHeight(30);

        // $sheet->getStyle('A' . ($startRow + 1) . ':BB' . ($startRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('A' . ($startRow + 1) . ':BB' . ($startRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        //for Under writing year
        
        if ($this->claimPaidGroupByYear) {
            $sheet->getStyle('A' . $startRow + 1 . ':BB' . $startRow + 1)->getFont()->setSize(10)->setBold(true);
            $sheet->getStyle('A' . ($startRow + 1) . ':BB' . ($startRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . ($startRow + 1) . ':BB' . ($startRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $startRow + 1 . ':BB' . ($startRow + 1))->getFill()->applyFromArray([
                'fillType' => 'solid',
                'bold' => true,
                'size' => 10,
                'rotation' => 0,
                'color' => ['rgb' => 'FFF2CC'],
            ]);
            $sheet->mergeCells('A' . $startRow + 1 . ':BB' . $startRow + 1);
            foreach ($this->claimPaidGroupByYear as $i => $group) {
                $titleAndUnderwritingYear = 2;
                if ($i != count($this->claimPaidGroupByYear) - 1) { //don't need for last.
                    $sheet->getStyle('A' . $startRow + $group + $titleAndUnderwritingYear . ':Y' . $startRow + $group + $titleAndUnderwritingYear)->getFont()->setSize(10)->setBold(true);
                    $sheet->getStyle('A' . ($startRow + $group + $titleAndUnderwritingYear) . ':Y' . ($startRow + $group + $titleAndUnderwritingYear))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('A' . ($startRow + $group + $titleAndUnderwritingYear) . ':Y' . ($startRow + $group + $titleAndUnderwritingYear))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $this->conditionalColorUnderwritingYear($sheet, $startRow, $group, $titleAndUnderwritingYear);
                }
            }
        }
        //end underwriting
    }

    private function conditionalColorUnderwritingYear($sheet, $startRow, $group, $titleAndUnderwritingYear)
    {
        $sheet->mergeCells('A' . $startRow + $group + $titleAndUnderwritingYear . ':BB' . ($startRow + $group + $titleAndUnderwritingYear));
        $sheet->getStyle('A' . $startRow + $group + $titleAndUnderwritingYear . ':BB' . ($startRow + $group + $titleAndUnderwritingYear))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'bold' => true,
            'size' => 10,
            'rotation' => 0,
            'color' => ['rgb' => 'FFF2CC'],
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'Y' => '#,##0.00_);[Red](#,##0.00)',
            'Z' => '#,##0.00_);[Red](#,##0.00)',
            'AA' => '#,##0.00_);[Red](#,##0.00)',
            'AB' => '#,##0.00_);[Red](#,##0.00)',
            'AC' => '#,##0.00_);[Red](#,##0.00)',
            'AD' => '#,##0.00_);[Red](#,##0.00)',
            'AE' => '#,##0.00_);[Red](#,##0.00)',
            'AF' => '#,##0.00_);[Red](#,##0.00)',
            'AG' => '#,##0.00_);[Red](#,##0.00)',
            'AH' => '#,##0.00_);[Red](#,##0.00)',
            'AI' => '#,##0.00_);[Red](#,##0.00)',
            'AJ' => '#,##0.00_);[Red](#,##0.00)',
            'AK' => '#,##0.00_);[Red](#,##0.00)',
            'AL' => '#,##0.00_);[Red](#,##0.00)',
            'AM' => '#,##0.00_);[Red](#,##0.00)',
            'AN' => '#,##0.00_);[Red](#,##0.00)',
            'AO' => '#,##0.00_);[Red](#,##0.00)',
            'AP' => '#,##0.00_);[Red](#,##0.00)',
            'AQ' => '#,##0.00_);[Red](#,##0.00)',
            'AR' => '#,##0.00_);[Red](#,##0.00)',
            'AS' => '#,##0.00_);[Red](#,##0.00)',
            'Z' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => '0%',
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => '0%',
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => '0%',
            'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => '0%',
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'X' => '0%',
        ];
    }
}