<?php

namespace App\Http\Controllers\ProductConfiguration\ProductConditionRating;

use Exception;
use App\Traits\DataTable;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductConfiguration\ProductConditionRating;
use App\Services\ProductConfig\ProductConditionRatingService;

class ProductConditionRatingController extends Controller
{
  use DataTable;
  public function __construct(private ProductConditionRatingService $ProductConditionRating )
  {
    $this->middleware('has-permission:PRODUCT_CONDITION_RATING.VIEW')->only(['index', 'show']);
    $this->middleware('has-permission:PRODUCT_CONDITION_RATING.NEW')->only('store');
    $this->middleware('has-permission:PRODUCT_CONDITION_RATING.UPD')->only('update');
    $this->middleware('has-permission:PRODUCT_CONDITION_RATING.APV')->only('approve');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return $this->generateTableData(
      ProductConditionRating::where('status', '<>', 'DEL')->orderByDesc('id')
    );
  }

  public function listProducts()
  {
    return Product::where('status', 'ACT')
      ->orderBy('code')
      ->get()
      ->map(function($item) {
        return [
          'label' => $item->code . ' - ' . $item->name,
          'value' => $item->code,
          'desc' => $item->description,
        ];
      });
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
    try{
      $response = $this->ProductConditionRating->save($request);
      if ($response->status() === 200) {
        return [
          'success' => true,
          'message' => 'Record is created.',
        ];
      }
      return response(['message' => 'Something  went wrong!'], $response->status());
    } catch (Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
    try{
      return ProductConditionRating::where('id', $id)->first();
    } catch (Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
    //
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
      $response = $this->ProductConditionRating->update($request, $id);
      return response()->json(['success' => true, 'message' => 'Product Condition Rating update successfully', 'data' => $response]);
    } catch (Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    try {
      $response = $this->ProductConditionRating->delete($id);
      return response()->json(['success' => true, 'response' => $response, 'message' => 'Product Condition Rating Delete successfully']);
    } catch (Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }
}
