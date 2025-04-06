<?php

namespace App\Imports\PA;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class InsuredPersonImport implements WithHeadingRow, WithValidation, WithChunkReading, WithCalculatedFormulas
{
    public function chunkSize(): int
    {
        return 500;
    }
    public function rules(): array
    {
        return [
            'insured_person' => 'required',
            'relationship' => 'required',
            'occupation' => 'required',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'sum_insured' => 'required',
            'permanent_disablement' => 'required',
            'medical_expense' => 'required',
            'class' => 'required',
            'age' => 'required'
        ];
    }
}
