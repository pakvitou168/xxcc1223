<?php
namespace App\Imports\HS\Sheets;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchemaDataDetailImport implements WithCalculatedFormulas, WithHeadingRow, SkipsEmptyRows {

}