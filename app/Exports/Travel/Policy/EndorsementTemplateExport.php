<?php

namespace App\Exports\Travel\Policy;

use App\Models\Travel\Policy\Policy;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EndorsementTemplateExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    private $policy;
    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }
    public function sheets(): array
    {
        return [
            new MasterDataExport($this->policy),
            new PlanDataExport($this->policy),
            new SchemaDataExport($this->policy),
            new NameListExport($this->policy)
        ];
    }
}
