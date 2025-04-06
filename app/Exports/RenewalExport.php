<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RenewalExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting {

  protected $totalSumInsured;
  protected $totalGrossPremium;
  protected $totalRenewalSumInsured;
  protected $totalRenewalPremium;

  public function __construct(protected $query, protected $count, protected $expiredFrom, protected $expiredTo) {}

  public function headings(): array
  {
    return [
      [
        'RENEWAL LIST'
      ],
      [
        $this->getDates($this->expiredFrom, $this->expiredTo)
      ],
      [],
      [
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        'EXPIRED TERMS',
        '',
        '',
        '',
        '',
        'RENEWAL TERMS'
      ],
      [
        'No.',
        'LOB',
        'Risk Type',
        'Policy / Endorsement No.',
        'Inception  Date',
        'Expiry Date',
        'Type of Client',
        'Insured Name',
        'Risk Coverage',
        'Risk Occupation',
        'Handler Code',
        'Business Channel',
        'Business Code',
        'Source of Referral',
        'Total Sum Insured (USD)',
        'Gross Written Premium (USD)',
        'Claim Incurred (USD)',
        'Claim Paid (USD)',
        'Claim Outstanding (USD)',
        'Risk Occupation',
        'Total Sum Insured (USD)',
        'Renewal Premium (USD)',
        'Renewal Status',
        'Reasons',
        'Date of Issue',
        'Issued by', 
      ],
    ];
  }

  protected function getDates($fromDate, $toDate) {
    if (!$fromDate && !$toDate) {
      return 'All records';
    }

    $str = 'Renewal Period: ';
    if ($fromDate) {
      $str .= "From " . $this->formattedDate($fromDate, 'd F Y');
    }

    if ($fromDate && $toDate) {
      $str .= " ";
    }

    if ($toDate) {
      $str .= "To " . $this->formattedDate($toDate, 'd F Y');
    }

    return $str;
  }

  public function query()
  {
    $this->totalSumInsured = $this->query->sum('sum_insured');
    $this->totalGrossPremium = $this->query->sum('total_gross_premium');
    $this->totalRenewalSumInsured = $this->query->sum('renew_sum_insured');
    $this->totalRenewalPremium = $this->query->sum('renew_premium');

    return $this->query;
  }

  public function columnWidths(): array
  {
    return [
      'A' => 9,
      'B' => 15,
      'C' => 9,
      'D' => 25,
      'E' => 15,
      'F' => 15,
      'G' => 15,
      'H' => 50,
      'I' => 15,
      'J' => 30,
      'K' => 20,
      'L' => 20,
      'M' => 20,
      'N' => 20,
      'O' => 25,
      'P' => 25,
      'Q' => 25,
      'R' => 25,
      'S' => 25,
      'T' => 25,
      'U' => 25,
      'V' => 25,
      'W' => 15,
      'X' => 30,
      'Y' => 15, 
      'Z' => 20,
    ];
  }

  public function map($item): array
  {
    return [
      null,
      $item->lob,
      $item->risk_type,
      $item->policy_no,
      $this->formattedDate($item->inception_date),
      $this->formattedDate($item->expiry_date),
      $item->client_type,
      $item->insured_name,
      $item->risk_coverage,
      $item->risk_occupation,
      $item->handler_code,
      $item->business_channel,
      $item->business_code,
      '',
      $item->sum_insured,
      $item->total_gross_premium,
      $item->claim_incurred,
      $item->claim_paid,
      $item->claim_outstanding,
      $item->risk_occupation,
      $item->renew_sum_insured,
      $item->renew_premium,
      $item->status,
      $item->renewed_remark,
      $this->formattedDate($item->issued_date),
      $item->issued_by,
    ];
  }

  private function formattedDate($dateString, $format = 'd/M/Y') {
    return Carbon::parse($dateString)->format($format);
  }

  public function styles(Worksheet $sheet) {
    $this->globalStyles($sheet);
    $this->headerStyles($sheet);

    $contentStartRow = 6;
    $contentEndRow = $contentStartRow + ($this->count - 1);
    $this->contentStyles($sheet, $contentStartRow, $contentEndRow);

    $this->setRowsNumber($sheet, $this->count, $contentStartRow);
    $this->setTotalRow($sheet, $contentEndRow);
  }

  private function globalStyles(Worksheet $sheet) {
    $sheet->getStyle("A:Z")->applyFromArray([
      'font' => [
        'name' => 'Candara'
      ]
    ]);
  }

  private function headerStyles(Worksheet $sheet) {
    $firstRowHeader = 4;
    $secondRowHeader = 5;

    // Font size
    $sheet->getStyle('A1:A2')->getFont()->setSize(12)->setBold(true);

    // Merge cells
    $sheet->mergeCells("O$firstRowHeader:P$firstRowHeader");
    $sheet->mergeCells("T$firstRowHeader:Z$firstRowHeader");

    $sheet->getStyle("A$firstRowHeader:Z$secondRowHeader")
      ->getAlignment()
      ->setWrapText(true)
      ->setVertical(Alignment::VERTICAL_TOP)
      ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A$firstRowHeader:Z$secondRowHeader")->getFont()->setSize(10)->setBold(true);

    // Border
    $sheet->getStyle("A$firstRowHeader:Z$firstRowHeader")->applyFromArray([
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rbg' => '000'],
        ],
        'bottom' => [
          'borderStyle' => Border::BORDER_NONE,
        ],
      ],
    ]);

    $sheet->getStyle("O$firstRowHeader:P$firstRowHeader")->applyFromArray([
      'borders' => [
        'bottom' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rbg' => '000'],
        ],
      ],
    ]);

    $sheet->getStyle("T$firstRowHeader:Z$firstRowHeader")->applyFromArray([
      'borders' => [
        'bottom' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rbg' => '000'],
        ],
      ],
    ]);

    $sheet->getStyle("A$secondRowHeader:Z$secondRowHeader")->applyFromArray([
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rbg' => '000'],
        ],
        'top' => [
          'borderStyle' => Border::BORDER_NONE,
        ],
      ],
    ]);

    // Background
    $sheet->getStyle("A$firstRowHeader:S$secondRowHeader")->getFill()->applyFromArray([
      'fillType' => 'solid',
      'rotation' => 0,
      'color' => ['rgb' => 'FFC000'],
    ]);
    $sheet->getStyle("T$firstRowHeader:Z$secondRowHeader")->getFill()->applyFromArray([
      'fillType' => 'solid',
      'rotation' => 0,
      'color' => ['rgb' => '92D050'],
    ]);
  }

  private function contentStyles(Worksheet $sheet, $startRow, $endRow) {
    $sheet->getStyle("A$startRow:Z$endRow")->applyFromArray([
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rbg' => '000'],
        ],
      ],
    ]);
    $sheet->getStyle("A$startRow:Z$endRow")->getFont()->setSize(10);
  }

  private function setRowsNumber(Worksheet $sheet, $rowCount, $startRow) {
    if ($rowCount > 0) {
      for ($i = 0; $i < $rowCount; $i++) {
        $sheet->setCellValue("A" . $startRow + $i, $i + 1);
      }
    }
  }

  private function setTotalRow(Worksheet $sheet, $endRow) {
    $totalRow = $endRow + 1;

    $sheet->getStyle("A$totalRow:Z$totalRow")->getFill()->applyFromArray([
      'fillType' => 'solid',
      'rotation' => 0,
      'color' => ['rgb' => 'ffff00'],
    ]);

    $sheet->getStyle("A$totalRow:Z$totalRow")->applyFromArray([
      'borders' => [
        'outline' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rbg' => '000'],
        ],
      ],
    ]);

    $sheet->getStyle("A$totalRow:Z$totalRow")->getFont()->setSize(10)->setBold(true);

    $sheet->setCellValue("A$totalRow", 'TOTAL');

    // Total Sum Insured
    $sheet->setCellValue("O$totalRow", $this->totalSumInsured);

    // Total Gross Written Premium
    $sheet->setCellValue("P$totalRow", $this->totalGrossPremium);

    // Total Renew Sum Insured
    $sheet->setCellValue("U$totalRow", $this->totalRenewalSumInsured);

    // Total Renew Premium
    $sheet->setCellValue("V$totalRow", $this->totalRenewalPremium);
  }

  public function columnFormats(): array
  {
    return [
      'O:S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
      'U:V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ];
  }
}