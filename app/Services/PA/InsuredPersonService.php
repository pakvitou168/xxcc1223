<?php

namespace App\Services\PA;

use App\Services\PA\CalculationService;
use App\Models\PA\DataDetail;
use App\Models\PA\DataMaster;
use App\Models\RecordStatus;
use Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Str;

class InsuredPersonService
{
    private $serviceURL;
    public function __construct(private DataDetail $insuredPerson, private DataMaster $dataMaster, private CalculationService $calculationService)
    {
        $this->serviceURL = config('pgi.api_insurance_service_url');
    }
    public function save($data)
    {
        $requestBody = [$this->preparedBody($data)];
        info("API.ADD INSURED PERSON BODY:", $requestBody);
        $master = $this->dataMaster->with('quotation')->findOr($data['master_data_id'], fn() => throw new ModelNotFoundException("Quotation not found"));
        $http = Http::withHeaders(['X-User-Id' => auth()->id()])->patch($this->serviceURL . "/pa/quotations/" . $master->quotation->id . '/insured-person/add', $requestBody);
        info("API.ADD INSURED INFO RESPONSE:", [$http->body()]);
        $http->throw();
        $this->calculationService->generatePremium([$data['master_data_id'], auth()->id()]);
        return $http->body();
    }
    private function preparedBody($data): array
    {
        $data = array_diff_key($data, array_flip(['master_data_id']));
        return array_combine(
            array_map(fn($key) => lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))), array_keys($data)),
            array_values($data)
        );
    }
    public function update($data, $id)
    {
        $requestBody = [[...$this->preparedBody($data), 'dataDetailId' => $id]];
        info("API.UPDATE INSURED PERSON BODY:", $requestBody);
        $master = $this->dataMaster->with('quotation')->findOr($data['master_data_id'], fn() => throw new ModelNotFoundException("Quotation not found"));
        $http = Http::withHeaders(['X-User-Id' => auth()->id()])->patch($this->serviceURL . "/pa/quotations/" . $master->quotation->id . '/insured-person', $requestBody);
        info("API.UPDATE INSURED INFO RESPONSE:", [$http->body()]);
        $http->throw();
        $this->calculationService->generatePremium([$data['master_data_id'], auth()->id()]);
        return $http->body();
    }
    public function deleteMany(array $ids, $dataId)
    {
        $requestBody = $this->insuredPerson->whereIn('id', $ids)->select('id AS dataDetailId')->get()->toArray();
        info("API.DELETE INSURED PERSON BODY:", $requestBody);
        $master = $this->dataMaster->with('quotation')->findOr($dataId, fn() => throw new ModelNotFoundException("Quotation not found"));
        $http = Http::withHeaders(['X-User-Id' => auth()->id()])->delete($this->serviceURL . "/pa/quotations/" . $master->quotation->id . '/insured-person/delete', $requestBody);
        info("API.DELETE INSURED INFO RESPONSE:", [$http->body()]);
        $http->throw();
        $this->calculationService->generatePremium([$dataId, auth()->id()]);
        return $http->body();
    }
}