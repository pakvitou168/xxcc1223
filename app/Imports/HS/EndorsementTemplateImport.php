<?php

namespace App\Imports\HS;

use App\Imports\HS\Endorsement\InsuredPersonImport;
use App\Imports\HS\Endorsement\SchemaDataImport;
use App\Models\HS\Policy;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EndorsementTemplateImport implements WithMultipleSheets
{
    const MASTER_DATA_SHEET = 0;
    const PLAN_DATA_SHEET = 1;
    private $policy;

    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    
    public function sheets(): array
    {
        return [
            self::MASTER_DATA_SHEET,
            self::PLAN_DATA_SHEET,
            new SchemaDataImport($this->policy),
            new InsuredPersonImport($this->policy)
        ];
    }
}
