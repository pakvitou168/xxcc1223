<?php

namespace App\Services\PA;

use App\Models\PA\Reinsurance;
use App\Models\RecordStatus;
use DB;

class ReinsuranceService
{
    public function __construct(private Reinsurance $reinsurance)
    {

    }

    public function detail($id)
    {
        return $this->reinsurance::whereDataId($id)->with('type', 'group', 'partner')->whereLvl(1)->with(['subs' => fn($q) => $q->with('type', 'group', 'partner')->whereStatus(RecordStatus::ACTIVE)])->whereStatus(RecordStatus::ACTIVE)->get()->map(function ($item) {
            $item->participant = @$item->partner->name;
            $item->reinsurance_type = $item->type ? $item->type->reinsurance_type : @$item->group->name;
            $item->subs->map(function ($sub) {
                $sub->participant = @$sub->partner->name;
                $sub->reinsurance_type = $sub->type ? $sub->type->reinsurance_type : @$sub->group->name;
                return $sub->makeHidden(['type', 'group', 'partner']);
            });
            return $item->makeHidden(['type', 'group', 'partner']);
        });
    }
    public function save($data, $master)
    {
        $keys = ['ri_commission', 'tax_fee', 'share', 'treaty_code'];
        $data = $data->map(function ($item) use ($keys, $master) {
            $item = collect($item)->only($keys);
            return array_merge($item->toArray(), [
                'policy_id' => $master->policy->id,
                'data_id' => $master->id,
                'product_line_code' => $master->product->product_line_code,
                'product_code' => $master->product_code,
                'lvl' => 1
            ]);
        });
        $master->reinsurances()->createMany($data);
    }
    public function delete(array $ids)
    {
        $this->reinsurance->whereIn('id', $ids)->update([
            'status' => RecordStatus::DELETED
        ]);
    }
    public function generateShare($policyId)
    {
        $bidings = [
            $policyId,
        ];
        $query = DB::select('select * from ins_pa_generate_reinsurance_share(?)', $bidings);
        $result = collect($query)->first();
        info("QUERY. GENERATE REINSURANCE SHARE:", ['result' => json_encode($result)]);
        if ($result && $result->response_code === 200) {
            return $result;
        }
        throw new \Exception("Generate reinsurance share failed", 500);
    }
    public function generateData($policyId)
    {
        $bidings = [
            $policyId,
        ];
        $query = DB::select('select * from ins_pa_generate_reinsurance_data(?)', $bidings);
        $result = collect($query)->first();
        info("QUERY. GENERATE REINSURANCE SHARE:", ['result' => json_encode($result)]);
        if ($result && $result->response_code === 200) {
            return $result;
        }
        throw new \Exception("Generate reinsurance share failed", 500);
    }
    public function update($data)
    {
        $keys = ['ri_commission', 'tax_fee', 'share'];
        $ids = $data->pluck('id')->toArray();
        if (!count($ids)){
            throw new \Exception("No ids provided for update");
        }
        $data->each(function($item) use($keys): void{
            $this->reinsurance->find($item['id'])->update(collect($item)->only($keys)->toArray());
        });
    }
}