<?php

namespace App\Http\Controllers\Claim\HS;


use Exception;
use App\Models\RefEnum;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\HS\Claim\ClaimRegisterV;
use App\Services\Claim\HS\HSPaymentService;
use App\Models\HS\Reinsurance\ClaimReinsurance;
use App\Models\HS\Claim\Register\ClaimHSDetail;
use App\Models\HS\Claim\Payment\ClaimTransaction;
use App\Models\HS\Claim\Payment\ClaimTransactionV;

class HSPaymentController extends Controller
{
    use DataTable;
    CONST ERROR_MESSAGE = "Something went wrong!";
    public function __construct(private HSPaymentService $HSPaymentService)
    {
        $this->middleware('has-permission:HS_CLAIM_PAYMENT.VIEW')->only(['index', 'show']);
        $this->middleware('has-permission:HS_CLAIM_PAYMENT.NEW')->only('store');
        $this->middleware('has-permission:HS_CLAIM_PAYMENT.UPDATE')->only('update');
        $this->middleware('has-permission:HS_CLAIM_PAYMENT.APPROVE')->only('approve');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            ClaimTransactionV::whereLangCode('EN')->orderByDesc('txn_id')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $claim = ClaimRegisterV::where('claim_no', $request->input('claim_no'))->first();
            $p_payee_id = $request->input('payee_id');
            $p_payment_type = $request->input('payment_type');
            $p_user_id = auth()->id();
            $response = $this->HSPaymentService->store($claim, $p_payee_id, $p_payment_type, $p_user_id, $request->input('amount'));
            info($response);
            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Record is created.'
                ];
            }
            return response(['message' => self::ERROR_MESSAGE], $response->status());
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->HSPaymentService->getData($id);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $claim_transaction = ClaimTransactionV::where('txn_id', $id)->whereLangCode('EN')->first();
            $claim = ClaimRegisterV::where('claim_no', $claim_transaction->claim_no)->first();
            $claim_hs_detail = ClaimHSDetail::where('id', $claim->claim_detail_id)->first();
            return [
                'claim' => $claim,
                'claim_transaction' => $claim_transaction,
                'claim_hs_detail' => $claim_hs_detail,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $response = $this->HSPaymentService->update($id, $request->all());
            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }
            return response(['message' => self::ERROR_MESSAGE], $response->status());
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function listCauseOfLosses($claimNo)
    {
        try {
            return $this->HSPaymentService->listCauseOfLossesByClaimNo($claimNo);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }

    public function getLov(Request $request)
    {
        return [
            'registers' => $this->HSPaymentService->listRegisters($request->search),
            'payees' => $this->HSPaymentService->listPayees(),
            'payment_types' => RefEnum::listPaymentTypes(),
            'payee_types' => RefEnum::listPayeeTypes(),
        ];
    }

    public function approve(Request $request, $id)
    {
        try {
            if ($this->checkMakerAndApprover($id)) {
                abort(403, "You can not approve your own Payment.");
            }

            $claim = ClaimTransaction::select('claim_id', 'claim_detail_id')
                ->where('approved_status', null)
                ->findOr($id, fn() => abort(404, 'Not found.'));

            $response = $this->HSPaymentService->approve($claim->claim_id, $claim->claim_detail_id, $request->status, $request->comment);

            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Approved successfully.'
                ];
            }

            return response(['message' => self::ERROR_MESSAGE], $response->status());
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => self::ERROR_MESSAGE], 500);
        }
    }

    public function checkMakerAndApprover($id)
    {
        $maker = ClaimTransaction::find($id);
        return $maker->updated_by ? $maker->updated_by == auth()->id() : $maker->created_by == auth()->id();
    }

    public function print($id, $lang)
    {
        App::setLocale($lang);

        $pdf = App::make('snappy.pdf.wrapper');

        // $pdf->setOption('title', 'Discharge Voucher');

        $pdf->setOption('margin-top', 36);
        $pdf->setOption('margin-bottom', 25);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);

        $pdf->setOption('footer-font-size', 8);

        $pdf->setOptions([
            'header-html' => view('pdf.header', [
                'documentNo' => '',
                'hasLetterHead' => request()->letterhead
            ]),
            'footer-html' => view('pdf.footer', [
                'hasLetterHead' => request()->letterhead
            ]),
        ]);
        $data = $this->HSPaymentService->printDetail($id);
        if (request()->print == 'cheque') {
            $reinsurances = ClaimReinsurance::whereClaimNo($data['EN']->claim_no)->get();
            $pdf->setOption('title', 'Claim HS Payment');
            $pdf->loadView('pdf.claims.hs.payment.cheque', ['data' => $data, 'reinsurances' => $reinsurances]);

            return $pdf->stream('Claim HS Cheque Request.pdf');
        } else if (request()->print == 'discharge') {
            $pdf->setOption('title', 'Discharge Voucher');
            $pdf->loadView('pdf.claims.hs.payment.discharge', ['data' => $data]);

            return $pdf->stream('Claim HS Discharge.pdf');
        }
        $pdf->setOption('title', 'Claim HS Payment');
        $pdf->loadView('pdf.claims.hs.payment.payment', ['data' => $data]);

        return $pdf->stream('Claim HS Payment.pdf');
    }
}
