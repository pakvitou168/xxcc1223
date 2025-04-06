<?php

namespace App\Services\Travel;

use App;
use App\Enums\TravelInsuredPersonType;
use App\Enums\TravelPackage;
use App\Enums\TravelPlan;
use App\Exports\Travel\InsuredPersonExport;
use App\Imports\Travel\QuotationImport;
use App\Models\Travel\QuotationV;
use App\Models\RecordStatus;
use App\Models\Travel\DataMaster;
use App\Models\Travel\DeductibleData;
use App\Models\Travel\Quotation;
use App\Models\UserManagement\User\UserFile;
use Arr;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use KhmerDateTime\KhmerDateTime;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class QuotationService
{
    const DATA_TYPE = 'QUOTATION';
    private $serviceUrl;
    const DEFAULT_LANG = 'EN';
    public function __construct(private Quotation $quote, private DataMaster $master, private QuotationV $quotationV)
    {
        $this->serviceUrl = config('pgi.api_insurance_service_url');
    }
    public function list()
    {
        return $this->quotationV->query();
    }
    public function detail($id, $lang = 'en'): DataMaster
    {
        $isEN = $lang === 'en';
        $langCode = $isEN ? 'EN' : 'KM';
        $data = $this->master->with([
            'quotation' => fn($q) => $q->select(
               'data_id',
                'document_no',
                'approved_status',
                'accepted_status',
                'approved_by',
                'created_by',
                'updated_by',
                'id'
            ),
            'product' => fn($q) => $q->select(
                'code',
                $isEN ? 'name' : 'name_kh AS name',
                $isEN ? 'coverage_en AS coverage' : 'coverage_kh AS coverage',
            ),
            'customer' => fn($q) => $q->with([
                'classification' => fn($q1) => $q1->select(
                    $isEN ? 'description AS occupation' : 'description_kh AS occupation',
                    'cust_classification'
                )
            ])
                ->select(
                    'customer_no',
                    $isEN ? 'name_en AS name' : 'name_kh AS name',
                    'cust_classification'
                ),
            'insuranceData',
            'dataDetailsView',
            'coverage',
        ])
            ->findOr($id, fn() => throw new ModelNotFoundException("Quotation not found", 404));

        $data->customer->address = $data->customer?->correspondence_address;
        $data->insurance_period = $this->insurancePeriod($data, $lang);
        $data->coverageStandard = $data->coverage->whereIn('category', ['STANDARD','AGG_LIMIT'])->where('lang_code',$langCode)->values();
        $data->coverageAdditional = $data->coverage->where('category', 'ADDITIONAL')->where('lang_code',$langCode)->values();
        $data->issued_on = $lang == 'en' ? Carbon::parse($data->updated_at)->format('d F Y') : KhmerDateTime::parse($data->updated_at)->format('LL');
       
        $data->signature = $data->quotation?$this->signature($data->quotation):null;
        unset($data->coverage);
        $data->deductibles =  DeductibleData::where('data_id',$id)->get();
        $data->jurisdiction = trans('Kingdom of Cambodia', [], $lang);
        $data->total_insured_persons = $this->master::withCount(['dataDetails' => fn($q) => $q->whereStatus(RecordStatus::ACTIVE)])->find($id)->data_details_count;
        return $data;
    }

    public function save($data)
    {
        $assignFields = $this->assignFields($data);
        $master = $this->master->create($assignFields);
        $endorsementClauses = $data['endorsement_clauses'] ?? [];
        $master->insuranceClauses()
            ->syncWithPivotValues([...$endorsementClauses, ...$data['general_exclusions']], [
                'status' => RecordStatus::ACTIVE
            ]);
        $insuredPersons = collect($this->extractInsuredPerson($data['file']))->map(function ($insuredPs) use ($master) {
            $insuredPs['insured_person_uuid'] = Str::uuid();
            $insuredPs['name'] = $insuredPs['full_name'];
            $insuredPs['product_code'] = $master->product_code;
            $insuredPs['master_data_type'] = self::DATA_TYPE;
            $insuredPs['date_of_birth'] = $this->excelToDate($insuredPs['date_of_birth']);
            $insuredPs['plan_code'] = $insuredPs['plan'];
            $insuredPs['is_child'] = $insuredPs['type_of_insured_person'] === TravelInsuredPersonType::ACCOMPANYING_CHILDREN->value;
            unset($insuredPs['is_selected']);
            unset($insuredPs['id']);
            return $insuredPs;
        });
        if($data['package_code'] === TravelPackage::GROUP->value && !($insuredPersons->count() === count(array_unique($insuredPersons->pluck('plan_code')->toArray())))){
            info('Plan must be the same.');
            throw new Exception("Plan must be the same.");
        }
        $insuredPersons = $insuredPersons->toArray();

        if (count($insuredPersons) == 0){
            info('No insured persons inserted.');
            throw new Exception("No insured persons inserted.");
        }
        $master->dataDetails()->createMany($insuredPersons);
        $data['schedule_benefits'] = collect($data['schedule_benefits'])->map(function ($item) {
            unset($item['is_selected']);
            unset($item['id']);
            return $item;
        })->toArray();
        if($this->generateOverallDeductible($master)){
            $this->generateQuotation($master);
        }
       return $master;
    }

    public function delete($id)
    {
        return $this->master->whereId($id)->update([
            'status' => RecordStatus::DELETED
        ]);
    }

    private function assignFields($data)
    {
        $assignFields = Arr::except($data, ['file', 'endorsement_clauses', 'general_exclusions','package_code','group_type','itinerary','type_of_insured_person']);
        return [
            ...$assignFields,
            'package_code' => $data['package_code'] === TravelPackage::INDIVIDUAL->value ? $data['package_code'] : $data['group_type'],
            'status' => RecordStatus::ACTIVE,
            'data_type' => self::DATA_TYPE,
            'branch_code' => '000',
            'itinerary' => implode('~', $data['itinerary']),
        ];
    }

    private function extractInsuredPerson($file)
    {
        $sheets = Excel::toArray(new QuotationImport(), $file);
        $insuredPersons = isset($sheets['Name List']) ? $sheets['Name List'] : throw new Exception("Name List sheet not found");
        if ($this->validateInsuredData($insuredPersons))
            return $insuredPersons;
    }

    public function validateInsuredData($insuredPersons)
    {
        $validator = Validator::make($insuredPersons, [
            '*.full_name' => ['required'],
            '*.type_of_insured_person' => ['required','in:'.TravelInsuredPersonType::ACCOMPANYING_CHILDREN->value.','.TravelInsuredPersonType::INSURED_PERSON->value],
            '*.gender' => ['required','in:F,M'],
            '*.date_of_birth' => ['required'],
            '*.passport' => ['required'],
            '*.plan' => ['required','in:'.TravelPlan::STANDARD->value.','.TravelPlan::DELUXE->value.','.TravelPlan::EXECUTIVE->value],
        ]);
        if ($validator->fails()) {
            info(json_encode($validator));
            throw new ValidationException($validator);
        } elseif (count($insuredPersons) == 0) {
            info('File contains no insured person');
            throw ValidationException::withMessages([
                'file' => ['File contains no insured person']
            ]);
        }
        return true;
    }

    private function excelToDate($date)
    {
        return $date ? Date::excelToDateTimeObject($date)->format('Y-m-d') : null;
    }

    private function generateQuotation($master)
    {
        $generateQuote = DB::select("select  * from ins_tv_prod_generate_new_quotation(?,?)", [$master->id, auth()->id()]);
        info("Generate Travel Quotation.", ['binding' => json_encode([$master->id, auth()->id()]), 'data' => json_encode($generateQuote)]);
        $result = collect($generateQuote)->first();
        if (!$result || $result->response_code !== 200) {
            Log::error('Generating Travel quote failed: ', ['result' => json_encode($generateQuote)]);
            throw new Exception($result->response_message ?? "Generating quotation failed", 500);
        }
        return true;
    }

    private function signature($quote)
    {
        return UserFile::whereFileType('SIGNATURE')->whereUserId($quote->approved_by)->value('file_url');
    }

    private function insurancePeriod($master, $lang = 'en')
    {
        $from = $lang == 'en' ? Carbon::parse($master->effective_date_from)->format('d/F/Y') : KhmerDateTime::parse($master->effective_date_from)->format('LL');
        $to = $lang == 'en' ? Carbon::parse($master->effective_date_to)->format('d/F/Y') : KhmerDateTime::parse($master->effective_date_to)->format('LL');

        return trans('Start Date', [], $lang) . ' ' . $from . ' ' . trans('Returning', [], $lang) . ' ' . $to . ' ' . trans('Number of Days', [], $lang) . ' : ' . $master->effective_day;
    }

    public function approve($id, $data)
    {
        $quote = $this->quote->whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Quotation not found"));
        if ($quote->created_by == auth()->id())
            throw new Exception("You can not approve your own quotation", 403);
        $quote->update([
            'approved_status' => $data['status'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approved_reason' => $data['remark'],
            ...($data['status'] === RecordStatus::APPROVED ? ['accepted_status' => RecordStatus::PENDING] : [])
        ]);
        return $quote;
    }
    public function accept($id, $data)
    {
        $quote = $this->quote->whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Quotation not found"));
        if ($quote->created_by == auth()->id())
            throw new Exception("You can not accept your own quotation", 403);
        $quote->update([
            'accepted_status' => $data['status'],
            'accepted_by' => auth()->id(),
            'accepted_at' => now(),
            'accepted_reason' => $data['remark']
        ]);
        return $quote;
    }
    public function proceed($id): bool
    {
        $quotation = $this->quote->whereDataId($id)->firstOr(fn() => throw new ModelNotFoundException("Quotation not found"));
        $query = DB::select('select * from ins_tv_prod_generate_new_policy(?,?)', [$quotation->id, auth()->id()]);
        $result = collect($query)->first();
        if (!$result || $result->response_code !== 200) {
            Log::error("Failed to proceed quotation no. $quotation->document_no", ['result' => json_encode($query)]);
            throw new Exception("Proceeding to policy failed", 500);
        }
        return true;
    }

    private function generateMasterPremium($master)
    {
        $generatePremium = DB::select("select  * from ins_tv_do_calc_premium(?,?)", [$master->id, auth()->id()]);
        info("Generate Travel Premium.", ['binding' => json_encode([$master->id, auth()->id()]), 'data' => json_encode($generatePremium)]);
        $result = collect($generatePremium)->first();
        if (!$result || $result->response_code !== 200) {
            Log::error('Generate Travel premium failed: ', ['result' => json_encode($generatePremium)]);
            throw new Exception(($result->response_message ?? "Generating premium failed"), 500);
        }
        return true;
    }

    public function generateOverallDeductible($master)
    {
        $params = [
            'QUOTATION',
            $master->id,
            auth()->user()->id,
        ];
        $generateOverallDeductible = DB::select("select * from ins_tv_prod_generate_deductible(?,?,?)", $params);
        $result = collect($generateOverallDeductible)->first();
        if (!$result || $result->response_code !== 200) {
            Log::error('Generate Travel deductible failed: ', ['result' => json_encode($generateOverallDeductible)]);
            throw new Exception($result->response_message ?? "Generating Travel deductible failed", 500);
        }
        return true;
    }

    public function storeCoverage($master,$coverageData)
    {
        $response = Http::withHeaders(['Accept-Language' => self::DEFAULT_LANG,'X-User-Id' => auth()->user()->id])->put($this->serviceUrl . '/tv/coverage-data/'.$master->id, $coverageData);
     
        $response->throw();
        $data = json_decode($response->body());
        info(json_encode($data));
        if (!$data || $data->code !== 'SUC-000') {
            $master->update(['status' => RecordStatus::DELETED,'updated_by','system']);
            Log::error('Store coverage data: ', ['result' => json_encode($data)]);
            throw new Exception($data->message ?? "Store coverage data failed", 500);
        }
        return true;
    }

    public function print($id, $lang = 'en')
    {
        $letterHead = request()->letterhead;
        $data = $this->detail($id, $lang);
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('title', 'Travel Quotation');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 27);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('footer-right', 'Page: [page] of [topage]          ');
        $pdf->setOption('footer-font-size', 8);
        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => $data->quotation?->document_no,
                'hasLetterHead' => $letterHead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => $letterHead
            ]),
        ]);
        $pdf->loadView('pdf.quotations.travel', ['data' => $data, 'lang' => $lang]);

        return $pdf->stream("travel_quotation-" . $data->quotation?->document_no . ".pdf");
    }

    public function exportInsuredPerson($id)
    {
        $master = $this->master->findOr($id, fn() => throw new ModelNotFoundException("Quotation not found"));
        return Excel::download(new InsuredPersonExport($master), 'quotation-insured-persons.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}