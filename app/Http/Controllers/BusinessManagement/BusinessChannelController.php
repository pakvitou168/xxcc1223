<?php

namespace App\Http\Controllers\BusinessManagement;

use App\Http\Controllers\Controller;
use App\Models\BusinessManagement\BusinessChannel;
use App\Models\RefEnum;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessChannelController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(BusinessChannel::class, 'business_channel');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->generateTableData(
            BusinessChannel::with('businessCategory:id,name')
                ->with('businessHandler:handler_code,name')->where('status', 'ACT')
                ->orderByDesc('id')
        );
        $saleChannels = RefEnum::select('name', 'enum_id')
            ->where('group_id', 'BUSINESS_CHANNEL')
            ->where('type_id', 'SALE_CHANNEL')
            ->get();

        $data = json_decode($data->content());
        collect($data->data)->transform(function($item) use ($saleChannels) {
            $saleChannelName = $saleChannels->where('enum_id', $item->sale_channel)->value('name');
            $item->sale_channel_name = $saleChannelName;

            return $item;
        });
        return $data;
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
        
        $businessChannel = new BusinessChannel();
        $businessChannel->business_code = $this->generateBusinessCode($request->business_category_id);
        $this->assignValues($request, $businessChannel);

        if ($businessChannel->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function validateRequest($request) {
        $request->validate([
            'business_category_id' => 'required',
            'full_name' => 'required|max:500',
            'sale_channel' => 'required',
            'commission_rate' => 'numeric|nullable',
            'handler_code' => 'required',
            'contact_phone' => 'max:500',
            'contact_email' => 'max:500',
            'premium_tax_fee_rate' => 'required',
            'witholding_tax_rate' => 'required'
        ]);
    }

    private function assignValues($request, $businessChannel) {
        $businessChannel->business_category_id = $request->post('business_category_id');
        $businessChannel->full_name = $request->post('full_name');
        $businessChannel->sale_channel = $request->post('sale_channel');
        $businessChannel->commission_rate = $request->post('commission_rate');
        $businessChannel->handler_code = $request->post('handler_code');
        $businessChannel->contact_phone = $request->post('contact_phone');
        $businessChannel->contact_email = $request->post('contact_email');
        $businessChannel->contact_address = $request->post('contact_address');
        $businessChannel->parent_code = $request->post('parent_code');
        $businessChannel->premium_tax_fee_rate = $request->post('premium_tax_fee_rate');
        $businessChannel->witholding_tax_rate = $request->post('witholding_tax_rate');
    }

    private function generateBusinessCode($businessCategoryId) {
        return collect(DB::select('select * from ins_generate_business_code(' . $businessCategoryId . ') as business_code'))->first()->business_code;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessManagement\BusinessChannel  $businessChannel
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessChannel $businessChannel)
    {
        $businessChannel->load('businessCategory')
            ->load('businessHandler')
            ->load('parentChannel');

        $businessChanelName = RefEnum::where('group_id', 'BUSINESS_CHANNEL')
        ->where('type_id', 'SALE_CHANNEL')
        ->where('enum_id', $businessChannel->sale_channel)
        ->value('name');

        $businessChannel->sale_channel_name = $businessChanelName;

        return $businessChannel;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessManagement\BusinessChannel  $businessChannel
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessChannel $businessChannel)
    {
        return $businessChannel;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessManagement\BusinessChannel  $businessChannel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessChannel $businessChannel)
    {
        $this->validateRequest($request);

        $this->assignValues($request, $businessChannel);

        if ($businessChannel->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessManagement\BusinessChannel  $businessChannel
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessChannel $businessChannel)
    {
        $businessChannel->status = 'DEL';

        if ($businessChannel->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
