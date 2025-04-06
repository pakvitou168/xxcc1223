<?php
namespace App\Imports\Travel\Policy\Sheets;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlanDataImport implements WithHeadingRow, SkipsEmptyRows {
  
}