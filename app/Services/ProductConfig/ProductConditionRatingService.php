<?php
namespace App\Services\ProductConfig;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\ProductConfiguration\ProductConditionRating;

class ProductConditionRatingService {

  public function __construct(private ProductConditionRating $ProductConditionRating){}

  public function save($formValues): JsonResponse
  {
    $result = $this->ProductConditionRating->create([
      'product_code' => $formValues->product_code,
      'code' => $formValues->code,
      'description' => $formValues->description,
      'key' => $formValues->key,
      'value' => $formValues->value,
      'cond_expr' => $formValues->cond_expr,
      'cond_type' => $formValues->cond_type
    ]);

    if($result){
      return response()->json(['success' => true, 'message' => 'Create Product Condition Rating successfully', 'data' => $formValues], 200);
    }else{
      return response()->json(['success' => false, 'message' => 'Something went wrong..!', 'data' => $formValues], 500);
    }
  }

  public function update($formValues, $id)
  {
    $result = $this->ProductConditionRating->findOr($id, fn() => throw new Exception("product condition rating not found"))
      ->update([
        'product_code' => $formValues->product_code,
        'code' => $formValues->code,
        'description' => $formValues->description,
        'key' => $formValues->key,
        'value' => $formValues->value,
        'cond_expr' => $formValues->cond_expr,
        'cond_type' => $formValues->cond_type
      ]);
    if($result){
      return response()->json(['success' => true, 'message' => 'Create Product Condition Rating successfully', 'data' => $formValues], 200);
    }else{
      return response()->json(['success' => false, 'message' => 'Something went wrong..!', 'data' => $formValues], 500);
    }
  }

  public function delete($id)
  {
    $condition_rating = $this->ProductConditionRating->findOr($id, fn() => throw new Exception("product condition rating not found"));
    $condition_rating->status = 'DEL';
    $condition_rating->update();
    return $condition_rating;
  }
}