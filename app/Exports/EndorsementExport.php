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
use App\Models\Insurance\Endorsement\EndorsementReport;

class EndorsementExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{

    private $issue_date_from;
    private $issue_date_to;
    private $vehicleReportData;

    public function __construct($issue_date_from, $issue_date_to)
    {
        $this->issue_date_from = $issue_date_from;
        $this->issue_date_to = $issue_date_to;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->vehicleReportData =  $this->getVehicleReportData()
                    ->map(function($item, $key){
                        $item->no = $key + 1;
                        return $item;
                    });
        return $this->vehicleReportData;
    }

    private function getVehicleReportData(){
        if($this->issue_date_from && $this->issue_date_to)
            return EndorsementReport::whereDate('date_of_issue', '>=' ,$this->issue_date_from)->whereDate('date_of_issue', '<=',$this->issue_date_to)->orderByDesc('id')->get();
        else if($this->issue_date_from && !$this->issue_date_to)
            return EndorsementReport::whereDate('date_of_issue', '>=', $this->issue_date_from)->orderByDesc('id')->get();
        else if(!$this->issue_date_from && $this->issue_date_to)
            return EndorsementReport::whereDate('date_of_issue', '<=', $this->issue_date_to)->orderByDesc('id')->get();
        else
            return EndorsementReport::orderByDesc('id')->get();
    }

    public function headings(): array
    {
        if($this->issue_date_from && $this->issue_date_to)
            $report_period = 'Report Period From '. \Carbon\Carbon::parse($this->issue_date_from)->format('d/m/Y') .' to '. \Carbon\Carbon::parse($this->issue_date_to)->format('d/m/Y');
        else if($this->issue_date_from && !$this->issue_date_to)
            $report_period = 'Report Period From '. \Carbon\Carbon::parse($this->issue_date_from)->format('d/m/Y');
        else if(!$this->issue_date_from && $this->issue_date_to)
            $report_period = 'Report Period Until '. \Carbon\Carbon::parse($this->issue_date_to)->format('d/m/Y');
        else
            $report_period = 'Report Period From All';
        $titleRows = collect([
            'No.',
            'Risk Type',
            'Policy/End. No.',
            'POLICY NO.',
            'Inception Date',
            'Expiry Date',
            'POI Count',
            'ENDT. Effective Date',
            'Insured Name',
            'Risk Occupation',
            'Risk Description',
            'No. of Insured/Vehicle',
            'Capital City/Province/Sangkat',
            'Type of Client',
            // For Coporate Customer Local
            'TIN Code/Number',
            // For Coporate Customer Abroad
            'Foreign TIN Number (Registration number with their Tax Authority',
            'Name In Khmer',
            'Name in LATIN (Capital Letter)',
            'Country',
            'Company Phone Number',
            'Email Address',
            'Company Full Address',
            //For Individual Customers
            'TID Number/National ID/Passport/Family Book',
            'Name in Khmer',
            'Name in LATIN (Capital Letter)',
            'Sex (Male/Female)',
            'Date of Birth (dd/mm/yy)',
            'National',
            'Nationality',
            'Phone Number',
            'Email Address',
            //Applicable to Property Line
            'Sangkat Code',
            'Construction Class',
            'Risk Code',
            'MD/LOP',
            'Add Perils Covered',
            'FEA Disc %',
            'Voluntary Deductible %',
            //Original Policy
            'Sum Insured',
            'Gross Premium',
            //DS018
            'Handler Code',
            //Direct Sales
            'Business Channel',
            //FA006
            'Account Code',
            //
            'REMARKS',
            'RI Capacity 100%',
            'Status',
            'UW Year',
            'Date of Issue',
            'Accounting Month',
            'Invoice No.',
            'Issued By',
            'Verified By',
            'Approved Reinsurance by',
            'Unearned Premium Reserved',
            'Deferred Commission and Brokerage',
            'Premium Payment',
            'Source of Business'
        ]);

        return [
            [
                'Phillip General Insurance (Cambodia) Plc.'
            ],

            [''],

            [
                'ENDORSEMENT REGISTER'
            ],

            [''],

            [
                $report_period
            ],

            $titleRows->toArray(),

            [''],
        ];
    }

