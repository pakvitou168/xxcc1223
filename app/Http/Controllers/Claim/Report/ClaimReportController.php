<?php

namespace App\Http\Controllers\Claim\Report;

use App\Exports\ClaimInCurredExport;
use App\Exports\ClaimOutstandingExport;
use App\Exports\ClaimPaidExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClaimReportController extends Controller
{
    public function exportClaim(Request $request)
    {
        if ($request->route('report_type') == 'ClaimsPaid') {
            return Excel::download(new ClaimPaidExport($request->route('from_date'), $request->route('to_date')), 'Claim Paid.xlsx');
        }
        if ($request->route('report_type') == 'ClaimsOutstanding') {
            return Excel::download(new ClaimOutstandingExport($request->route('from_date'), $request->route('to_date')), 'Claim Outstanding.xlsx');
        }
        if ($request->route('report_type') == 'ClaimsIncurred') {
            return Excel::download(new ClaimInCurredExport($request->route('from_date'), $request->route('to_date')), 'Claim Incurred.xlsx');
        }
    }
}
