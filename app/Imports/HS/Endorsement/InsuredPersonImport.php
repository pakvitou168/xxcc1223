<?php

namespace App\Imports\HS\Endorsement;

use App\Models\HS\DataDetail;
use App\Models\HS\Policy;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class InsuredPersonImport implements ToCollection, WithHeadingRow,WithValidation,WithEvents
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
            if ($row->filter()->isNotEmpty() && $row['name']) {
                $dob = date('Y-m-d', strtotime(str_replace('/', ' ', $row['date_of_birth'])));
                DataDetail::updateOrCreate([
                    'id'                => $row['hidden_id'],
                    'master_data_id'    => $this->policy->data_id
                ], [
                    'dob'                   => $dob,
                    'gender'                => in_array(strtolower($row['gender']), ['m', 'male']) ? 'M' : 'F',
                    'name'                  => $row['name'],
                    'occupation'            => $row['occupation'],
                    'standard_plan'         => $row['standard_plan'],
                    'optional_plan'         => $row['optional_plan'],
                    'endorsement_e_date'    => $row['endorsement_effective_date'],
                    'endos_day_remaining'   => $row['days_of_endorsement'],
                    'premium'               => $row['endorsement_premium'],
                    'endorsement_stage'     => $row['edorsement_no'],
                    'endorsement_state'     => strtoupper($row['transaction_type']),
                    'remark'                => $row['endorsement_remark']
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'name'          => ['required'],
            'gender'        => ['required'],
            'date_of_birth' => ['required'],
            'occupation'    => ['required'],
            'standard_plan' => ['required'],
            'optional_plan' => ['required'],

            'endorsement_premium'           => ['required'],
            'endorsement_effective_date'    => ['required']
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
