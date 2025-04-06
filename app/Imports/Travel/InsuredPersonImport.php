<?php

namespace App\Imports\Travel;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class InsuredPersonImport implements WithHeadingRow, WithValidation, WithChunkReading, WithCalculatedFormulas,SkipsEmptyRows
{
    public function chunkSize(): int
    {
        return 500;
    }
    public function rules(): array
    {
        return [
            'full_name' => 'required',
            'type_of_insured_person' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'passport' => 'required',
            'package_category' => 'required',
            'group_type' => 'required',
            'plan' => 'required'
        ];
    }
}
