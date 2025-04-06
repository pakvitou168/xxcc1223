<?php

namespace App\Http\Controllers\CustomerManagement;

use App\Http\Controllers\Controller;
use App\Models\CustomerManagement\Country;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Country::class, 'country');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(
            Country::where('status', 'ACT')
                ->latest('id')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request, null);

        $country = new Country();
        $this->assignValues($request, $country);

        if ($country->save())
            return response([
                'success' => true,
                'message' => 'Record is created.'
            ], 201);
    }

    private function validateRequest($request, $model) {
        $request->validate([
            'country_code' => [
                'required',
                'max:2',
                Rule::unique(Country::class, 'country_code')
                    ->ignore($model)
                    ->where('status', 'ACT'),
            ],
            'alt_country_code' => 'required|max:3',
            'isd_code' => 'numeric',
        ]);
    }

    private function assignValues($request, $country) {
        $country->country_code = $request->country_code;
        $country->description = $request->description;
        $country->alt_country_code = $request->alt_country_code;
        $country->isd_code = $request->isd_code;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerManagement\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        return $country;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerManagement\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        $this->validateRequest($request, $country);

        $this->assignValues($request, $country);

        if ($country->save())
            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerManagement\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        $country->status = "DEL";
        
        if ($country->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
