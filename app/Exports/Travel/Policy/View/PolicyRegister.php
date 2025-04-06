<?php

namespace App\Exports\Travel\Policy\View;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PolicyRegister implements FromView, ShouldAutoSize, WithStartRow, WithStyles,WithColumnFormatting
{
    private $issue_date_from, $issue_date_to, $treaties, $totalRecord, $report;
    public function __construct($fromDate, $toDate, $treaties)
    {
        $this->issue_date_from = $fromDate;
        $this->issue_date_to = $toDate;
        $this->treaties = $treaties;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view("");
    }
    public function startRow(): int
    {
        return 9;
    }

    private function reportPeriod()
    {
        return $this->issue_date_from && $this->issue_date_to ? 'Report from ' . date('d F Y', strtotime($this->issue_date_from)) . ' to ' . date('d F Y', strtotime($this->issue_date_to)) : ($this->issue_date_from ? 'Report from ' . date('d F Y', strtotime($this->issue_date_from)) : ($this->issue_date_to ? 'Report from beginning to ' . date('d F Y', strtotime($this->issue_date_to)) : 'Report from all period'));
    }
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('A7:DS8')->getFont()->setBold(true);
        $sheet->getStyle('A1:DS' . $this->totalRecord)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '#000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A7:DS8')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);
        foreach (['A7:N8', 'U7:U8', 'W7:AB8', 'AX7:DS8'] as $range) {
            $sheet->getStyle($range)->getFill()->applyFromArray([
                'fillType' => 'solid',
                'rotation' => 0,
                'color' => ['rgb' => 'FFC000'],
            ]);
        }

        $sheet->getStyle('O7:T8')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFFF00'],
        ]);
        $sheet->getStyle('V7:V8')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => '92D050'],
        ]);
        $sheet->getStyle('AH7:AN8')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'A9D08E'],
        ]);
        $sheet->getStyle('AC7:AG8')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'FFF2CC'],
        ]);
        $sheet->getStyle('AO7:AW8')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'F8CBAD'],
        ]);
        $sheet->getStyle('BW8:BZ8')->applyFromArray([
            'fill' => [
                'fillType' => 'solid',
                'rotation' => 0,
                'color' => ['rgb' => '4472C4'],
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);
        foreach (['BK9:BK'.$this->totalRecord] as $range) {
            $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        

        $endRow = $this->totalRecord;

        $sheet->setCellValue('A' . $endRow, 'TOTAL');
        $sheet->getStyle('B' . $endRow . ':DM' . $endRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('AX' . $endRow, $this->sumTotalCol('total_sum_insured_usd'));
        $sheet->setCellValue('AY' . $endRow, $this->sumTotalCol('gross_writen_premium'));
        $sheet->setCellValue('AY' . $endRow, $this->sumTotalCol('gross_writen_premium'));
        $sheet->setCellValue('BF' . $endRow, $this->sumTotalCol('tax_and_fee_amount_usd'));
        $sheet->setCellValue('BG' . $endRow, $this->sumTotalCol('net_writen_premuium'));
        $sheet->setCellValue('BI' . $endRow, $this->sumTotalCol('commission_amt'));
        $sheet->setCellValue('BJ' . $endRow, $this->sumTotalCol('with_holding_amt'));
        $sheet->setCellValue('BK' . $endRow, $this->sumTotalCol('commission_amt_due'));

        // CAMBODIA RE COMPULSORY CESSION
        $sheet->setCellValue('BM' . $endRow, $this->sumTotalCol('cambodia_re_sum_insured_ceded'));
        $sheet->setCellValue('BN' . $endRow, $this->sumTotalCol('cambodia_re_premium_ceded'));
        $sheet->setCellValue('BO' . $endRow, $this->sumTotalCol('cambodia_re_tax_fee_amounts'));
        $sheet->setCellValue('BQ' . $endRow, $this->sumTotalCol('cambodia_re_ri_commission_amount'));
        $sheet->setCellValue('BR' . $endRow, $this->sumTotalCol('cambodia_re_net_due'));

        // Quota Share
        $sheet->setCellValue('BT' . $endRow, $this->sumTotalCol('quota_share_sum_insured_ceded'));
        $sheet->setCellValue('BU' . $endRow, $this->sumTotalCol('quota_share_total_premium_ceded'));
        $sheet->setCellValue('BX' . $endRow, $this->sumTotalCol('quota_share_tax_fee_amount'));
        $sheet->setCellValue('CB' . $endRow, $this->sumTotalCol('quota_share_ri_commission_amount'));
        $sheet->setCellValue('CC' . $endRow, $this->sumTotalCol('quota_share_net_due'));

        // PGI Retention
        $sheet->setCellValue('CE' . $endRow, $this->sumTotalCol('pgi_retention_sum_insured_ceded'));
        $sheet->setCellValue('CF' . $endRow, $this->sumTotalCol('pgi_retention_premium_ceded'));
        $sheet->setCellValue('CG' . $endRow, $this->sumTotalCol('pgi_retention_tax_fee_amount'));
        $sheet->setCellValue('CH' . $endRow, $this->sumTotalCol('pgi_retention_net_due'));

        // OTHER TREATY CESSIONS
        $sheet->setCellValue('CK' . $endRow, $this->sumTotalCol('reinsure_other_sum_insured_ceded'));
        $sheet->setCellValue('CL' . $endRow, $this->sumTotalCol('reinsure_other_premium_ceded'));
        $sheet->setCellValue('CM' . $endRow, $this->sumTotalCol('reinsure_other_tax_fee_amt'));
        $sheet->setCellValue('CO' . $endRow, $this->sumTotalCol('reinsure_other_ri_commission_amount'));
        $sheet->setCellValue('CP' . $endRow, $this->sumTotalCol('reinsure_other_net_due'));

        // OUTWARD FACULTATIVE REINSURANCE
        $sheet->setCellValue('CS' . $endRow, $this->sumTotalCol('facul_sum_insured_ceded'));
        $sheet->setCellValue('CT' . $endRow, $this->sumTotalCol('facul_premium_ceded'));
        $sheet->setCellValue('CU' . $endRow, $this->sumTotalCol('facul_tax_fee_amounts'));
        $sheet->setCellValue('CW' . $endRow, $this->sumTotalCol('facul_ri_commission_amount'));
        $sheet->setCellValue('CX' . $endRow, $this->sumTotalCol('facul_net_due'));

        // OUTWARD CO-INSURANCE
        $sheet->setCellValue('DA' . $endRow, $this->sumTotalCol('outward_sum_insured_ceded'));
        $sheet->setCellValue('DB' . $endRow, $this->sumTotalCol('outward_premium_ceded'));
        $sheet->setCellValue('DD' . $endRow, $this->sumTotalCol('outward_ri_commission_amount'));
        $sheet->setCellValue('DE' . $endRow, $this->sumTotalCol('outward_net_due'));

        //
        $sheet->setCellValue('DF' . $endRow, $this->sumTotalCol('reinsurance_premium_ceded'));

        $sheet->getStyle('A' . $endRow . ':DS' . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $endRow . ':DS' . $endRow)->getFont()->setSize(10)->setBold(true);
    }

    private function sumTotalCol($column_name)
    {
        return $this->report->sum($column_name);
    }

    public function columnFormats(): array{
        return [
            'DL' => 'dd-mmm-yyyy'
        ];
    }
}
