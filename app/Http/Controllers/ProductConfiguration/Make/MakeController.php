<?php

namespace App\Http\Controllers\ProductConfiguration\Make;

use App\Http\Controllers\Controller;
use App\Models\Make\Make;
use App\Traits\DataTable;
use Illuminate\Http\Request;

class MakeController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Make::class, 'make');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Make::where('status', 'ACT')->latest('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'make' => 'required|max:255',
            'description' =>'max:255'
        ]);

        $make = new Make();
        $this->assignValues($request, $make);

        if ($make->save()) {
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
        }
    }

    private function assignValues($request, $make) {
        $availableOffline = $request->post('available_offline') ?? false;
        $availableOnline = $request->post('available_online') ?? false;

        $make->make = trim($request->post('make'));
        $make->description = $request->post('description');
        $make->available_offline = $availableOffline ? 'Y' : 'N';
        $make->available_online = $availableOnline ? 'Y' : 'N';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Make\Make  $make
     * @return \Illuminate\Http\Response
     */
    public function show(Make $make)
    {
        $make->available_offline = $make->available_offline == 'Y' ? true : false;
        $make->available_online = $make->available_online == 'Y' ? true : false;
        return $make;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Make\Make  $make
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Make $make)
    {
        $request->validate([
            'make' => 'required|max:255',
            'description' =>'max:255'
        ]);

        $this->assignValues($request, $make);

        if ($make->save()) {
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Make\Make  $make
     * @return \Illuminate\Http\Response
     */
    public function destroy(Make $make)
    {
        $make->status = "DEL";

        if ($make->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
