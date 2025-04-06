<?php

namespace App\Services\PA;

use App\Enums\RecordStatus;
use App\Exceptions\InsException;
use App\Imports\PA\QuotationImport;
use App\Models\PA\DataMaster;
use App\Models\PA\WorkingClass;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Excel;
use Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Validator;

class SyncService
{
    private $dateType;
    public function __construct($dataType)
    {
        $this->dateType = $dataType;
    }
    public function syncJointDetails(DataMaster $master, array $data)
    {
        $master->jointAccountDetails()->delete();
        $jointDetails = collect($data)->map(function ($item) use ($master) {
            $item['product_line_code'] = $master->product->product_line_code;
            $item['product_code'] = $master->product_code;
            $item['data_id'] = $master->id;
            return $item;
        })->toArray();
        if (count($jointDetails)) {
            $master->jointAccountDetails()->createMany($jointDetails);
        }
    }
    public function syncClauses(DataMaster $master, array $data)
    {
        $master->insuranceClauses()
            ->syncWithPivotValues($data, [
                'status' => RecordStatus::ACTIVE
            ]);
    }
    public function syncInsuredPersons(DataMaster $master, $file)
    {
        $insuredPersons = collect($this->extractInsuredPerson($file))->map(function ($insuredPs) use ($master) {
            $insuredPs['insured_person_uuid'] = Str::uuid();
            $insuredPs['product_code'] = $master->product_code;
            $insuredPs['master_data_type'] =  $this->dateType;
            $insuredPs['name'] = $insuredPs['insured_person'];
            $insuredPs['date_of_birth'] = $this->excelToDate($insuredPs['date_of_birth']);
            $insuredPs['working_class_code'] = $insuredPs['class'];
            $insuredPs['permanent_disablement_amount'] = $insuredPs['permanent_disablement'];
            $insuredPs['medical_expense_amount'] = $insuredPs['medical_expense'];
            $insuredPs['inception_date'] = $master->effective_date_from;
            return $insuredPs;
        })->toArray();
        if (count($insuredPersons) == 0) {
            throw new InsException("No insured persons inserted");
        }
        $master->dataDetails()->createMany($insuredPersons);
    }
    private function excelToDate($date)
    {
        return $date ? Date::excelToDateTimeObject($date)->format('Y-m-d') : null;
    }
    private function extractInsuredPerson($file)
    {
        $sheets = Excel::toArray(new QuotationImport(), $file);
        $insuredPersons = isset($sheets['Name List']) ? $sheets['Name List'] : throw new InsException("Name List sheet not found");
        if ($this->validateInsuredData($insuredPersons)) {
            return $insuredPersons;
        }
    }
    public function validateInsuredData($insuredPersons)
    {
        $validator = Validator::make($insuredPersons, [
            '*.insured_person' => ['required'],
            '*.occupation' => ['required'],
            '*.gender' => ['required'],
            '*.date_of_birth' => ['required'],
            '*.sum_insured' => ['required', 'numeric'],
            '*.permanent_disablement' => ['required', 'numeric'],
            '*.medical_expense' => ['required', 'numeric'],
            '*.age' => ['required'],
            '*.relationship' => ['nullable'],
            '*.class' => [Rule::in(WorkingClass::codeList())]
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        } elseif (count($insuredPersons) == 0) {
            throw ValidationException::withMessages([
                'file' => ['File contains no insured person']
            ]);
        }
        return true;
    }
    public function syncExtensions(DataMaster $master,array $data)
    {
        $extensionData = array_column(array_map(function ($item) {
            return array_intersect_key($item, array_flip(['extension_id', 'extension_code', 'extension_name', 'extension_description', 'is_selected', 'amount_type', 'rating']));
        }, $data), null, 'extension_id');
        $master->extensions()->sync($extensionData);
    }
}