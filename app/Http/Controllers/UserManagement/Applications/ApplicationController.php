<?php

namespace App\Http\Controllers\UserManagement\Applications;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Application;
use App\Traits\DataTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    use DataTable;

    public function __construct()
    {
        $this->authorizeResource(Application::class, 'application');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->generateTableData(Application::where('status', 'ACT')->latest('id'));
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

        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])
                ->post(config('pgi.api_base_url') . 'v1/application', $this->getRequestBody($request))->throw();

                info($response->body());

                if ($response->successful()) {
                    return response([
                        'success' => true,
                        'message' => 'Record is created.'
                    ], 201);
                }
        } catch(Exception $e) {
            info($e);
            return $e;
        }

    }

    private function validateRequest($request, $model) {
        $request->validate([
            'code' => [
                'required',
                'max:10',
                Rule::unique(Application::class, 'code')->ignore($model)->where('status', 'ACT')
            ],
            'name' => 'required|max:50',
            'description' => 'max:250',
            'level' => 'required'
        ]);
    }

    private function getRequestBody($request) {

        $status = 'ACT';

        return [
            "code" => $request->post('code'),
            "name" => $request->post('name'),
            "description" => $request->post('description'),
            "level" => $request->post('level'),
            "allow_change_username" => $request->get('allow_change_username') ? 'Y' : 'N',
            "status" => $status
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserManagement\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])
                ->get(config('pgi.api_base_url') . 'v1/application/' . $application->code)->throw();

            info($response->body());

            if ($response->successful()) {
                return response($response->body());
            }

        } catch(Exception $e) {
            info($e);
            return $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserManagement\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        $this->validateRequest($request, $application);

        try {
            $response = Http::withHeaders(['User-Id' => auth()->id()])
                ->put(config('pgi.api_base_url') . 'v1/application/' . $application->code, $this->getRequestBody($request))->throw();

            info($response->body());

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Record is updated.'
                ];
            }

        } catch(Exception $e) {
            info($e);
            return $e;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserManagement\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        $application->status = "DEL";
        
        if ($application->save()) {
            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        }
    }
}
