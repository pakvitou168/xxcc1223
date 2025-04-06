<?php

use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

function splitWordsKm($text, $locale = 'km')
{
    $i = IntlBreakIterator::createWordInstance($locale);
    $i->setText($text);
    return collect($i->getPartsIterator())->join('​');
}
function age($dob)
{
    return Carbon::parse($dob)->age;
}


if (!function_exists('response_format')) {
    /**
     * @param $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    function response_format(
        $data = [],
        $message = '',
        $result = true,
        $status = 200
    ): JsonResponse {
        $responseData['result'] = $result;
        $responseData['message'] = $message;
        $responseData['data'] = $data;
        return response()->json($responseData, $status);
    }
}
