<?php

namespace App\Imports\Travel\Policy\EndorsementReadout;

use App\Imports\Travel\Policy\EndorsementReadout\Sheet\InsuredPersonImport;
use App\Imports\Travel\Policy\EndorsementReadout\Sheet\MasterDataImport;
use App\Imports\Travel\Policy\EndorsementReadout\Sheet\PlanDataDetailImport;
use App\Imports\Travel\Policy\EndorsementReadout\Sheet\SchemaDataImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportAddDelete implements WithMultipleSheets
{
    use Importable;

    const  AGE_SETUP_SHEET = '0';
    const  SCHEMA_SHEET = '1';
    const  SCHEMAN_DETAIL_SHEET =  '2';
    public function sheets(): array
    {
        return [
            self::AGE_SETUP_SHEET,
            self::SCHEMA_SHEET,
            self::SCHEMAN_DETAIL_SHEET,
            'Policy Name List'  => new InsuredPersonImport,
            'Master Data'       => new MasterDataImport,
            'Plan Data Detail'  => new PlanDataDetailImport,
            'Schema Data'       => new SchemaDataImport,
            
        ];
    }
}
