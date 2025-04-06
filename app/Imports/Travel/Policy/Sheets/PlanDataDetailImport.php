<?php
namespace App\Imports\Travel\Policy\Sheets;

use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlanDataDetailImport implements HasReferencesToOtherSheets, WithHeadingRow, SkipsEmptyRows {

}