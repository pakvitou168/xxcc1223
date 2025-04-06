<?php

namespace App\Services\Travel\Policy;

use App\Models\Travel\Policy\DataMaster;
use App\Models\UserManagement\User\UserFile;

class SignatureService
{
    /**
     * Get signature URL for the policy
     * Checks various sources for signature in priority order
     *
     * @param DataMaster $data
     * @return string|null
     */
    public function getSignatureUrl(DataMaster $data): ?string
    {
        // Check quotation approver's signature
        if ($data->quotation && optional($data->quotation)->approved_by ||
            optional($data->quotation)->approved_status === 'APV') {

            $signatureUrl = $this->getUserSignature($data->quotation->approved_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        // Check policy approver's signature
        if ($data->policy && $data->policy->approved_by) {
            $signatureUrl = $this->getUserSignature($data->policy->approved_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        // Check record updater's signature
        if ($data->updated_by) {
            $signatureUrl = $this->getUserSignature($data->updated_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        // Check record creator's signature
        if ($data->created_by) {
            $signatureUrl = $this->getUserSignature($data->created_by);
            if ($signatureUrl) {
                return $signatureUrl;
            }
        }

        // Commented out but kept for reference
        /*if (auth()->check()) {
            return $this->getUserSignature(auth()->id());
        }*/

        return null;
    }

    /**
     * Get user signature URL by user ID
     *
     * @param int|null $userId
     * @return string|null
     */
    private function getUserSignature(?int $userId): ?string
    {
        return !$userId ? null : UserFile::where('user_id', $userId)
            ->where('file_type', 'SIGNATURE')
            ->value('file_url');
    }
}