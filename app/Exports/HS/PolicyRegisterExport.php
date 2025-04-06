<?php

namespace App\Exports\HS;

use App\Models\HS\PolicyReport\HSPolicyRegisterReportV;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
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

class PolicyRegisterExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{

    private $issue_date_from;
    private $issue_date_to;
    private $treaties;
    private $insuredPerosonReportData;

    public function __construct($issue_date_from, $issue_date_to, $treaties)
    {
        $this->issue_date_from = $issue_date_from;
        $this->issue_date_to = $issue_date_to;
        $this->treaties = $treaties;
    }

    public function view(): \Illuminate\Contracts\View\View{
        $data = HSPolicyRegisterReportV::when($this->issue_date_from,function($q){
            $q->whereDate('issued_date','>=',$this->issue_date_from);
        })->when($this->issue_date_to,function($q){
            $q->whereDate('issued_date','<=',$this->issue_date_to);
        });
        return view("pdf.policies.hs.register",['data' => $data]);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->insuredPerosonReportData = $this->getinsuredPerosonReportData()
                    ->map(function($item, $key){
                        $item->no = $key + 1;
                        return $item;
                    });
        return $this->insuredPerosonReportData;
    }

    private function getinsuredPerosonReportData(){
        return HSPolicyRegisterReportV::when($this->issue_date_from,function($q){
            $q->whereDate('issued_date','>=',$this->issue_date_from);
        })->when($this->issue_date_to,function($q){
            $q->whereDate('issued_date','<=',$this->issue_date_to);
        })->get();
        // if($this->issue_date_from && $this->issue_date_to){
        //     $data = HSPolicyRegisterReportV::where('apv_status', 'APV')->whereBetween('issued_date', [$this->issue_date_from, \Carbon\Carbon::parse($this->issue_date_to)->addDays(1)->format('Y-m-d')])->get();
        // }
        // else if($this->issue_date_from && !$this->issue_date_to)
        //     $data = HSPolicyRegisterReportV::where('apv_status', 'APV')->whereDate('issued_date', '>=', $this->issue_date_from)->get();
        // else if(!$this->issue_date_from && $this->issue_date_to)
        //     $data = HSPolicyRegisterReportV::where('apv_status', 'APV')->whereDate('issued_date', '<=', $this->issue_date_to)->get();
        // else
        //     $data = HSPolicyRegisterReportV::where('apv_status', 'APV')->get();
        // return $data;
    }

