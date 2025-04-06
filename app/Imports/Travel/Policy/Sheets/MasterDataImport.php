<?php
namespace App\Imports\Travel\Policy\Sheets;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterDataImport implements WithCalculatedFormulas, WithHeadingRow, SkipsEmptyRows {
  
}