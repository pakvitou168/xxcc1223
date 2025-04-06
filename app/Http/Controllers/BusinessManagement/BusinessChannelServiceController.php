<?php

namespace App\Http\Controllers\BusinessManagement;

use App\Http\Controllers\Controller;
use App\Models\BusinessManagement\BusinessCategory;
use App\Models\BusinessManagement\BusinessHandler;
use App\Models\RefEnum\RefEnum;
use Illuminate\Support\Facades\DB;

class BusinessChannelServiceController extends Controller
{
    public function listBusinessCategories()
    {
        $categories = BusinessCategory::select('id', 'name')
            ->where('status', 'ACT')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

        return  $categories;
    }

    public function listSaleChannels(): array
    {
        $channels = RefEnum::where('group_id', 'BUSINESS_CHANNEL')
            ->where('type_id', 'SALE_CHANNEL')
            ->select('name as label', 'enum_id as value')
            ->get()
            ->toArray();

        return $channels;
    }

    public function listBusinessHandlers(): array
    {
        $handlers = BusinessHandler::where('status', 'ACT')
            ->select('name as label', 'handler_code as value')
            ->get()
            ->toArray();

        return  $handlers;
    }

    public function listParents(): array
    {
        $parents = DB::table('ins_business_channel_pc_v')
            ->select('business_code', 'full_name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->business_code,
                    'label' => $item->full_name,
                ];
            })
            ->toArray();

        return $parents;
    }
}