    public function headings(): array{
        if($this->issue_date_from && $this->issue_date_to)
            $report_period = 'Report Period From '. \Carbon\Carbon::parse($this->issue_date_from)->format('d M Y') .' to '. \Carbon\Carbon::parse($this->issue_date_to)->format('d M Y');
        else if($this->issue_date_from && !$this->issue_date_to)
            $report_period = 'Report Period From '. \Carbon\Carbon::parse($this->issue_date_from)->format('d M Y');
        else if(!$this->issue_date_from && $this->issue_date_to)
            $report_period = 'Report Period Until '. \Carbon\Carbon::parse($this->issue_date_to)->format('d M Y');
        else
            $report_period = 'Report Period From All';
        $titleRows = collect([
            'No.',
            'LOB',
            'Risk Type',
            'Policy/Endorsement No.',
            'Number of Risk',
            'Reference Policy No. (Last Degist)',
            'Inception Date',
            'Expiry Date',
            'Numbers of Days',
            'Endorsement Effective Date',
            'Type of Client',
            'Insured Name',
            'Risk Coverage',
            'Risk Occupation',
            // For Property
            'Risk Occupation Code',
            'Construction Class',
            'MD/LOP',
            'Add Perils Covered',
            'FEA Disc %',
            'Voluntary Deductible %',
            // For Medical & PA
            'Numbers of Insured Persons',
            // For Auto
            'Numbers of Vehicles',
            // For System
            'Location of Risk(Full address)',
            //
            'Tower/ Block of Buildings, e.g. Borey',
            // For System
            'Street No.',
            //
            'Commune Name',
            'Commune Code',
            'Province/ City',
            // For Corporate Customer-Local
            'TIN Code/Number',
            'Company Phone Number',
            'Contact Person',
            'Phone Number',
            'Email Address',
            // For Corporate Customer-Abroad
            'Foreign TIN Number (Registration number with their Tax Authority)',
            'Name in Khmer',
            'Name in LATIN (Capital Letter)',
            'Country',
            'Company Phone Number',
            'Email Address',
            'Company Full Address',
            // For Individual Customer
            'TID Number/National ID/Passport/Family Book',
            'Name in Khmer',
            'Name in LATIN (Capital Letter)',
            'Sex (Male/Female)',
            'Date of Birth (dd/mm/yyyy)',
            'National',
            'Nationality',
            'Phone Number',
            'Email Address',
            //
            'Total Sum Insured (USD)',
            'Gross Written Premium (USD)',
            'Handler Code',
            'Business Channel',
            'Business Code',
            'Source of Referral',
            'Tax and Fee Amount 5.5% (USD)',
            'Net Written Premium (USD)',
            'Commission Rate (%)',
            'Commission Amount (USD)',
            'Withholding Tax Amount (USD)',
            'Commission Amount Due (USD)',
            // CAMBODIA RE COMPULSORY CESSION
            'Share %',
            'Sum Insured Ceded',
            'Premium Ceded',
            'Tax & Fee Amount (5.5%)',
            'RI. Comm.%',
            'RI. Comm. Amount',
            'Net Due',
            // QUOTA SHARE CESSION
            'Share %',
            'Sum Insured Ceded',
            'Premium Ceded',
            'Tax & Fee Amount (5.5%)',
            'RI. Comm.% from Re',
            'RI. Comm. Amount from Re',
            'RI. Comm.% from Hub',
            'RI. Comm. Amount from Hub Risk',
            'RI. Comm. % In System',
            'RI. Comm. Amount In System',
            'Net Due',
            // PGI RETENTION
            'Share %',
            'Sum Insured Retented',
            'Premium Retented',
            'Tax & Fee Amount (5.5%)',
            'Net Retention',
            // OTHER TREATY CESSIONS
            'Treaty Name',
            'Share %',
            'Sum Insured Ceded',
            'Premium Ceded',
            'Tax & Fee Amount (5.5%)',
            'RI. Comm.%',
            'RI. Comm. Amount',
            'Net Due',
            // OUTWARD FACULTATIVE REINSURANCE
            'Reinsurer',
            'Share %',
            'Sum Insured Ceded',
            'Premium Ceded',
            'Tax & Fee Amount (5.5%)',
            'RI. Comm.%',
            'RI. Comm. Amount',
            'Net Due',
            // OUTWARD CO-INSURANCE
            'Co-Insurer',
            'Share %',
            'Sum Insured Ceded',
            'Premium Ceded',
            'Co-Fee + Agency Comm.%',
            'Co-Fee + Agency Comm.Amount',
            'Net Due',
            //
            'Reinsurance Premium Ceded',
            'RI Capacity 100%',
            'Type of Policy',
            'Status',
            'UW Year (Standard: Jan to Dec)',
            'UW Year (Quota Share Cession)',
            'Date of issue',
            'Accounting Month',
            'Invoice No.',
            'Issued by',
            'Approved by',
            'Unearned Premium Reserved',
            'Deferred Commission and Brokerage',
            'Remarks'
        ]);
        return [
            [
                'Phillip General Insurance (Cambodia) Plc.'
            ],

            [''],

            [
                'POLICY REGISTER'
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
        // if(!is_null($policy->inception_date))
        //     $policy->inception_date = \Carbon\Carbon::parse($policy->inception_date)->format('d-M-Y');
        // if(!is_null($policy->expiry_date))
        //     $policy->expiry_date = \Carbon\Carbon::parse($policy->expiry_date)->format('d-M-Y');
        // if(!is_null($policy->endorsement_effect_date))
        //     $policy->endorsement_effect_date = \Carbon\Carbon::parse($policy->endorsement_effect_date)->format('d M Y');
        // if(!is_null($policy->ic_dob))
        //     $policy->ic_dob = \Carbon\Carbon::parse($policy->ic_dob)->format('d M Y');
        // if(!is_null($policy->issued_date))
        //     $policy->issued_date = \Carbon\Carbon::parse($policy->issued_date)->format('d-M-Y');

        if(!is_null($policy->reinsure_other_treaty_name))
            $policy->reinsure_other_treaty_name = $this->treaties[$policy->reinsure_other_treaty_name];

        $rows = collect([
            $policy->no,
            $policy->lob,
            $policy->risk_type,
            $policy->document_no,
            $policy->number_of_risk,
            $policy->ref_policy_no,
            $policy->inception_date,
            $policy->expiry_date,
            $policy->number_of_day,
            $policy->endorsement_effect_date,
            $policy->client_type,
            $policy->insured_name,
            $policy->risks_coverage,
            $policy->risk_occupation,
            // For Property
            isset($policy->risk_occupation_code) ? $policy->risk_occupation_code : '',
            isset($policy->construction_class) ? $policy->construction_class : '',
            isset($policy->md_lop) ? $policy->md_lop : '',
            isset($policy->add_perils_covered) ? $policy->add_perils_covered : '',
            isset($policy->fea_disc) ? $policy->fea_disc : '',
            isset($policy->voluntary_deductible) ? $policy->voluntary_deductible : '',
            // For Medical & PA
            isset($policy->number_insured_persons) ? $policy->number_insured_persons : '',
            // For Auto
            $policy->number_of_vehicle,
            // For system
            $policy->full_address_en,
            //
            isset($policy->tower_block_of_building) ? $policy->tower_block_of_building : '',
            $policy->street_no,
            $policy->commune_name,
            $policy->commune_code,
            $policy->city,
            // For Corporate Customer-Local
            $policy->cl_tin_code,
            $policy->cl_company_phone,
            $policy->cl_contact_person,
            $policy->cl_phone_number,
            $policy->cl_email_address,
            // For Corporate Customer-Abroad
            $policy->ca_tin_code,
            $policy->ca_name_kh,
            $policy->ca_name_en,
            $policy->ca_country,
            $policy->ca_company_phone,
            $policy->ca_email_address,
            $policy->ca_full_address,
            // For Individual Customer
            $policy->ic_national_id,
            $policy->ic_name_kh,
            $policy->ic_name_en,
            $policy->ic_gender,
            $policy->ic_dob,
            $policy->ic_national,
            $policy->ic_nationality,
            $policy->ic_phone_number,
            $policy->ic_email,
            //
            $policy->total_sum_insured_usd < 0 ? '('. number_format(abs($policy->total_sum_insured_usd), 2,'.', ',') .')' : $policy->total_sum_insured_usd,
            $policy->gross_writen_premium < 0 ? '('. number_format(abs($policy->gross_writen_premium), 2,'.', ',') .')' : $policy->gross_writen_premium,
            $policy->handler_code,
            $policy->sale_channel,
            $policy->business_code,
            $policy->source_of_referal,
            $policy->tax_and_fee_amount_usd < 0 ? '('. number_format(abs($policy->tax_and_fee_amount_usd), 2,'.', ',') .')' : $policy->tax_and_fee_amount_usd,
            $policy->net_writen_premuium < 0 ? '('. number_format(abs($policy->net_writen_premuium), 2,'.', ',') .')' : $policy->net_writen_premuium,
            $policy->commission_rate * 100,
            $policy->commission_amt < 0 ? '('. number_format(abs($policy->commission_amt), 2,'.', ',') .')' : $policy->commission_amt,
            $policy->with_holding_amt < 0 ? '('. number_format(abs($policy->with_holding_amt), 2,'.', ',') .')' : $policy->with_holding_amt,
            $policy->commission_amt_due < 0 ? '('. number_format(abs($policy->commission_amt_due), 2,'.', ',') .')' : $policy->commission_amt_due,
            // CAMBODIA RE COMPULSORY CESSION
            $policy->cambodia_re_share * 100,
            $policy->cambodia_re_sum_insured_ceded < 0 ? '('. number_format(abs($policy->cambodia_re_sum_insured_ceded), 2,'.', ',') .')' : $policy->cambodia_re_sum_insured_ceded,
            $policy->cambodia_re_premium_ceded < 0 ? '('. number_format(abs($policy->cambodia_re_premium_ceded), 2,'.', ',') .')' : $policy->cambodia_re_premium_ceded,
            $policy->cambodia_re_tax_fee_amounts < 0 ? '('. number_format(abs($policy->cambodia_re_tax_fee_amounts), 2,'.', ',') .')' : $policy->cambodia_re_tax_fee_amounts,
            $policy->cambodia_re_ri_commissions * 100,
            $policy->cambodia_re_ri_commission_amount < 0 ? '('. number_format(abs($policy->cambodia_re_ri_commission_amount), 2,'.', ',') .')' : $policy->cambodia_re_ri_commission_amount,
            $policy->cambodia_re_net_due < 0 ? '('. number_format(abs($policy->cambodia_re_net_due), 2,'.', ',') .')' : $policy->cambodia_re_net_due,
            // QUOTA SHARE CESSION
            $policy->quota_share_share * 100,
            $policy->quota_share_sum_insured_ceded < 0 ? '('. number_format(abs($policy->quota_share_sum_insured_ceded), 2,'.', ',') .')' : $policy->quota_share_sum_insured_ceded,
            $policy->quota_share_total_premium_ceded < 0 ? '('. number_format(abs($policy->quota_share_total_premium_ceded), 2,'.', ',') .')' : $policy->quota_share_total_premium_ceded,
            $policy->quota_share_tax_fee_amount < 0 ? '('. number_format(abs($policy->quota_share_tax_fee_amount), 2,'.', ',') .')' : $policy->quota_share_tax_fee_amount,
            null,
            null,
            null,
            null,
            $policy->quota_share_ri_commission * 100,
            $policy->quota_share_ri_commission_amount < 0 ? '('. number_format(abs($policy->quota_share_ri_commission_amount), 2,'.', ',') .')' : $policy->quota_share_ri_commission_amount,
            $policy->quota_share_net_due < 0 ? '('. number_format(abs($policy->quota_share_net_due), 2,'.', ',') .')' : $policy->quota_share_net_due,
            // PGI RETENTION
            $policy->pgi_retention_share * 100,
            $policy->pgi_retention_sum_insured_ceded < 0 ? '('. number_format(abs($policy->pgi_retention_sum_insured_ceded), 2,'.', ',') .')' : $policy->pgi_retention_sum_insured_ceded,
            $policy->pgi_retention_premium_ceded < 0 ? '('. number_format(abs($policy->pgi_retention_premium_ceded), 2,'.', ',') .')' : $policy->pgi_retention_premium_ceded,
            $policy->pgi_retention_tax_fee_amount < 0 ? '('. number_format(abs($policy->pgi_retention_tax_fee_amount), 2,'.', ',') .')' : $policy->pgi_retention_tax_fee_amount,
            $policy->pgi_retention_net_due < 0 ? '('. number_format(abs($policy->pgi_retention_net_due), 2,'.', ',') .')' : $policy->pgi_retention_net_due,
            // OTHER TREATY CESSIONS
            $policy->reinsure_other_treaty_name,
            $policy->reinsure_other_share * 100,
            $policy->reinsure_other_sum_insured_ceded < 0 ? '('. number_format(abs($policy->reinsure_other_sum_insured_ceded), 2,'.', ',') .')' : $policy->reinsure_other_sum_insured_ceded,
            $policy->reinsure_other_premium_ceded < 0 ? '('. number_format(abs($policy->reinsure_other_premium_ceded), 2,'.', ',') .')' : $policy->reinsure_other_premium_ceded,
            $policy->reinsure_other_tax_fee_amt < 0 ? '('. number_format(abs($policy->reinsure_other_tax_fee_amt), 2,'.', ',') .')' : $policy->reinsure_other_tax_fee_amt,
            $policy->reinsure_other_ri_commission * 100,
            $policy->reinsure_other_ri_commission_amount < 0 ? '('. number_format(abs($policy->reinsure_other_ri_commission_amount), 2,'.', ',') .')' : $policy->reinsure_other_ri_commission_amount,
            $policy->reinsure_other_net_due < 0 ? '('. number_format(abs($policy->reinsure_other_net_due), 2,'.', ',') .')' : $policy->reinsure_other_net_due,
            // OUTWARD FACULTATIVE REINSURANCE
            $policy->facul_treaty_name,
            $policy->facul_share * 100,
            $policy->facul_sum_insured_ceded < 0 ? '('. number_format(abs($policy->facul_sum_insured_ceded), 2,'.', ',') .')' : $policy->facul_sum_insured_ceded,
            $policy->facul_premium_ceded < 0 ? '('. number_format(abs($policy->facul_premium_ceded), 2,'.', ',') .')' : $policy->facul_premium_ceded,
            $policy->facul_tax_fee_amounts < 0 ? '('. number_format(abs($policy->facul_tax_fee_amounts), 2,'.', ',') .')' : $policy->facul_tax_fee_amounts,
            $policy->facul_ri_commissions * 100,
            $policy->facul_ri_commission_amount < 0 ? '('. number_format(abs($policy->facul_ri_commission_amount), 2,'.', ',') .')' : $policy->facul_ri_commission_amount,
            $policy->facul_net_due < 0 ? '('. number_format(abs($policy->facul_net_due), 2,'.', ',') .')' : $policy->facul_net_due,
            // OUTWARD CO-INSURANCE
            $policy->outward_treaty_name,
            $policy->outward_share * 100,
            $policy->outward_sum_insured_ceded < 0 ? '('. number_format(abs($policy->outward_sum_insured_ceded), 2,'.', ',') .')' : $policy->outward_sum_insured_ceded,
            $policy->outward_premium_ceded < 0 ? '('. number_format(abs($policy->outward_premium_ceded), 2,'.', ',') .')' : $policy->outward_premium_ceded,
            $policy->outward_ri_commissions * 100,
            $policy->outward_ri_commission_amount < 0 ? '('. number_format(abs($policy->outward_ri_commission_amount), 2,'.', ',') .')' : $policy->outward_ri_commission_amount,
            $policy->outward_net_due < 0 ? '('. number_format(abs($policy->outward_net_due), 2,'.', ',') .')' : $policy->outward_net_due,
            //
            $policy->reinsurance_premium_ceded < 0 ? '('. number_format(abs($policy->reinsurance_premium_ceded), 2,'.', ',') .')' : $policy->reinsurance_premium_ceded,
            $policy->ri_capacity,
            $policy->type_of_policy,
            $policy->status,
            $policy->uw_year_standard,
            $policy->uw_year_quota_share,
            $policy->issued_date,
            $policy->accounting_month,
            $policy->invoice_no,
            $policy->issued_by,
            $policy->approved_by,
            $policy->unearned_premium_reserved,
            $policy->deferred_commission_and_brokerage,
            strip_tags($policy->remark)
        ]);
        return $rows->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'J' => 15,
            'L' => 30,
            'W' => 20,
            'X' => 20,
            'AG' => 30,
            'AH' => 30,
            'AI' => 30,
            'AJ' => 30,
            'AM' => 30,
            'AN' => 30,
            'AO' => 30,
            'AP' => 30,
            'AQ' => 30,
            'AW' => 30,
            'DM' => 30,
        ];
    }

    public function styles(Worksheet $sheet) {
        $rowCount =  count($this->insuredPerosonReportData);
        // Cells borders
        $startRow = 6;
        // Two extra rows to deal with merging rows/cells and total row
        $endRow = $startRow + $rowCount + 2;

        // Global
        $sheet->getStyle('A:DQ')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
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

        //Table Total
        $this->conditionalTotalRow($sheet, $endRow);
    }

    private function conditionalStyles($sheet, $startRow, $endRow) {
        $sheet->getStyle('B:DG')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('L')->getAlignment()->setWrapText(true);
        $sheet->getStyle('W')->getAlignment()->setWrapText(true);
        $sheet->getStyle('X')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AG:AJ')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AM:AQ')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AW')->getAlignment()->setWrapText(true);
        $sheet->getStyle('DQ')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$startRow.':DQ'.$endRow)->getFont()->setSize(10);
        $sheet->getStyle('A'.$startRow.':DQ'.$endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
    }

