<?php

namespace App\Imports\Travel\Endorsement;

use App\Models\Travel\Policy\Policy;
use App\Models\Travel\PolicySchemaData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class SchemaDataImport implements ToCollection, WithHeadingRow,WithValidation,WithEvents
{
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if ($row->filter()->isNotEmpty() && $row['key']) {
                SchemaData::updateOrCreate(
                    [
                        'master_data_id'    => $this->policy->data_id,
                        'key'               => $row['key'],
                        'schema_type'       => $row['schema_type'],
                        'status'            => 'ACT'
                    ],
                    [
                        'age_band'          => $row['age_band'],
                        'no_female'         => $row['no_female'],
                        'no_person'         => $row['no_person'],
                        'rate'              => $row['rate'],
                        'plan_1'            => $row['plan_1'],
                        'plan_2'            => $row['plan_2'],
                        'plan_3'            => $row['plan_3'],
                        'plan_4'            => $row['plan_4'],
                        'plan_5'            => $row['plan_5'],
                        'master_data_type'  => $row['master_data_type'],
                        'schema_detail_code' => $row['schema_detail_code']
                    ]
                );
            }
        }
    }

    public function rules(): array
    {
        return [
            'key'   => ['required']
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event){
                $event->getReader()->getPhpSpreadsheetReader()->setReadDataOnly(true);
                $event->getReader()->getPhpSpreadsheetReader()->setReadEmptyCells(false);
            }
        ];
    }
}
