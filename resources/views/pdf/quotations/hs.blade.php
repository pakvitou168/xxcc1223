@extends('pdf.layout')

@section('content')
@php 
    $is_en = App::getLocale() !== 'km';
    $hasTotalOptional = ($hs['optional_total_premium']?->plan_1 + $hs['optional_total_premium']?->plan_2 + $hs['optional_total_premium']?->plan_3 + $hs['optional_total_premium']?->plan_4 + $hs['optional_total_premium']?->plan_5) > 0;
@endphp

<body class="{{ !$is_en ? 'font-khmer-os' : 'font-candara' }} mb-0">
    <div class="text-center">
        <div class="pt-1">
            <div
                class="{{ !$is_en ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-title font-bold text-center uppercase">
                @if (!$is_en)
                    {{ $hs['product']?->name_kh }}
                @else
                    {{ $hs['product']?->name }}
                @endif
                <div class="pt-2" id="testing1">{{__('Insurance Quotation') }}</div>
            </div>
        </div>
        <div class="flex flex-col py-2">
            <div class="text-right">
                <div class="mt-1">{{__('Quotation No')}} : <span>{{ $hs['quotation_no'] }}</span></div>
                <div class="mt-1">{{__('Business Code')}} : <span>{{ $hs['business_code'] }}</span></div>
            </div>
        </div>
    </div>
    <div class="pt-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('The Insured Name')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">{{ $is_en ? $hs['insured_name'] : $hs['insured_name_kh'] }}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Correspondence Address')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-khmer">
                    <span>{{$hs['correspondence_address']}}</span>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Business / Occupation')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                    @if ($is_en)
                        {{ $hs['business_occupation'] }}
                    @else
                        {{ $hs['business_occupation_kh'] }}
                    @endif
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Period Of Insurance')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $hs['period_of_insurance'] }}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Coverage')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="avoid-break">
                    {!! $hs['coverage'] !!}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Policy Wording')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class=" mb-3">
                    {{__('Subject to Product Policy Wording', [
    'productPolicyWording' => __('Product Policy Wording', ['productName' => !$is_en ? optional($hs['product'])->name_kh : optional($hs['product'])->name])
])}} ({{ $hs['policy_wording_version'] }})
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Geographical Limit')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">
                @if ($is_en)
                    {{ @$hs['geographical_limit']['clause'] }}
                @else
                    {{ @$hs['geographical_limit']['clause_kh'] }}
                @endif
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Insured Persons')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">
                @if ($is_en)
                    {!!$hs['insured_person_note']!!}
                @else
                    {!!$hs['insured_person_note_kh']!!}
                @endif
            </div>
        </div>
    </div>
    <div class="pt-1 pb-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Schedule Of Benefits')}}</div>
            </div>
        </div>
        @if ($hs['standard_benefits']->count() >= 1)
            <table class="border-collapse w-full font-size-content">
                <thead style="display: table-row-group;">
                    <tr>
                        <th class="p-1">{{__('Item')}}</th>
                        <th class="p-1">{{__('Benefits')}}</th>
                        <th class="p-1">{{__('Number of Days')}}</th>
                        @if($hs['standard_total_premium']?->plan_1)
                        <th class="p-1">{{__('Plan I (USD)')}}</th>@endif
                        @if($hs['standard_total_premium']?->plan_2)
                        <th class="p-1">{{__('Plan II (USD)')}}</th>@endif
                        @if($hs['standard_total_premium']?->plan_3)
                        <th class="p-1">{{__('Plan III (USD)')}}</th>@endif
                        @if($hs['standard_total_premium']?->plan_4)
                        <th class="p-1">{{__('Plan IV (USD)')}}</th>@endif
                        @if($hs['standard_total_premium']?->plan_5)
                        <th class="p-1">{{__('Plan V (USD)')}}</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($hs['standard_benefits'] as $index => $item)
                        <tr>
                            <td class="p-1 text-center">{{ $index + 1 }}</td>
                            <td class="p-1">{{ $is_en ? $item?->name : splitWordsKm($item?->name) }}</td>
                            <td class="p-1">{{ $item?->amount ? $item?->amount . __('Days') : '' }}</td>
                            @if($hs['standard_total_premium']?->plan_1)
                            <td class="p-1 text-right">{{ number_format($item?->plan_1, 2) }}</td>@endif
                            @if($hs['standard_total_premium']?->plan_2)
                            <td class="p-1 text-right">{{ number_format($item?->plan_2, 2) }}</td>@endif
                            @if($hs['standard_total_premium']?->plan_3)
                            <td class="p-1 text-right">{{ number_format($item?->plan_3, 2) }}</td>@endif
                            @if($hs['standard_total_premium']?->plan_4)
                            <td class="p-1 text-right">{{ number_format($item?->plan_4, 2) }}</td>@endif
                            @if($hs['standard_total_premium']?->plan_5)
                            <td class="p-1 text-right">{{ number_format($item?->plan_5, 2) }}</td>@endif
                        </tr>
                    @endforeach
                    @if($hs['standard_base_plan_amount'])
                    <tr>
                        <td colspan="3" class="p-1 text-right">
                            {{$is_en ? $hs['standard_base_plan_amount']?->name : splitWordsKm($hs['standard_base_plan_amount']?->name) }}
                        </td>
                        @if($hs['standard_total_premium']?->plan_1)
                            <td class="p-1 text-right">{{number_format($hs['standard_base_plan_amount']?->plan_1, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_2)
                            <td class="p-1 text-right">{{number_format($hs['standard_base_plan_amount']?->plan_2, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_3)
                            <td class="p-1 text-right">{{number_format($hs['standard_base_plan_amount']?->plan_3, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_4)
                            <td class="p-1 text-right">{{number_format($hs['standard_base_plan_amount']?->plan_4, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_5)
                            <td class="p-1 text-right">{{number_format($hs['standard_base_plan_amount']?->plan_5, 2) }}</td>
                        @endif
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="p-1 text-right">
                            {{$is_en ? $hs['standard_premium_per_person']?->name : splitWordsKm($hs['standard_premium_per_person']?->name) }}
                        </td>
                        @if($hs['standard_total_premium']?->plan_1)
                            <td class="p-1 text-right">{{number_format($hs['standard_premium_per_person']?->plan_1, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_2)
                            <td class="p-1 text-right">{{number_format($hs['standard_premium_per_person']?->plan_2, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_3)
                            <td class="p-1 text-right">{{number_format($hs['standard_premium_per_person']?->plan_3, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_4)
                            <td class="p-1 text-right">{{number_format($hs['standard_premium_per_person']?->plan_4, 2) }}</td>
                        @endif
                        @if($hs['standard_total_premium']?->plan_5)
                            <td class="p-1 text-right">{{number_format($hs['standard_premium_per_person']?->plan_5, 2) }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="3" class="p-1 text-right">
                            {{$is_en ? $hs['standard_total_premium']?->name : splitWordsKm($hs['standard_total_premium']?->name) }}
                        </td>
                        @if($hs['standard_total_premium']?->plan_1)
                        <td class="p-1 text-right">{{number_format($hs['standard_total_premium']?->plan_1, 2) }}</td>@endif
                        @if($hs['standard_total_premium']?->plan_2)
                        <td class="p-1 text-right">{{number_format($hs['standard_total_premium']?->plan_2, 2) }}</td>@endif
                        @if($hs['standard_total_premium']?->plan_3)
                        <td class="p-1 text-right">{{number_format($hs['standard_total_premium']?->plan_3, 2) }}</td>@endif
                        @if($hs['standard_total_premium']?->plan_4)
                        <td class="p-1 text-right">{{number_format($hs['standard_total_premium']?->plan_4, 2) }}</td>@endif
                        @if($hs['standard_total_premium']?->plan_5)
                        <td class="p-1 text-right">{{number_format($hs['standard_total_premium']?->plan_5, 2) }}</td>@endif
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
    @if($hasTotalOptional)
        <div class="pt-1 pb-3 avoid-break">
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Optional Extensions')}}</div>
                </div>
            </div>
            @if ($hs['optional_benefits']->count() >= 1)
                <table class="border-collapse w-full font-size-content">
                    <thead>
                        <tr>
                            <th class="p-1">{{__('Item')}}</th>
                            <th class="p-1">{{__('Benefits')}}</th>
                            @if($hs['optional_total_premium']?->plan_1)
                            <th class="p-1">{{__('Limit Plan I')}}</th>@endif
                            @if($hs['optional_total_premium']?->plan_2)
                            <th class="p-1">{{__('Limit Plan II')}}</th>@endif
                            @if($hs['optional_total_premium']?->plan_3)
                            <th class="p-1">{{__('Limit Plan III')}}</th>@endif
                            @if($hs['optional_total_premium']?->plan_4)
                            <th class="p-1">{{__('Limit Plan IV')}}</th>@endif
                            @if($hs['optional_total_premium']?->plan_5)
                            <th class="p-1">{{__('Limit Plan V')}}</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hs['optional_benefits'] as $index => $item)
                            <tr>
                                <td class="p-1 text-center">{{ $index + 1 }}</td>
                                <td class="p-1" style="word-break:break-word;">
                                    {{ $is_en ? $item?->name : splitWordsKm($item?->name) }}
                                </td>
                                @if($hs['optional_total_premium']?->plan_1)
                                <td class="p-1 text-right">{{ number_format($item?->plan_1, 2) }}</td>@endif
                                @if($hs['optional_total_premium']?->plan_2)
                                <td class="p-1 text-right">{{ number_format($item?->plan_2, 2) }}</td>@endif
                                @if($hs['optional_total_premium']?->plan_3)
                                <td class="p-1 text-right">{{ number_format($item?->plan_3, 2) }}</td>@endif
                                @if($hs['optional_total_premium']?->plan_4)
                                <td class="p-1 text-right">{{ number_format($item?->plan_4, 2) }}</td>@endif
                                @if($hs['optional_total_premium']?->plan_5)
                                <td class="p-1 text-right">{{ number_format($item?->plan_5, 2) }}</td>@endif
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="p-1 text-right">
                                {{ $is_en ? $hs['optional_premium_per_person']?->name : splitWordsKm($hs['optional_premium_per_person']?->name) }}
                            </td>
                            @if($hs['optional_total_premium']?->plan_1)
                                <td class="p-1 text-right">{{ number_format($hs['optional_premium_per_person']?->plan_1, 2) }}</td>
                            @endif
                            @if($hs['optional_total_premium']?->plan_2)
                                <td class="p-1 text-right">{{ number_format($hs['optional_premium_per_person']?->plan_2, 2) }}</td>
                            @endif
                            @if($hs['optional_total_premium']?->plan_3)
                                <td class="p-1 text-right">{{ number_format($hs['optional_premium_per_person']?->plan_3, 2) }}</td>
                            @endif
                            @if($hs['optional_total_premium']?->plan_4)
                                <td class="p-1 text-right">{{ number_format($hs['optional_premium_per_person']?->plan_4, 2) }}</td>
                            @endif
                            @if($hs['optional_total_premium']?->plan_5)
                                <td class="p-1 text-right">{{ number_format($hs['optional_premium_per_person']?->plan_5, 2) }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="2" class="p-1 text-right">
                                {{ $is_en ? $hs['optional_total_premium']?->name : splitWordsKm($hs['optional_total_premium']?->name) }}
                            </td>
                            @if($hs['optional_total_premium']?->plan_1)
                            <td class="p-1 text-right">{{ number_format($hs['optional_total_premium']?->plan_1, 2) }}</td>@endif
                            @if($hs['optional_total_premium']?->plan_2)
                            <td class="p-1 text-right">{{ number_format($hs['optional_total_premium']?->plan_2, 2) }}</td>@endif
                            @if($hs['optional_total_premium']?->plan_3)
                            <td class="p-1 text-right">{{ number_format($hs['optional_total_premium']?->plan_3, 2) }}</td>@endif
                            @if($hs['optional_total_premium']?->plan_4)
                            <td class="p-1 text-right">{{ number_format($hs['optional_total_premium']?->plan_4, 2) }}</td>@endif
                            @if($hs['optional_total_premium']?->plan_5)
                            <td class="p-1 text-right">{{ number_format($hs['optional_total_premium']?->plan_5, 2) }}</td>@endif
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    @endif
    @if($hs['total_additional_premium'])
        <div class="pt-1 pb-3 avoid-break">
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Additional Extensions')}}</div>
                </div>
            </div>
            @if ($hs['additional_benefits']->count() >= 1)
                <table class="border-collapse w-full font-size-content">
                    <thead>
                        <tr>
                            <th class="p-1">{{__('Item')}}</th>
                            <th class="p-1">{{__('Benefits')}}</th>
                            @if($hs['additional_total_premium']?->plan_1)
                            <th class="p-1">{{__('Limit Plan I')}}</th>@endif
                            @if($hs['additional_total_premium']?->plan_2)
                            <th class="p-1">{{__('Limit Plan II')}}</th>@endif
                            @if($hs['additional_total_premium']?->plan_3)
                            <th class="p-1">{{__('Limit Plan III')}}</th>@endif
                            @if($hs['additional_total_premium']?->plan_4)
                            <th class="p-1">{{__('Limit Plan IV')}}</th>@endif
                            @if($hs['additional_total_premium']?->plan_5)
                            <th class="p-1">{{__('Limit Plan V')}}</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hs['additional_benefits'] as $index => $item)
                            <tr>
                                <td class="p-1 text-center">{{ $index + 1 }}</td>
                                <td class="p-1">{{ $item?->name }}</td>
                                @if($hs['additional_total_premium']?->plan_1)
                                <td class="p-1 text-right">{{ number_format($item?->plan_1, 2) }}</td>@endif
                                @if($hs['additional_total_premium']?->plan_2)
                                <td class="p-1 text-right">{{ number_format($item?->plan_2, 2) }}</td>@endif
                                @if($hs['additional_total_premium']?->plan_3)
                                <td class="p-1 text-right">{{ number_format($item?->plan_3, 2) }}</td>@endif
                                @if($hs['additional_total_premium']?->plan_4)
                                <td class="p-1 text-right">{{ number_format($item?->plan_4, 2) }}</td>@endif
                                @if($hs['additional_total_premium']?->plan_5)
                                <td class="p-1 text-right">{{ number_format($item?->plan_5, 2) }}</td>@endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
    @if($hs['total_premium']?->plan_1 || $hs['total_premium']?->plan_2 || $hs['total_premium']?->plan_3 || $hs['total_premium']?->plan_4 || $hs['total_premium']?->plan_5)
        <div class="pt-1 pb-3 avoid-break clearfix">
            <div class="w-1/4 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Total Premium (USD)')}}:</div>
            </div>
            <div class="w-3/4 inline-block float-right">
                <div class="font-bold mb-3">
                    <table class="border-collapse w-full font-size-content">
                        <thead>
                            <tr>
                                <th class="border p-2">{{__('Premium (USD)')}}</th>
                                @if($hs['standard_total_premium']?->plan_1 > 0 || $hs['optional_total_premium']?->plan_1 > 0)
                                <th class="p-1">{{__('Plan I (USD)')}}</th>@endif
                                @if($hs['standard_total_premium']?->plan_2 > 0 || $hs['optional_total_premium']?->plan_2 > 0)
                                <th class="p-1">{{__('Plan II (USD)')}}</th>@endif
                                @if($hs['standard_total_premium']?->plan_3 > 0 || $hs['optional_total_premium']?->plan_3 > 0)
                                <th class="p-1">{{__('Plan III (USD)')}}</th>@endif
                                @if($hs['standard_total_premium']?->plan_4 > 0 || $hs['optional_total_premium']?->plan_4 > 0)
                                <th class="p-1">{{__('Plan IV (USD)')}}</th>@endif
                                @if($hs['standard_total_premium']?->plan_5 > 0 || $hs['optional_total_premium']?->plan_5 > 0)
                                <th class="p-1">{{__('Plan V (USD)')}}</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-2 text-right">
                                    {{splitWordsKm(__('IPD Premium Per Person'))}}:
                                </td>
                                @if($hs['standard_total_premium']?->plan_1 > 0 || $hs['optional_total_premium']?->plan_1 > 0)
                                    <td class="p-1 text-right">
                                        {{$hs['standard_total_premium']?->plan_1 > 0 ? number_format($hs['standard_premium_per_person']?->plan_1, 2) : '' }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_2 > 0 || $hs['optional_total_premium']?->plan_2 > 0)
                                    <td class="p-1 text-right">
                                        {{$hs['standard_total_premium']?->plan_2 > 0 ? number_format($hs['standard_premium_per_person']?->plan_2, 2) : '' }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_3 > 0 || $hs['optional_total_premium']?->plan_3 > 0)
                                    <td class="p-1 text-right">
                                        {{$hs['standard_total_premium']?->plan_3 > 0 ? number_format($hs['standard_premium_per_person']?->plan_3, 2) : '' }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_4 > 0 || $hs['optional_total_premium']?->plan_4 > 0)
                                    <td class="p-1 text-right">
                                        {{$hs['standard_total_premium']?->plan_4 > 0 ? number_format($hs['standard_premium_per_person']?->plan_4, 2) : '' }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_5 > 0 || $hs['optional_total_premium']?->plan_5 > 0)
                                    <td class="p-1 text-right">
                                        {{$hs['standard_total_premium']?->plan_5 > 0 ? number_format($hs['standard_premium_per_person']?->plan_5, 2) : '' }}
                                </td>@endif
                            </tr>
                            @if($hasTotalOptional)
                            <tr>
                                <td class="p-2 text-right">
                                    {{splitWordsKm(__('Optional Extensions Premium Per Person'))}}:
                                </td>
                                @if($hs['standard_total_premium']?->plan_1 > 0 || $hs['optional_total_premium']?->plan_1 > 0)
                                    <td class="p-1 text-right">{{$hs['optional_total_premium']?->plan_1 ? number_format($hs['optional_premium_per_person']?->plan_1, 2) : 0 }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_2 > 0 || $hs['optional_total_premium']?->plan_2 > 0)
                                    <td class="p-1 text-right">{{$hs['optional_total_premium']?->plan_2 ? number_format($hs['optional_premium_per_person']?->plan_2, 2) : 0 }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_3 > 0 || $hs['optional_total_premium']?->plan_3 > 0)
                                    <td class="p-1 text-right">{{$hs['optional_total_premium']?->plan_3 ? number_format($hs['optional_premium_per_person']?->plan_3, 2) : 0 }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_4 > 0 || $hs['optional_total_premium']?->plan_4 > 0)
                                    <td class="p-1 text-right">{{$hs['optional_total_premium']?->plan_4 ? number_format($hs['optional_premium_per_person']?->plan_4, 2) : 0 }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_5 > 0 || $hs['optional_total_premium']?->plan_5 > 0)
                                    <td class="p-1 text-right">{{$hs['optional_total_premium']?->plan_5 ? number_format($hs['optional_premium_per_person']?->plan_5, 2) : 0 }}
                                </td>@endif
                            </tr>
                            @endif
                            <tr>
                                <td class="border p-2 text-right">
                                    {{ $is_en ? $hs['premium']?->name : splitWordsKm($hs['premium']?->name) }}
                                </td>
                                @if($hs['standard_total_premium']?->plan_1 > 0 || $hs['optional_total_premium']?->plan_1 > 0)
                                    <td class="border p-2 text-right">
                                        {{number_format($hs['premium']?->plan_1, 2)}}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_2 > 0 || $hs['optional_total_premium']?->plan_2 > 0)
                                    <td class="border p-2 text-right">
                                        {{number_format($hs['premium']?->plan_2, 2)}}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_3 > 0 || $hs['optional_total_premium']?->plan_3 > 0)
                                    <td class="border p-2 text-right">
                                        {{number_format($hs['premium']?->plan_3, 2) }}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_4 > 0 || $hs['optional_total_premium']?->plan_4 > 0)
                                    <td class="border p-2 text-right">
                                        {{number_format($hs['premium']?->plan_4, 2)}}
                                </td>@endif
                                @if($hs['standard_total_premium']?->plan_5 > 0 || $hs['optional_total_premium']?->plan_5 > 0)
                                    <td class="border p-2 text-right">
                                        {{number_format($hs['premium']?->plan_5, 2)}}
                                </td>@endif
                            </tr>
                            <tr>
                                <td class="border p-2 text-right">
                                    {{ $is_en ? $hs['total_premium']?->name : splitWordsKm($hs['total_premium']?->name) }}
                                </td>
                                @if($hs['standard_total_premium']?->plan_1 > 0 || $hs['optional_total_premium']?->plan_1 > 0)
                                    <td class="border p-2 text-right">{{number_format($hs['total_premium']?->plan_1, 2)}}</td>
                                @endif
                                @if($hs['standard_total_premium']?->plan_2 > 0 || $hs['optional_total_premium']?->plan_2 > 0)
                                    <td class="border p-2 text-right">{{number_format($hs['total_premium']?->plan_2, 2)}}</td>
                                @endif
                                @if($hs['standard_total_premium']?->plan_3 > 0 || $hs['optional_total_premium']?->plan_3 > 0)
                                    <td class="border p-2 text-right">{{number_format($hs['total_premium']?->plan_3, 2)}}</td>
                                @endif
                                @if($hs['standard_total_premium']?->plan_4 > 0 || $hs['optional_total_premium']?->plan_4 > 0)
                                    <td class="border p-2 text-right">{{number_format($hs['total_premium']?->plan_4, 2)}}</td>
                                @endif
                                @if($hs['standard_total_premium']?->plan_5 > 0 || $hs['optional_total_premium']?->plan_5 > 0)
                                    <td class="border p-2 text-right">{{number_format($hs['total_premium']?->plan_5, 2)}}</td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Grand Total Premium (USD)')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3 avoid-break"> {{ number_format($hs['grand_total_premium'], 2) }}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Endorsements/Clauses')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach ($hs['endorsement_clauses'] as $item)
                <div class="mb-3 avoid-break">- {{ $is_en ? $item->clause : $item->clause_kh }}</div>
            @endforeach
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('General Exclusions')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach ($hs['general_exclusions'] as $item)
                <div class="mb-3 avoid-break">- {{ $is_en ? $item->clause : $item->clause_kh }}</div>
            @endforeach
        </div>
    </div>
    @if($hs['warranty'] || $hs['warranty_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Warranty')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $hs[!$is_en ? 'warranty_kh' : 'warranty'] !!}</div>
            </div>
        </div>
    @endif
    @if($hs['memorandum'] || $hs['memorandum_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Memorandum')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $hs[!$is_en ? 'memorandum_kh' : 'memorandum'] !!}</div>
            </div>
        </div>
    @endif
    @if($hs['subjectivity'] || $hs['subjectivity_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Subjectivity')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $hs[!$is_en ? 'subjectivity_kh' : 'subjectivity'] !!}</div>
            </div>
        </div>
    @endif
    @if($hs['remark'] || $hs['remark_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Remark')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $hs[!$is_en ? 'remark_kh' : 'remark'] !!}</div>
            </div>
        </div>
    @endif
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Jurisdiction')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$hs['jurisdiction']}}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Quotation Validity')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$hs['quotation_validity']}}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$hs['issued_on']}}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Issued By')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$hs['issued_by']}}</div>
        </div>
    </div>
    <div class="avoid-break">
        <div class="py-2">
            <div class="clearfix">
                <div class="w-2/3 inline-block float-left">
                    <div
                        class="{{ !$is_en ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization font-bold mb-3 uppercase">
                        {{__('Phillip General Insurance (Cambodia) Plc.')}}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="relative" style="min-height: 150px;">
                        @if(
                                request('noStamp') && !$hs['hasLetterHead'] &&
                                isset($hs['signature']) && file_exists(public_path($hs['signature']->file_url))
                            )
                                                    <img class="img-over" src="{{ public_path($hs['signature']->file_url) }}"
                                                        style="max-height: 150px">
                        @elseif (
                                $hs['hasLetterHead'] && isset($hs['signature']) &&
                                file_exists(public_path($hs['signature']->file_url))
                            )
                                                    <img class="img-over" src="{{ public_path($hs['signature']->file_url) }}"
                                                        style="max-height: 150px">
                                                    <img class="img-under" src="{{ public_path('/images/stamp/phillip_insurance.png') }}"
                                                        style="max-height: 150px">
                        @endif
                    </div>
                    <hr class="my-3 border-t border-b-0 border-x-0 border-solid">
                    <div class="mb-3">{{__('Authorised Signature')}}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left"></div>
                <div class="w-2/3 inline-block float-right">
                    <div class="{{ !$is_en ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization mb-3 font-bold uppercase"
                        style="text-decoration: underline;">{{__('Acceptance By Client')}}:</div>
                    <div class="mb-3">
                        {{__('Acceptance By Client Context')}}
                    </div>
                    <div class="w-2/3 inline-block my-20">
                        <hr class="my-3 border-t border-b-0 border-x-0 border-solid mt-12">
                        <div class="pt-1 border-t border-gray-200">{{__('Authorised Signature')}}
                            ({{__('Company Stamp if Applicable')}})</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection

<style>
    html {
        margin: 0;
        padding: 0;
    }

    body {
        font-size: 12pt;
        padding: 0 35px !important;
    }

    .font-candara {
        font-family: Candara, sans-serif;
    }

    .font-khmer-os {
        font-family: KhmerOS !important;
    }

    .font-khmer {
        font-family: KhmerOS, sans-serif;
    }

    .font-khmer-os-moul-light {
        font-family: KhmerOSMoulLight !important;
    }

    .font-size-title {
        font-size: 15pt !important;
    }

    .font-size-authorization {
        font-size: 12pt !important;
    }

    .font-size-content {
        font-size: 12pt !important;
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    table,
    th,
    td {
        border: 1px solid #000 !important;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    .img-under {
        position: absolute;
        left: 0px;
        top: 0px;
        z-index: -1;
    }

    .img-over {
        position: absolute;
        left: 80px;
        top: 10px;
        z-index: -1;
    }
</style>