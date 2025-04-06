<?php

namespace App\Imports\Travel;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuotationImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Name List' => new InsuredPersonImport()
        ];
    }
}
