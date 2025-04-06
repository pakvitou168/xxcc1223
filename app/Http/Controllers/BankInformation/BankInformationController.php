<?php

namespace App\Http\Controllers\BankInformation;

use App\Http\Controllers\Controller;
use App\Models\BankInformation\BankInformation;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankInformationController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(BankInformation::class, 'bank_information');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            BankInformation::select('id', 'code', 'type', 'name', 'account_no', 'account_name', 'ccy', 'default')
                ->where('status','ACT')
                ->orderBy('id')
        );
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(BankInformation $bank_information)
    {
        return $bank_information;
    }

    public function store(Request $request)
    {
        $this->validateRequest($request, null);

        $bank_information = new BankInformation();
        $this->assignValues($request, $bank_information);

        if ($bank_information->save()) {
            return [
                'success' => true,
                'message' => 'Bank Informaiton is successfully created.'
            ];
        }
    }

    private function validateRequest($request, $bank_information) {
        $request->validate([
            'code' => [
                'required',
                Rule::unique(BankInformation::class, 'code')->ignore($bank_information)->where('status','ACT')
            ],
            'name' => 'required',
            'account_no' => 'required',
            'account_name' => 'required',
        ]);
    }

    private function assignValues($request, $bank_information) {
        $bank_information->code = $request->post('code');
        $bank_information->name = $request->post('name');
        $bank_information->type = $request->post('type');
        $bank_information->account_no = $request->post('account_no');
        $bank_information->account_name = $request->post('account_name');
        $bank_information->ccy = $request->post('ccy');
        $bank_information->default = $request->post('default');
        $bank_information->status = $request->post('status');
    }

    public function edit(BankInformation $bank_information)
    {
        return $bank_information;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankInformation $bank_information)
    {
        $this->validateRequest($request, $bank_information);
        $this->assignValues($request, $bank_information);

        if ($bank_information->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankInformation $bank_information)
    {
        $bank_information->status = "DEL";
        $bank_information->default = false;

        if ($bank_information->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
