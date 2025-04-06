<?php
namespace App\Imports\Travel\Policy;

use App\Imports\Travel\Policy\Sheets\DataDetailImport;
use App\Imports\Travel\Policy\Sheets\MasterDataImport;
use App\Imports\Travel\Policy\Sheets\PlanDataDetailImport;
use App\Imports\Travel\Policy\Sheets\PlanDataImport;
use App\Imports\Travel\Policy\Sheets\SchemaDataDetailImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataImport implements WithMultipleSheets{
  use Importable;

  CONST AGE_SETUP_SHEET = '0'; // Age Setup
  CONST SCHEMA_SHEET = '1'; // Schema (default)
  CONST SCHEMA_DETAIL_SHEET = '2'; //Schema detail (default)
  CONST MASTER_DATA_SHEET = '3'; // Master Data
  CONST DATA_DETAIL_SHEET = '4'; // Data Detail
  CONST PLAN_DATA_SHEET = '5'; // Plan Data
  CONST PLAN_DATA_DETAIL_SHEET = '6'; // Plan Data Detail
  CONST SCHEMA_DATA_SHEET = '7'; // Schema Data

  public function sheets(): array {
    return [
      self::AGE_SETUP_SHEET,
      self::SCHEMA_SHEET,
      self::SCHEMA_DETAIL_SHEET,
      self::MASTER_DATA_SHEET => new MasterDataImport(),
      self::DATA_DETAIL_SHEET => new DataDetailImport(),
      self::PLAN_DATA_SHEET => new PlanDataImport(),
      self::PLAN_DATA_DETAIL_SHEET => new PlanDataDetailImport(),
      self::SCHEMA_DATA_SHEET => new SchemaDataDetailImport(),
    ];
  }
}