    public function map($policy): array
    {
        if(!is_null($policy->inception_date))
            $policy->inception_date = \Carbon\Carbon::parse($policy->inception_date)->format('d/m/Y');
        if(!is_null($policy->expiry_date))
            $policy->expiry_date = \Carbon\Carbon::parse($policy->expiry_date)->format('d/m/Y');
        if(!is_null($policy->endt_effective_date))
            $policy->endt_effective_date = \Carbon\Carbon::parse($policy->endt_effective_date)->format('d/m/Y');
        if(!is_null($policy->date_of_birth))
            $policy->date_of_birth = \Carbon\Carbon::parse($policy->date_of_birth)->format('d/m/Y');
        if(!is_null($policy->date_of_issue))
            $policy->date_of_issue = \Carbon\Carbon::parse($policy->date_of_issue)->format('d/m/Y');

        if(!is_null($policy->status)){
            if ($policy->status == 'APV')
                $policy->status = 'Approved';
            else if ($policy->status == 'PND')
                $policy->status = 'Pending';
            else if ($policy->status = 'REJ')
                $policy->status = 'Rejected';
        }

        $rows = collect([
            $policy->no,
            $policy->risk_type,
            $policy->end_no,
            $policy->policyno,
            $policy->inception_date,
            $policy->expiry_date,
            $policy->poi_count,
            $policy->endt_effective_date,
            $policy->insured_name,
            isset($policy->risk_occupation) ? $policy->risk_occupation : '',
            isset($policy->risk_description) ? $policy->risk_description : '',
            $policy->no_of_insured,
            $policy->city_province,
            $policy->type_client,
            $policy->tincode,
            $policy->foriegn_tin_no,
            $policy->name_in_khmer,
            $policy->name_in_english,
            $policy->country,
            $policy->company_phone,
            $policy->email_address,
            $policy->full_address,
            $policy->identity_no,
            $policy->name_kh,
            $policy->name_english,
            $policy->sex,
            $policy->date_of_birth,
            $policy->national,
            $policy->nationality,
            $policy->phone_number,
            $policy->email,
            $policy->sangkatcode,
            $policy->construction_class,
            $policy->risk_code,
            $policy->md_lop,
            $policy->add_perils_covered,
            $policy->fea_discount,
            $policy->voluntary_deductible,
            $policy->sum_insured,
            $policy->total_premium < 0 ? '('. number_format(abs($policy->total_premium), 2,'.', ',') .')' : $policy->total_premium,
            $policy->handlercode,
            $policy->sale_channel,
            $policy->accountcode,
            $policy->remark,
            $policy->ri_capacity,
            $policy->status,
            $policy->uw_year,
            $policy->date_of_issue,
            isset($policy->accounting_month) ? $policy->accounting_month : '',
            $policy->invoice_no,
            $policy->issued_by,
            $policy->verified_by,
            $policy->approved_by,
            $policy->unearned_premium_reserved,
            $policy->deferred_commission_and_brokerage,
            $policy->as_premium_payment,
            $policy->source_of_business
        ]);

        return $rows->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'I' => 40,
            'L' => 20,
            'M' => 30,
            'P' => 30,
            'V' => 30,
            'W' => 30,
            'AR' => 40,
            'BA' => 15,
            'BB' => 20,
            'BC' => 20,

        ];
    }

    public function styles(Worksheet $sheet) {
        $rowCount =  count($this->vehicleReportData);
        // Cells borders
        $startRow = 6;
        // An extra row to deal with merging rows/cells
        $endRow = $startRow + $rowCount +1;

        // Global
        $sheet->getStyle('A:BE')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $sheet->getStyle('A1')->getFont()->setSize(12);
        $sheet->getStyle('A2:A5')->getFont()->setSize(10);
        $sheet->getStyle('A1:A5')->getFont()->setBold(true);
        $sheet->getStyle('A1:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $this->conditionalStyles($sheet, $startRow, $endRow);

        // Table column title
        $this->conditionalMergeTitle($sheet, $startRow);
        $this->conditionalStyleTitle($sheet, $startRow);
    }

    private function conditionalStyles($sheet, $startRow, $endRow) {
        $sheet->getStyle('B:BE')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('M')->getAlignment()->setWrapText(true);
        $sheet->getStyle('P')->getAlignment()->setWrapText(true);
        $sheet->getStyle('V')->getAlignment()->setWrapText(true);
        $sheet->getStyle('W')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AR')->getAlignment()->setWrapText(true);
        $sheet->getStyle('BA')->getAlignment()->setWrapText(true);
        $sheet->getStyle('BB')->getAlignment()->setWrapText(true);
        $sheet->getStyle('BC')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$startRow.':BE'.$endRow)->getFont()->setSize(10);
        $sheet->getStyle('A'.$startRow.':BE'.$endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
    }

    private function conditionalMergeTitle($sheet, $startRow){
        // Normal cells
        $sheet->mergeCells('A'.$startRow.':A'.($startRow +1));
        $sheet->mergeCells('B'.$startRow.':B'.($startRow +1));
        $sheet->mergeCells('C'.$startRow.':C'.($startRow +1));
        $sheet->mergeCells('D'.$startRow.':D'.($startRow +1));
        $sheet->mergeCells('E'.$startRow.':E'.($startRow +1));
        $sheet->mergeCells('F'.$startRow.':F'.($startRow +1));
        $sheet->mergeCells('G'.$startRow.':G'.($startRow +1));
        $sheet->mergeCells('H'.$startRow.':H'.($startRow +1));
        $sheet->mergeCells('I'.$startRow.':I'.($startRow +1));
        $sheet->mergeCells('J'.$startRow.':J'.($startRow +1));
        $sheet->mergeCells('K'.$startRow.':K'.($startRow +1));
        $sheet->mergeCells('L'.$startRow.':L'.($startRow +1));
        $sheet->mergeCells('M'.$startRow.':M'.($startRow +1));
        $sheet->mergeCells('N'.$startRow.':N'.($startRow +1));

        // For Corporate Customer-Local Cells
        $sheet->setCellValue('O'.($startRow +1), $sheet->getCell('O'.$startRow));
        $sheet->setCellValue('O'.$startRow, 'For Corporate Customer-Local');

        // For For Corporate Customer-Abroad Cells
        $sheet->setCellValue('P'.($startRow +1), $sheet->getCell('P'.$startRow));
        $sheet->setCellValue('Q'.($startRow +1), $sheet->getCell('Q'.$startRow));
        $sheet->setCellValue('R'.($startRow +1), $sheet->getCell('R'.$startRow));
        $sheet->setCellValue('S'.($startRow +1), $sheet->getCell('S'.$startRow));
        $sheet->setCellValue('T'.($startRow +1), $sheet->getCell('T'.$startRow));
        $sheet->setCellValue('U'.($startRow +1), $sheet->getCell('U'.$startRow));
        $sheet->setCellValue('V'.($startRow +1), $sheet->getCell('V'.$startRow));

        $sheet->setCellValue('P'.$startRow, 'For Corporate Customer-Abroad');
        $sheet->mergeCells('P'.$startRow.':V'.$startRow);

        // For Individual Customer Cells
        $sheet->setCellValue('W'.($startRow +1), $sheet->getCell('W'.$startRow));
        $sheet->setCellValue('X'.($startRow +1), $sheet->getCell('X'.$startRow));
        $sheet->setCellValue('Y'.($startRow +1), $sheet->getCell('Y'.$startRow));
        $sheet->setCellValue('Z'.($startRow +1), $sheet->getCell('Z'.$startRow));
        $sheet->setCellValue('AA'.($startRow +1), $sheet->getCell('AA'.$startRow));
        $sheet->setCellValue('AB'.($startRow +1), $sheet->getCell('AB'.$startRow));
        $sheet->setCellValue('AC'.($startRow +1), $sheet->getCell('AC'.$startRow));
        $sheet->setCellValue('AD'.($startRow +1), $sheet->getCell('AD'.$startRow));
        $sheet->setCellValue('AE'.($startRow +1), $sheet->getCell('AE'.$startRow));

        $sheet->setCellValue('W'.$startRow, 'For Individual Customer');
        $sheet->mergeCells('W'.$startRow.':AE'.$startRow);

        // Applicable to Property Line
        $sheet->setCellValue('AF'.($startRow +1), $sheet->getCell('AF'.$startRow));
        $sheet->setCellValue('AG'.($startRow +1), $sheet->getCell('AG'.$startRow));
        $sheet->setCellValue('AH'.($startRow +1), $sheet->getCell('AH'.$startRow));
        $sheet->setCellValue('AI'.($startRow +1), $sheet->getCell('AI'.$startRow));
        $sheet->setCellValue('AJ'.($startRow +1), $sheet->getCell('AJ'.$startRow));
        $sheet->setCellValue('AK'.($startRow +1), $sheet->getCell('AK'.$startRow));
        $sheet->setCellValue('AL'.($startRow +1), $sheet->getCell('AL'.$startRow));

        $sheet->setCellValue('AF'.$startRow, 'Applicable to Property Line');
        $sheet->mergeCells('AF'.$startRow.':AL'.$startRow);

        // Original Policy Cells
        $sheet->setCellValue('AM'.($startRow +1), $sheet->getCell('AM'.$startRow));
        $sheet->setCellValue('AN'.($startRow +1), $sheet->getCell('AN'.$startRow));

        $sheet->setCellValue('AM'.$startRow, 'Original Policy');
        $sheet->mergeCells('AM'.$startRow.':AN'.$startRow);

        // DS018 Cells
        $sheet->mergeCells('AO'.$startRow.':AO'.($startRow +1));
        $sheet->setCellValue('AO'.($startRow - 1), 'DS018');
        $sheet->getStyle('AO'.($startRow - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ]);

        // Direct Sales Cells
        $sheet->mergeCells('AP'.$startRow.':AP'.($startRow +1));
        $sheet->setCellValue('AP'.($startRow - 1), 'Direct Sales');
        $sheet->getStyle('AP'.($startRow - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ]);

        // FA006 Cells
        $sheet->mergeCells('AQ'.$startRow.':AQ'.($startRow +1));
        $sheet->setCellValue('AQ'.($startRow - 1), 'FA006');
        $sheet->getStyle('AQ'.($startRow - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ]);

        // Normal cells
        $sheet->mergeCells('AR'.$startRow.':AR'.($startRow +1));
        $sheet->mergeCells('AS'.$startRow.':AS'.($startRow +1));
        $sheet->mergeCells('AT'.$startRow.':AT'.($startRow +1));
        $sheet->mergeCells('AU'.$startRow.':AU'.($startRow +1));
        $sheet->mergeCells('AV'.$startRow.':AV'.($startRow +1));
        $sheet->mergeCells('AW'.$startRow.':AW'.($startRow +1));
        $sheet->mergeCells('AX'.$startRow.':AX'.($startRow +1));
        $sheet->mergeCells('AY'.$startRow.':AY'.($startRow +1));
        $sheet->mergeCells('AZ'.$startRow.':AZ'.($startRow +1));
        $sheet->mergeCells('BA'.$startRow.':BA'.($startRow +1));
        $sheet->mergeCells('BB'.$startRow.':BB'.($startRow +1));
        $sheet->mergeCells('BC'.$startRow.':BC'.($startRow +1));
        $sheet->mergeCells('BD'.$startRow.':BD'.($startRow +1));
        $sheet->mergeCells('BE'.$startRow.':BE'.($startRow +1));
    }

    private function conditionalStyleTitle($sheet, $startRow){
        $sheet->getStyle('AO'.($startRow - 1).':AQ'.($startRow - 1))->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':BE'.$startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.($startRow + 1).':BE'.($startRow + 1))->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':BE'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$startRow.':BE'.$startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A'.($startRow+1).':BE'.($startRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.($startRow+1).':BE'.($startRow+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


        $this->conditionalColorTitle($sheet, $startRow);
    }

    private function conditionalColorTitle($sheet, $startRow){
        // All cells before merge
        $sheet->getStyle('A'.$startRow.':BE'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFC000'],
        ]);
        // For Corporate Customer-Local
        $sheet->getStyle('O'.$startRow.':O'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFF2CC'],
        ]);
        // For Corporate Customer-Abroad a9d08e
        $sheet->getStyle('P'.$startRow.':V'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'A9D08E'],
        ]);
        $sheet->getStyle('P'.($startRow + 1).':V'.($startRow+1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'A9D08E'],
        ]);
        // For Individual Customer
        $sheet->getStyle('W'.$startRow.':AE'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'F8CBAD'],
        ]);
        $sheet->getStyle('W'.($startRow + 1).':AE'.($startRow+1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'F8CBAD'],
        ]);
        // Applicable to Property Line
        $sheet->getStyle('AF'.$startRow.':AL'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFFF00'],
        ]);
        $sheet->getStyle('AF'.($startRow + 1).':AL'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFFF00'],
        ]);
        // DS018, Direct Sale, FA006
        $sheet->getStyle('AO'.($startRow - 1).':AQ'.($startRow - 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFFF00'],
        ]);
        // Approved Reinsurance by
        $sheet->getStyle('BA'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFFF00'],
        ]);
        // Sum Insured, Gross Premium
        $sheet->getStyle('AM'.($startRow + 1).':AN'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFC000'],
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_NUMBER,
            'AM' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AN' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
