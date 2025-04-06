<?php

namespace App\Services\PA;

use App\Exceptions\InsException;
use App\Models\PA\DataMaster;
use App\Models\PA\EndorsementV;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class EndorsementService
{

    private $syncService;
    public function __construct(private QuotationService $quotationService)
    {
        $this->syncService = new SyncService('POLICY');
    }
    public function findAll()
    {
        return EndorsementV::query();
    }
    public function generate($policyId, $data)
    {
        $bindings = [
            $policyId,
            $data['endorsement_type'],
            $data['endorsement_effective_date'],
            $data['endorsement_description'],
            auth()->id()
        ];
        $query = DB::select('select * from ins_pa_prod_gen_new_policy_endorsement(?,?,?,?,?)', $bindings);
        $result = collect($query)[0];
        info("GENERATE ENDORSEMENT", ['BODY' => json_encode($bindings), 'RESULT' => json_encode($query)]);
        if ($result->response_code === 200) {
            return $result;
        }
        Log::error("GENERATE ENDORSEMENT FAILED", ['RESULT' => json_encode($result)]);
        throw new InsException("Generate endorsement failed", 500);
    }
    public function edit($id)
    {
        $data = $this->quotationService->edit($id);
        $data->load('policy');
        return $data;
    }
    public function info($id)
    {
        return EndorsementV::whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Endorsement not found"));
    }
    public function update($data, $id)
    {
        $master = DataMaster::findOr($id, fn() => throw new ModelNotFoundException("Endorsement not found"));
        $master->update(attributes: $data);
        $this->syncService->syncClauses($master, [...$data['endorsement_clauses'], ...$data['general_exclusions'], ...$data['automatic_extensions']]);
    }
}