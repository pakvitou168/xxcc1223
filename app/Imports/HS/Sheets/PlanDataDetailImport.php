<?php
namespace App\Imports\HS\Sheets;

use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlanDataDetailImport implements HasReferencesToOtherSheets, WithHeadingRow, SkipsEmptyRows {

}