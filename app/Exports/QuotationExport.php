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
use App\Models\Insurance\QuotationView;

class QuotationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting, WithStrictNullComparison
{

    private $issue_date_from;
    private $issue_date_to;
    private $sub_classes;
    private $business_names;
    private $handler_names;
    private $users;
    private $quotationReportData;

    public function __construct($issue_date_from, $issue_date_to, $sub_classes, $business_names, $handler_names, $users)
    {
        $this->issue_date_from = $issue_date_from;
        $this->issue_date_to = $issue_date_to;
        $this->sub_classes = $sub_classes;
        $this->business_names = $business_names;
        $this->handler_names = $handler_names;
        $this->users = $users;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->quotationReportData = $this->getQuotationData()
                    ->map(function($item, $key){
                        $item->no = $key + 1;
                        return $item;
                    });
        // dd($this->quotationReportData);
        return $this->quotationReportData;
    }

    private function getQuotationData(){
        if($this->issue_date_from && $this->issue_date_to)
            return QuotationView::with(
                [
                    'auto:id,product_code,sum_insured,created_by,updated_at,updated_by,business_code,sale_channel,handler_code',
                    'quotation' => function($query) {
                        $query->select(
                            'data_id',
                            'quotation_no',
                            'approved_at',
                            'approved_by',
                            'approved_status',
                            'approved_reason',
                            'accepted_at',
                            'accepted_status',
                            'accepted_reason'
                        );
                    }
                ])->whereBetween('issued_at', [$this->issue_date_from, $this->issue_date_to])->latest('id')->get();
        else if($this->issue_date_from && !$this->issue_date_to)
            return QuotationView::with(
                [
                    'auto:id,product_code,sum_insured,created_by,updated_at,updated_by,business_code,sale_channel,handler_code',
                    'quotation' => function($query) {
                        $query->select(
                            'data_id',
                            'quotation_no',
                            'approved_at',
                            'approved_by',
                            'approved_status',
                            'approved_reason',
                            'accepted_at',
                            'accepted_status',
                            'accepted_reason'
                        );
                    }
                ])->whereDate('issued_at', '>=', $this->issue_date_from)->latest('id')->get();
        else if(!$this->issue_date_from && $this->issue_date_to)
            return QuotationView::with(
                [
                    'auto:id,product_code,sum_insured,created_by,updated_at,updated_by,business_code,sale_channel,handler_code',
                    'quotation' => function($query) {
                        $query->select(
                            'data_id',
                            'quotation_no',
                            'approved_at',
                            'approved_by',
                            'approved_status',
                            'approved_reason',
                            'accepted_at',
                            'accepted_status',
                            'accepted_reason'
                        );
                    }
                ])->whereDate('issued_at', '<=', $this->issue_date_to)->latest('id')->get();
        else
            return QuotationView::with(
                [
                    'auto:id,product_code,sum_insured,created_by,updated_at,updated_by,business_code,sale_channel,handler_code',
                    'quotation' => function($query) {
                        $query->select(
                            'data_id',
                            'quotation_no',
                            'approved_at',
                            'approved_by',
                            'approved_status',
                            'approved_reason',
                            'accepted_at',
                            'accepted_status',
                            'accepted_reason'
                        );
                    }
                ])->latest('id')->get();
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
            'Quotation Number',
            'Sub Class',
            'Customer Name',
            'Sum Insured (USD)',
            'Premium',
            'Business Channel',
            'Business Name',
            'Handler  Name',
            'Issue Date',
            'Issued By',
            'Quote Approval',
            'Approved By',
            'Approval Date',
            'Approved Reason',
            'Quote Acceptance',
            'Accepted Reason',
            'Acceptance Date'
        ]);

        return [
            [
                'Phillip General Insurance (Cambodia) Plc.'
            ],

            [''],

            [
                'AUTO QUOTATION'
            ],

            [''],

            [
                $report_period
            ],

            $titleRows->toArray(),
        ];
    }

    public function map($row): array
    {
        if(!is_null($row->auto->product_code))
            $row->auto->product_code = $this->sub_classes[$row->auto->product_code] ?? $row->auto->product_code;

        if(!is_null($row->auto->business_code))
            $row->auto->business_code = $this->business_names[$row->auto->business_code] ?? $row->auto->business_code;

        if(!is_null($row->auto->handler_code))
            $row->auto->handler_code = $this->handler_names[$row->auto->handler_code] ?? $row->auto->handler_code;

        if(!is_null($row->auto->updated_by))
            $row->auto->updated_by = $this->users[$row->auto->updated_by] ?? $row->auto->updated_by;

        if(!is_null($row->auto->created_by))
            $row->auto->created_by = $this->users[$row->auto->created_by] ?? $row->auto->created_by;

        if(!is_null($row->quotation))
            $row->quotation->approved_by = $this->users[$row->quotation->approved_by] ?? $row->quotation->approved_by;

        if(!is_null($row->issued_at))
            $row->issued_at = \Carbon\Carbon::parse($row->issued_at)->format('d/m/Y');

        if(!is_null($row->quotation)){
            if(!is_null($row->quotation->approved_at)){
                if($row->quotation->approved_status == 'APV')
                    $row->quotation->approved_at = \Carbon\Carbon::parse($row->quotation->approved_at)->format('d/m/Y');
                else
                    $row->quotation->approved_at = null;
            }
            if(!is_null($row->quotation->accepted_at)){
                if($row->quotation->accepted_status == 'ACP')
                    $row->quotation->accepted_at = \Carbon\Carbon::parse($row->quotation->accepted_at)->format('d/m/Y');
                else
                    $row->quotation->accepted_at = null;
            }
        }

        if(!is_null($row->quotation)){
            if($row->quotation->approved_status == 'APV')
                $row->quotation->approved_status = 'APPROVED';
            else if($row->quotation->approved_status == 'REJ')
                $row->quotation->approved_status = 'REJECTED';
            else if($row->quotation->approved_status == 'PND')
                $row->quotation->approved_status = 'PENDING';

            if($row->quotation->accepted_status == 'ACP')
                $row->quotation->accepted_status = 'ACCEPTED';
            else if($row->quotation->accepted_status == 'REJ')
                $row->quotation->accepted_status = 'REJECTED';
            else if($row->quotation->accepted_status == 'PND')
                $row->quotation->accepted_status = 'PENDING';
        }

        $rows = collect([
            $row->no,
            $row->document_no,
            $row->auto->product_code,
            $row->name_en,
            $row->auto->sum_insured,
            $row->total_premium,
            $row->auto->sale_channel,
            $row->auto->business_code,
            $row->auto->handler_code,
            $row->issued_at,
            $row->auto->updated_by ?? $row->auto->created_by ?? '',
            $row->quotation->approved_status ?? '',
            $row->quotation->approved_by ?? '',
            $row->quotation->approved_at ?? '',
            $row->quotation->approved_reason ?? '',
            $row->quotation->accepted_status ?? '',
            $row->quotation->accepted_reason ?? '',
            $row->quotation->accepted_at ?? '',
        ]);

        return $rows->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'O' => 30,
            'Q' => 30,
        ];
    }

    public function styles(Worksheet $sheet) {
        $rowCount =  count($this->quotationReportData);
        // Cells borders
        $startRow = 6;
        $endRow = $startRow + $rowCount;

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
        $this->conditionalStyleTitle($sheet, $startRow);
    }

    private function conditionalStyles($sheet, $startRow, $endRow) {
        $sheet->getStyle('B:R')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O')->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$startRow.':R'.$endRow)->getFont()->setSize(10);
        $sheet->getStyle('A'.$startRow.':R'.$endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000']
                ],
            ],
        ]);
    }

    private function conditionalStyleTitle($sheet, $startRow){
        $sheet->getStyle('A'.$startRow.':R'.$startRow)->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A'.$startRow.':R'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$startRow.':R'.$startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A'.$startRow.':R'.$startRow)->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'D9D9D9'],
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
