<?php

namespace App\Services\Travel\Policy;

use App\Models\RecordStatus;
use App\Models\Travel\DeductibleData;
use App\Models\Travel\Policy\DataMaster;
use Carbon\Carbon;
use KhmerDateTime\KhmerDateTime;

class PolicyFormatterService
{
    /**
     * @param SignatureService $signatureService
     */
    public function __construct(
        private SignatureService $signatureService
    ) {
    }

    /**
     * Format policy detail data
     *
     * @param DataMaster $data
     * @param string $lang
     * @return DataMaster
     */
    public function formatPolicyDetail(DataMaster $data, string $lang = 'en'): DataMaster
    {
        // Format customer address
        $data->customer->address = $data->customer?->correspondence_address;

        // Format insurance period
        $data->insurance_period = $this->formatInsurancePeriod($data, $lang);

        // Process coverage data
        $this->processCoverageData($data);

        // Format issue date
        $data->issued_on = $this->formatDate($data->updated_at, $lang);

        // Get signature URL
        $data->signature = $this->signatureService->getSignatureUrl($data);

        // Get issuer name
        $data->issued_by = $this->getIssuerName($data);

        $data->total_insured_persons = $this->getTotalInsuredPersons($data);
        $data->deductibles =  DeductibleData::where('data_id',$data->id)->get();


        return $data;
    }

    /**
     * Process coverage data by categorizing them
     *
     * @param DataMaster $data
     * @return void
     */
    private function processCoverageData(DataMaster $data): void
    {
        $data->coverage_standard = collect();
        $data->coverage_additional = collect();

        $limitTypes = ['standard', 'deluxe', 'executive'];

        foreach ($limitTypes as $type) {
            $data->{"show_{$type}_limit"} = false;
        }

        if (!$data->coverage || $data->coverage->isEmpty()) {
            return;
        }

        $coverage = clone $data->coverage;

        $data->coverage_standard = $coverage->where('category', 'STANDARD')->values();
        $data->coverage_additional = $coverage->where('category', 'ADDITIONAL')->values();

        if ($data->coverage_standard->isNotEmpty()) {
            foreach ($limitTypes as $type) {
                $limitField = "{$type}_limit";

                $data->{"show_{$type}_limit"} = $data->coverage_standard->every(
                    fn($item) => isset($item->$limitField) && $item->$limitField !== null
                );
            }
        }
    }

    /**
     * Format date based on language
     *
     * @param mixed $date
     * @param string $lang
     * @return string
     */
    private function formatDate($date, string $lang = 'en'): string
    {
        if ($lang === 'en') {
            return Carbon::parse($date)->format('d F Y');
        }

        return KhmerDateTime::parse($date)->format('LL');
    }

    /**
     * Get issuer name from updater or creator
     *
     * @param DataMaster $data
     * @return string|null
     */
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

    /**
     * Format insurance period text
     *
     * @param DataMaster $master
     * @param string $lang
     * @return string
     */
    private function formatInsurancePeriod(DataMaster $master, string $lang = 'en'): string
    {
        $dateFormat = $lang === 'en' ? 'd M Y' : 'LL';

        $from = $lang === 'en'
            ? Carbon::parse($master->effective_date_from)->format($dateFormat)
            : KhmerDateTime::parse($master->effective_date_from)->format($dateFormat);

        $to = $lang === 'en'
            ? Carbon::parse($master->effective_date_to)->format($dateFormat)
            : KhmerDateTime::parse($master->effective_date_to)->format($dateFormat);

        $startingDateText = trans('Starting Date', [], $lang);
        $returningDateText = trans('Returning Date', [], $lang);
        $daysText = trans('Days', [], $lang);

        return sprintf(
            '%s: <span class="font-semibold">%s</span> %s: <span class="font-semibold">%s</span> %s: <span class="font-semibold">%d</span>',
            $startingDateText,
            $from,
            $returningDateText,
            $to,
            $daysText,
            $master->effective_day
        );
    }

    private function getTotalInsuredPersons(DataMaster $data): ?string
    {
        $data->loadCount([
            'dataDetails' => function($query) {
                $query->where('status', RecordStatus::ACTIVE);
            },
        ]);

        return $data->data_details_count;
    }
}