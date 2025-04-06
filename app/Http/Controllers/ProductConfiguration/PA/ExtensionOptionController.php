<?php

namespace App\Http\Controllers\ProductConfiguration\PA;

use App\Http\Controllers\Controller;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtensionOptionController extends Controller
{
    use DataTable;
    private $baseUrl;
    const DEFAULT_LANG = 'EN';

    public function __construct()
    {
        $this->baseUrl = config('pgi.api_insurance_service_url');
    }

    public function index()
    {
        $query = DB::table('ins_pa_extension_option_v')
                ->where('lang_code', 'EN')
                ->select(['id', 'type', 'code', 'name', 'description']);

        return $this->generateTableData($query);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
        'nameKh' => 'nullable|max:255',
        'description' => 'nullable',
        'descriptionKh' => 'nullable'
    ]);

    $data = [
        'name' => trim($request->post('name')),
        'nameKh' => trim($request->post('nameKh', '')),
        'description' => $request->post('description', ''),
        'descriptionKh' => $request->post('descriptionKh', '')
    ];

    // Log the data we're sending to the API
    Log::info('Extension option data being sent to API:', $data);

    try {
        $response = Http::withHeaders([
            'X-User-Id' => auth()->id(),
            'accept' => '*/*'
        ])->post($this->baseUrl . '/pa/extensions', $data);

        // Log the API response
        Log::info('API response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        if ($response->failed()) {
            throw new \Exception('API Error: ' . $response->body());
        }

        return response([
            'success' => true,
            'message' => 'Record is created.'
        ], 201);
    } catch (\Exception $e) {
        return response([
            'success' => false,
            'message' => 'Failed to create record: ' . $e->getMessage()
        ], 500);
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
        $enData = DB::table('ins_pa_extension_option_v')
                  ->where('id', $id)
                  ->where('lang_code', 'EN')
                  ->first();

        Log::info("English data for ID $id:", ['data' => $enData]);

        $khData = DB::table('ins_pa_extension_option_v')
                  ->where('id', $id)
                  ->where('lang_code', 'KM')
                  ->first();

        Log::info("Khmer data for ID $id:", ['data' => $khData]);

        if (!$enData) {
            return response()->json(['error' => 'Extension option not found'], 404);
        }

        $allDataForId = DB::table('ins_pa_extension_option_v')
                          ->where('id', $id)
                          ->get();

        Log::info("All language rows for ID $id:", ['count' => count($allDataForId), 'data' => $allDataForId]);

        $response = [
            'id' => $enData->id,
            'type' => $enData->type,
            'code' => $enData->code,
            'name' => $enData->name,
            'description' => $enData->description,
            'nameKh' => $khData ? $khData->name : null,
            'descriptionKh' => $khData ? $khData->description : null,
        ];

        Log::info("Final response:", ['response' => $response]);

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'nameKh' => 'nullable|max:255',
            'description' => 'nullable',
            'descriptionKh' => 'nullable'
        ]);

        $data = [
            'name' => trim($request->post('name')),
            'nameKh' => trim($request->post('nameKh', '')),
            'description' => $request->post('description', ''),
            'descriptionKh' => $request->post('descriptionKh', '')
        ];

        try {
            $response = Http::withHeaders([
                'X-User-Id' => auth()->id(),
                'accept' => '*/*'
            ])->put($this->baseUrl . '/pa/extensions/' . $id, $data);

            if ($response->failed()) {
                throw new \Exception('API Error: ' . $response->body());
            }

            return [
                'success' => true,
                'message' => 'Record is updated.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update record: ' . $e->getMessage()
            ];
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::withHeaders([
                'X-User-Id' => auth()->id(),
                'accept' => '*/*'
            ])->delete($this->baseUrl . '/pa/extensions/' . $id);

            if ($response->failed()) {
                throw new \Exception('API Error: ' . $response->body());
            }

            return [
                'success' => true,
                'message' => 'Record is deleted.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete record: ' . $e->getMessage()
            ];
        }
    }
}
