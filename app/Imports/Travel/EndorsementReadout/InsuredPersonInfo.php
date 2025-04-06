<?php

namespace App\Imports\Travel\EndorsementReadout;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InsuredPersonInfo implements WithHeadingRow, WithValidation, SkipsEmptyRows, WithChunkReading
{
    public function chunkSize(): int
    {
        return 500;
    }

    public function rules(): array
    {
        return [
            'insured_id' => 'required',
            'name' => 'required',
            'occupation' => 'required',
            'gender' => 'required',
            'date_of_birth' => 'required',
        ];
    }
}
