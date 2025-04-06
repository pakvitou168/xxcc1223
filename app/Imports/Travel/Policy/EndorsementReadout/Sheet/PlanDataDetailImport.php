<?php

namespace App\Imports\Travel\Policy\EndorsementReadout\Sheet;

use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlanDataDetailImport implements WithHeadingRow, WithCalculatedFormulas, SkipsEmptyRows, HasReferencesToOtherSheets
{
}