    private function conditionalTotalRow($sheet, $endRow){
        $sheet->setCellValue('A'.$endRow, 'TOTAL');
        $sheet->getStyle('B'.$endRow.':DM'.$endRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('AX'.$endRow, $this->sumTotalCol('total_sum_insured_usd'));
        $sheet->setCellValue('AY'.$endRow, $this->sumTotalCol('gross_writen_premium'));
        $sheet->setCellValue('AY'.$endRow, $this->sumTotalCol('gross_writen_premium'));
        $sheet->setCellValue('BD'.$endRow, $this->sumTotalCol('tax_and_fee_amount_usd'));
        $sheet->setCellValue('BE'.$endRow, $this->sumTotalCol('net_writen_premuium'));
        $sheet->setCellValue('BG'.$endRow, $this->sumTotalCol('commission_amt'));
        $sheet->setCellValue('BH'.$endRow, $this->sumTotalCol('with_holding_amt'));
        $sheet->setCellValue('BI'.$endRow, $this->sumTotalCol('commission_amt_due'));

        // CAMBODIA RE COMPULSORY CESSION
        $sheet->setCellValue('BK'.$endRow, $this->sumTotalCol('cambodia_re_sum_insured_ceded'));
        $sheet->setCellValue('BL'.$endRow, $this->sumTotalCol('cambodia_re_premium_ceded'));
        $sheet->setCellValue('BM'.$endRow, $this->sumTotalCol('cambodia_re_tax_fee_amounts'));
        $sheet->setCellValue('BO'.$endRow, $this->sumTotalCol('cambodia_re_ri_commission_amount'));
        $sheet->setCellValue('BP'.$endRow, $this->sumTotalCol('cambodia_re_net_due'));

        // Quota Share
        $sheet->setCellValue('BR'.$endRow, $this->sumTotalCol('quota_share_sum_insured_ceded'));
        $sheet->setCellValue('BS'.$endRow, $this->sumTotalCol('quota_share_total_premium_ceded'));
        $sheet->setCellValue('BT'.$endRow, $this->sumTotalCol('quota_share_tax_fee_amount'));
        $sheet->setCellValue('BZ'.$endRow, $this->sumTotalCol('quota_share_ri_commission_amount'));
        $sheet->setCellValue('CA'.$endRow, $this->sumTotalCol('quota_share_net_due'));

        // PGI Retention
        $sheet->setCellValue('CC'.$endRow, $this->sumTotalCol('pgi_retention_sum_insured_ceded'));
        $sheet->setCellValue('CD'.$endRow, $this->sumTotalCol('pgi_retention_premium_ceded'));
        $sheet->setCellValue('CE'.$endRow, $this->sumTotalCol('pgi_retention_tax_fee_amount'));
        $sheet->setCellValue('CF'.$endRow, $this->sumTotalCol('pgi_retention_net_due'));

        // OTHER TREATY CESSIONS
        $sheet->setCellValue('CI'.$endRow, $this->sumTotalCol('reinsure_other_sum_insured_ceded'));
        $sheet->setCellValue('CJ'.$endRow, $this->sumTotalCol('reinsure_other_premium_ceded'));
        $sheet->setCellValue('CK'.$endRow, $this->sumTotalCol('reinsure_other_tax_fee_amt'));
        $sheet->setCellValue('CM'.$endRow, $this->sumTotalCol('reinsure_other_ri_commission_amount'));
        $sheet->setCellValue('CN'.$endRow, $this->sumTotalCol('reinsure_other_net_due'));

        // OUTWARD FACULTATIVE REINSURANCE
        $sheet->setCellValue('CQ'.$endRow, $this->sumTotalCol('facul_sum_insured_ceded'));
        $sheet->setCellValue('CR'.$endRow, $this->sumTotalCol('facul_premium_ceded'));
        $sheet->setCellValue('CS'.$endRow, $this->sumTotalCol('facul_tax_fee_amounts'));
        $sheet->setCellValue('CU'.$endRow, $this->sumTotalCol('facul_ri_commission_amount'));
        $sheet->setCellValue('CV'.$endRow, $this->sumTotalCol('facul_net_due'));

        // OUTWARD CO-INSURANCE
        $sheet->setCellValue('CY'.$endRow, $this->sumTotalCol('outward_sum_insured_ceded'));
        $sheet->setCellValue('CZ'.$endRow, $this->sumTotalCol('outward_premium_ceded'));
        $sheet->setCellValue('DB'.$endRow, $this->sumTotalCol('outward_ri_commission_amount'));
        $sheet->setCellValue('DC'.$endRow, $this->sumTotalCol('outward_net_due'));

        //
        $sheet->setCellValue('DD'.$endRow, $this->sumTotalCol('reinsurance_premium_ceded'));

        $sheet->getStyle('A'.$endRow.':DM'.$endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A'.$endRow.':DM'.$endRow)->getFont()->setSize(10)->setBold(true);
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

        // For Property
        $sheet->setCellValue('O'.($startRow +1), $sheet->getCell('O'.$startRow));
        $sheet->setCellValue('P'.($startRow +1), $sheet->getCell('P'.$startRow));
        $sheet->setCellValue('Q'.($startRow +1), $sheet->getCell('Q'.$startRow));
        $sheet->setCellValue('R'.($startRow +1), $sheet->getCell('R'.$startRow));
        $sheet->setCellValue('S'.($startRow +1), $sheet->getCell('S'.$startRow));
        $sheet->setCellValue('T'.($startRow +1), $sheet->getCell('T'.$startRow));
        $sheet->setCellValue('O'.$startRow, 'For Property');
        $sheet->mergeCells('O'.$startRow.':T'.$startRow);

        // For Medical & PA
        $sheet->setCellValue('U'.($startRow +1), $sheet->getCell('U'.$startRow));
        $sheet->setCellValue('U'.$startRow, 'For Medical & PA');

        // For Auto
        $sheet->setCellValue('V'.($startRow +1), $sheet->getCell('V'.$startRow));
        $sheet->setCellValue('V'.$startRow, 'For Auto');

        // For System
        $sheet->mergeCells('W'.$startRow.':W'.($startRow +1));
        $sheet->setCellValue('W'.($startRow - 1), 'For System');

        //
        $sheet->mergeCells('X'.$startRow.':X'.($startRow +1));

        // For System
        $sheet->mergeCells('Y'.$startRow.':Y'.($startRow +1));
        $sheet->setCellValue('Y'.($startRow - 1), 'For System');

        $sheet->mergeCells('Z'.$startRow.':Z'.($startRow +1));
        $sheet->mergeCells('AA'.$startRow.':AA'.($startRow +1));
        $sheet->mergeCells('AB'.$startRow.':AB'.($startRow +1));

        // For Corporate Customer-Local
        $sheet->setCellValue('AC'.($startRow +1), $sheet->getCell('AC'.$startRow));
        $sheet->setCellValue('AD'.($startRow +1), $sheet->getCell('AD'.$startRow));
        $sheet->setCellValue('AE'.($startRow +1), $sheet->getCell('AE'.$startRow));
        $sheet->setCellValue('AF'.($startRow +1), $sheet->getCell('AF'.$startRow));
        $sheet->setCellValue('AG'.($startRow +1), $sheet->getCell('AG'.$startRow));
        $sheet->setCellValue('AC'.$startRow, 'For Corporate Customer-Local');
        $sheet->mergeCells('AC'.$startRow.':AG'.$startRow);

        // For Corporate Customer-Abroad
        $sheet->setCellValue('AH'.($startRow +1), $sheet->getCell('AH'.$startRow));
        $sheet->setCellValue('AI'.($startRow +1), $sheet->getCell('AI'.$startRow));
        $sheet->setCellValue('AJ'.($startRow +1), $sheet->getCell('AJ'.$startRow));
        $sheet->setCellValue('AK'.($startRow +1), $sheet->getCell('AK'.$startRow));
        $sheet->setCellValue('AL'.($startRow +1), $sheet->getCell('AL'.$startRow));
        $sheet->setCellValue('AM'.($startRow +1), $sheet->getCell('AM'.$startRow));
        $sheet->setCellValue('AN'.($startRow +1), $sheet->getCell('AN'.$startRow));
        $sheet->setCellValue('AH'.$startRow, 'For Corporate Customer-Abroad');
        $sheet->mergeCells('AH'.$startRow.':AN'.$startRow);

        // For Individual Customer
        $sheet->setCellValue('AO'.($startRow +1), $sheet->getCell('AO'.$startRow));
        $sheet->setCellValue('AP'.($startRow +1), $sheet->getCell('AP'.$startRow));
        $sheet->setCellValue('AQ'.($startRow +1), $sheet->getCell('AQ'.$startRow));
        $sheet->setCellValue('AR'.($startRow +1), $sheet->getCell('AR'.$startRow));
        $sheet->setCellValue('AS'.($startRow +1), $sheet->getCell('AS'.$startRow));
        $sheet->setCellValue('AT'.($startRow +1), $sheet->getCell('AT'.$startRow));
        $sheet->setCellValue('AU'.($startRow +1), $sheet->getCell('AU'.$startRow));
        $sheet->setCellValue('AV'.($startRow +1), $sheet->getCell('AV'.$startRow));
        $sheet->setCellValue('AW'.($startRow +1), $sheet->getCell('AW'.$startRow));
        $sheet->setCellValue('AO'.$startRow, 'For Individual Customer');
        $sheet->mergeCells('AO'.$startRow.':AW'.$startRow);

        //
        $sheet->mergeCells('AX'.$startRow.':AX'.($startRow +1));
        $sheet->mergeCells('AY'.$startRow.':AY'.($startRow +1));
        $sheet->mergeCells('AZ'.$startRow.':AZ'.($startRow +1));
        $sheet->mergeCells('BA'.$startRow.':BA'.($startRow +1));
        $sheet->mergeCells('BB'.$startRow.':BB'.($startRow +1));
        $sheet->mergeCells('BC'.$startRow.':BC'.($startRow +1));
        $sheet->mergeCells('BD'.$startRow.':BD'.($startRow +1));
        $sheet->mergeCells('BE'.$startRow.':BE'.($startRow +1));
        $sheet->mergeCells('BF'.$startRow.':BF'.($startRow +1));
        $sheet->mergeCells('BG'.$startRow.':BG'.($startRow +1));
        $sheet->mergeCells('BH'.$startRow.':BH'.($startRow +1));
        $sheet->mergeCells('BI'.$startRow.':BI'.($startRow +1));

        // CAMBODIA RE COMPULSORY CESSION
        $sheet->setCellValue('BJ'.($startRow +1), $sheet->getCell('BJ'.$startRow));
        $sheet->setCellValue('BK'.($startRow +1), $sheet->getCell('BK'.$startRow));
        $sheet->setCellValue('BL'.($startRow +1), $sheet->getCell('BL'.$startRow));
        $sheet->setCellValue('BM'.($startRow +1), $sheet->getCell('BM'.$startRow));
        $sheet->setCellValue('BN'.($startRow +1), $sheet->getCell('BN'.$startRow));
        $sheet->setCellValue('BO'.($startRow +1), $sheet->getCell('BO'.$startRow));
        $sheet->setCellValue('BP'.($startRow +1), $sheet->getCell('BP'.$startRow));
        $sheet->setCellValue('BJ'.$startRow, 'CAMBODIA RE COMPULSORY CESSION');
        $sheet->mergeCells('BJ'.$startRow.':BP'.$startRow);

        // QUOTA SHARE CESSION
        $sheet->setCellValue('BQ'.($startRow +1), $sheet->getCell('BQ'.$startRow));
        $sheet->setCellValue('BR'.($startRow +1), $sheet->getCell('BR'.$startRow));
        $sheet->setCellValue('BS'.($startRow +1), $sheet->getCell('BS'.$startRow));
        $sheet->setCellValue('BT'.($startRow +1), $sheet->getCell('BT'.$startRow));
        $sheet->setCellValue('BU'.($startRow +1), $sheet->getCell('BU'.$startRow));
        $sheet->setCellValue('BV'.($startRow +1), $sheet->getCell('BV'.$startRow));
        $sheet->setCellValue('BW'.($startRow +1), $sheet->getCell('BW'.$startRow));
        $sheet->setCellValue('BX'.($startRow +1), $sheet->getCell('BX'.$startRow));
        $sheet->setCellValue('BY'.($startRow +1), $sheet->getCell('BY'.$startRow));
        $sheet->setCellValue('BZ'.($startRow +1), $sheet->getCell('BZ'.$startRow));
        $sheet->setCellValue('CA'.($startRow +1), $sheet->getCell('CA'.$startRow));
        $sheet->setCellValue('BQ'.$startRow, 'QUOTA SHARE CESSION');
        $sheet->mergeCells('BQ'.$startRow.':CA'.$startRow);

        // PGI RETENTION
        $sheet->setCellValue('CB'.($startRow +1), $sheet->getCell('CB'.$startRow));
        $sheet->setCellValue('CC'.($startRow +1), $sheet->getCell('CC'.$startRow));
        $sheet->setCellValue('CD'.($startRow +1), $sheet->getCell('CD'.$startRow));
        $sheet->setCellValue('CE'.($startRow +1), $sheet->getCell('CE'.$startRow));
        $sheet->setCellValue('CF'.($startRow +1), $sheet->getCell('CF'.$startRow));
        $sheet->setCellValue('CB'.$startRow, 'PGI RETENTION');
        $sheet->mergeCells('CB'.$startRow.':CF'.$startRow);

        // OTHER TREATY CESSIONS
        $sheet->setCellValue('CG'.($startRow +1), $sheet->getCell('CG'.$startRow));
        $sheet->setCellValue('CH'.($startRow +1), $sheet->getCell('CH'.$startRow));
        $sheet->setCellValue('CI'.($startRow +1), $sheet->getCell('CE'.$startRow));
        $sheet->setCellValue('CJ'.($startRow +1), $sheet->getCell('CJ'.$startRow));
        $sheet->setCellValue('CK'.($startRow +1), $sheet->getCell('CK'.$startRow));
        $sheet->setCellValue('CL'.($startRow +1), $sheet->getCell('CL'.$startRow));
        $sheet->setCellValue('CM'.($startRow +1), $sheet->getCell('CM'.$startRow));
        $sheet->setCellValue('CN'.($startRow +1), $sheet->getCell('CN'.$startRow));
        $sheet->setCellValue('CG'.$startRow, 'OTHER TREATY CESSIONS');
        $sheet->mergeCells('CG'.$startRow.':CN'.$startRow);

        // OUTWARD FACULTATIVE REINSURANCE
        $sheet->setCellValue('CO'.($startRow +1), $sheet->getCell('CO'.$startRow));
        $sheet->setCellValue('CP'.($startRow +1), $sheet->getCell('CP'.$startRow));
        $sheet->setCellValue('CQ'.($startRow +1), $sheet->getCell('CQ'.$startRow));
        $sheet->setCellValue('CR'.($startRow +1), $sheet->getCell('CR'.$startRow));
        $sheet->setCellValue('CS'.($startRow +1), $sheet->getCell('CS'.$startRow));
        $sheet->setCellValue('CT'.($startRow +1), $sheet->getCell('CT'.$startRow));
        $sheet->setCellValue('CU'.($startRow +1), $sheet->getCell('CU'.$startRow));
        $sheet->setCellValue('CV'.($startRow +1), $sheet->getCell('CV'.$startRow));
        $sheet->setCellValue('CO'.$startRow, 'OUTWARD FACULTATIVE REINSURANCE');
        $sheet->mergeCells('CO'.$startRow.':CV'.$startRow);

        // OUTWARD CO-INSURANCE
        $sheet->setCellValue('CW'.($startRow +1), $sheet->getCell('CW'.$startRow));
        $sheet->setCellValue('CX'.($startRow +1), $sheet->getCell('CX'.$startRow));
        $sheet->setCellValue('CY'.($startRow +1), $sheet->getCell('CY'.$startRow));
        $sheet->setCellValue('CZ'.($startRow +1), $sheet->getCell('CZ'.$startRow));
        $sheet->setCellValue('DA'.($startRow +1), $sheet->getCell('DA'.$startRow));
        $sheet->setCellValue('DB'.($startRow +1), $sheet->getCell('DB'.$startRow));
        $sheet->setCellValue('DC'.($startRow +1), $sheet->getCell('DC'.$startRow));
        $sheet->setCellValue('CW'.$startRow, 'OUTWARD CO-INSURANCE');
        $sheet->mergeCells('CW'.$startRow.':DC'.$startRow);

        //
        
        $sheet->mergeCells('DD'.$startRow.':DD'.($startRow +1));
        $sheet->mergeCells('DE'.$startRow.':DE'.($startRow +1));
        $sheet->mergeCells('DF'.$startRow.':DF'.($startRow +1));
        $sheet->mergeCells('DG'.$startRow.':DG'.($startRow +1));
        $sheet->mergeCells('DH'.$startRow.':DH'.($startRow +1));
        $sheet->mergeCells('DI'.$startRow.':DI'.($startRow +1));
        $sheet->mergeCells('DJ'.$startRow.':DJ'.($startRow +1));
        $sheet->mergeCells('DK'.$startRow.':DK'.($startRow +1));
        $sheet->mergeCells('DL'.$startRow.':DL'.($startRow +1));
        $sheet->mergeCells('DM'.$startRow.':DM'.($startRow +1));
        $sheet->mergeCells('DN'.$startRow.':DN'.($startRow +1));
        $sheet->mergeCells('DO'.$startRow.':DO'.($startRow +1));
        $sheet->mergeCells('DP'.$startRow.':DP'.($startRow +1));
        $sheet->mergeCells('DQ'.$startRow.':DQ'.($startRow +1));
    }

    private function conditionalStyleTitle($sheet, $startRow){
        $sheet->getStyle('A'.($startRow - 1).':DQ'.($startRow - 1))->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':DQ'.$startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.($startRow + 1).':DQ'.($startRow + 1))->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':DQ'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$startRow.':DQ'.$startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A'.($startRow+1).':DQ'.($startRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.($startRow+1).':DQ'.($startRow+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $this->conditionalColorTitle($sheet, $startRow);
    }

    private function conditionalColorTitle($sheet, $startRow){
        // All cells before merge
        $sheet->getStyle('A'.$startRow.':DQ'.($startRow+1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFC000'],
        ]);
        // For Property
        $sheet->getStyle('O'.$startRow.':T'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFFF00'],
        ]);
        // For Corporate Customer-Local
        $sheet->getStyle('AC'.$startRow.':AG'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFF2CC'],
        ]);
        // For Corporate Customer-Abroad
        $sheet->getStyle('AH'.$startRow.':AN'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'A9D08E'],
        ]);
        // For Individual Customer
        $sheet->getStyle('AO'.$startRow.':AW'.($startRow + 1))->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'F8CBAD'],
        ]);
    }

    private function sumTotalCol($column_name){
        return $this->insuredPerosonReportData->sum($column_name);
    }

    public function columnFormats(): array {
        return [
            'G' => 'dd-mmm-yyyy',
            'H' => 'dd-mmm-yyyy',
            'F' => NumberFormat::FORMAT_TEXT,
            'AC' => NumberFormat::FORMAT_TEXT,
            'AD' => NumberFormat::FORMAT_TEXT,
            'AF' => NumberFormat::FORMAT_TEXT,
            'AG' => NumberFormat::FORMAT_TEXT,
            'AH' => NumberFormat::FORMAT_NUMBER,
            'AO' => NumberFormat::FORMAT_NUMBER,
            'AL' => NumberFormat::FORMAT_TEXT,
            'AM' => NumberFormat::FORMAT_TEXT,
            'AW' => NumberFormat::FORMAT_TEXT,
            'AX' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AY' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AZ' => NumberFormat::FORMAT_TEXT,
            'BD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BE' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BF' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BG' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BH' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BI' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BJ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BK' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BL' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BM' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BN' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BO' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BP' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BQ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BR' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BS' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BT' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BU' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BV' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BW' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BX' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BY' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'BZ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CE' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CF' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CG' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CH' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CI' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CJ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CL' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CM' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CN' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CO' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CP' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CQ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CR' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CT' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CU' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CV' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CW' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CX' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CY' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'CZ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'DA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'DJ' => 'yyyy-mm-dd'
        ];
    }
}
