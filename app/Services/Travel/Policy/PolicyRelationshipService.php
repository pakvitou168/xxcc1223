<?php

namespace App\Services\Travel\Policy;

class PolicyRelationshipService
{
    /**
     * Get relationships for policy detail
     *
     * @param string $lang
     * @return array
     */
    public function getDetailRelationships(string $lang = 'en'): array
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
                'approved_by',
                'policy_type',
                'business_type',
                'approved_status',
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
            },
            'createdBy',
            'updatedBy'
        ];
    }
}