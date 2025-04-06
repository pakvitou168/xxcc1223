<?php

namespace App\Imports\Travel\EndorsementReadout\Sheet;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchemaDataImport implements WithHeadingRow,SkipsEmptyRows,WithCalculatedFormulas
{
}
