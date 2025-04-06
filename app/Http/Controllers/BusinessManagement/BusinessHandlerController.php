<?php

namespace App\Http\Controllers\BusinessManagement;

use App\Http\Controllers\Controller;
use App\Models\BusinessManagement\BusinessHandler;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessHandlerController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(BusinessHandler::class, 'business_handler');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(BusinessHandler::where('status', 'ACT')->orderByDesc('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        
        $businessHandler = new BusinessHandler();

        $businessHandler->handler_code = $this->generateHandlerCode();
        $this->assignValues($request, $businessHandler);

        if ($businessHandler->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'title' => 'required|max:25',
            'name' => 'required|max:100',
            'employee_code' => 'max:50',
            'phone' => 'max:500',
            'email' => 'max:500',
            'incentive_rate' => 'numeric|nullable'
        ]);
    }

    private function assignValues($request, $businessHandler) {
        $businessHandler->title = $request->post('title');
        $businessHandler->name = $request->post('name');
        $businessHandler->employee_code = $request->post('employee_code');
        $businessHandler->phone = $request->post('phone');
        $businessHandler->email = $request->post('email');
        $businessHandler->incentive_rate = $request->post('incentive_rate');

    }

    private function generateHandlerCode() {
        return collect(DB::select('select * from ins_generate_handler_code()'))->first()->ins_generate_handler_code;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessManagement\BusinessHandler  $businessHandler
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessHandler $businessHandler)
    {
        return $businessHandler;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessManagement\BusinessHandler  $businessHandler
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessHandler $businessHandler)
    {
        return $businessHandler;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessManagement\BusinessHandler  $businessHandler
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessHandler $businessHandler)
    {
        $this->validateRequest($request);
        
        $this->assignValues($request, $businessHandler);

        if ($businessHandler->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessManagement\BusinessHandler  $businessHandler
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessHandler $businessHandler)
    {
        $businessHandler->status = 'DEL';

        if ($businessHandler->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
