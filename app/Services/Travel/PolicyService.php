<?php

namespace App\Services\Travel;

use App\Models\RecordStatus;
use App\Models\Travel\DataMaster;
use App\Models\Travel\PolicyV;
use App\Models\UserManagement\User\UserFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use KhmerDateTime\KhmerDateTime;

class PolicyService
{
    /**
     * @param DataMaster $master
     * @param PolicyV $policyV
     */
    public function __construct(private DataMaster $master, private PolicyV $policyV)
    {
    }

    /**
     * Get policy list query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function list()
    {
        return $this->policyV->query();
    }

    public function delete($id)
    {
        return $this->master->where('id', $id)->update([
            'status' => RecordStatus::DELETED
        ]);
    }
    private function getDetailRelationships(string $lang = 'en'): array
    {
        $isEN = $lang === 'en';

        return [
            'policy' => fn($q) => $q->select(
                'data_id',
                'document_no',
                'policy_no',
                'status',
                'created_by',
                'updated_by',
                'id',
                'approved_by'
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
            'coverage' => fn($q) => $q->where('lang_code', $isEN ? 'EN' : 'KM'),
            'quotation' => function ($query) {
                $query->select(
                    'id',
                    'data_id',
                    'quotation_no',
                    'document_no',
                    'created_at',
                    'approved_status',
                    'approved_by',
                    'accepted_status',
                    'accepted_by'
                )->with('policy:quotation_id');
            }
        ];
    }
    public function detail($id, $lang = 'en'): DataMaster
    {
        $query = $this->master->with($this->getDetailRelationships($lang));

        $data = $this->findPolicyById($query, $id);
        if (!$data) {
            throw new ModelNotFoundException("Policy not found", 404);
        }
        $data->customer->address = $data->customer?->correspondence_address;
        $data->insurance_period = $this->insurancePeriod($data, $lang);
        $this->processCoverageData($data);
        $data->issued_on = $this->formatDate($data->updated_at, $lang);
        $data->signature = $this->getSignatureUrl($data);
        $data->issued_by = $this->getIssuerName($data);

        return $data;
    }
    private function findPolicyById($query, $id)
    {
        return $query->find($id);
    }
    private function processCoverageData(DataMaster $data): void
    {
        $allCoverages = $data->coverage;

        $data->coverageStandard = $allCoverages->where('category', 'STANDARD')->values();

        $data->coverageAdditional = $allCoverages->where('category', 'ADDITIONAL')->values();

        unset($data->coverage);
    }
    private function formatDate($date, string $lang = 'en'): string
    {
        if ($lang === 'en') {
            return Carbon::parse($date)->format('d F Y');
        }

        return KhmerDateTime::parse($date)->format('LL');
    }
    private function getIssuerName(DataMaster $data): ?string
    {
        if ($data->updatedBy) {
            return $data->updatedBy->full_name;
        }

        if ($data->createdBy) {
            return $data->createdBy->full_name;
        }

        return null;
    }
    private function getSignatureUrl(DataMaster $data): ?string
    {
        if ($data->quotation && optional($data->quotation)->approved_by ||
            optional($data->quotation)->approved_status === 'APV') {

            $signatureUrl = $this->getUserSignature($data->quotation->approved_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        if ($data->policy && $data->policy->approved_by) {
            $signatureUrl = $this->getUserSignature($data->policy->approved_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        if ($data->updated_by) {
            $signatureUrl = $this->getUserSignature($data->updated_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        if ($data->created_by) {
            $signatureUrl = $this->getUserSignature($data->created_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        /*if (auth()->check()) {
            return $this->getUserSignature(auth()->id());
        }*/

        return null;
    }
    private function getUserSignature(?int $userId): ?string
    {
        return  !$userId? null: UserFile::where('user_id', $userId)
            ->where('file_type', 'SIGNATURE')
            ->value('file_url');
    }

    private function insurancePeriod(DataMaster $master, string $lang = 'en'): string
    {
        $from = $this->formatDate($master->effective_date_from, $lang);
        $to = $this->formatDate($master->effective_date_to, $lang);

        $daysText = trans('Days', [], $lang);
        $fromText = trans('From', [], $lang);
        $toText = trans('To', [], $lang);
        $inclusiveText = trans('Both Days Inclusive', [], $lang);

        return sprintf(
            '%d %s - %s %s %s %s (%s)',
            $master->effective_day,
            $daysText,
            $fromText,
            $from,
            $toText,
            $to,
            $inclusiveText
        );
    }
}