@extends('pdf.layout')

@section('content')
@php
    $is_en = App::getLocale() !== 'km';
    $hasTotalOptional = ($travel['optional_total_premium']?->plan_1 + $travel['optional_total_premium']?->plan_2 + $travel['optional_total_premium']?->plan_3 + $travel['optional_total_premium']?->plan_4 + $travel['optional_total_premium']?->plan_5) > 0;
@endphp

<body class="{{ !$is_en ? 'font-khmer-os' : 'font-candara' }} mb-0">
    <div class="text-center">
        <div class="pt-1">
            <div
                class="{{ !$is_en ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-title font-bold text-center uppercase">
                @if (!$is_en)
                    {{ $travel['product']?->name_kh }}
                @else
                    {{ $travel['product']?->name }}
                @endif
                <div class="pt-2">{{__('Policy Schedule') }}</div>
            </div>
        </div>
        <div class="flex flex-col py-2">
            <div class="text-right">
                <div class="mt-1">{{__('Policy No')}} : <span>{{ $travel['policy_no'] }}</span></div>
                <div class="mt-1">{{__('Business Code')}} : <span>{{ $travel['business_code'] }}</span></div>
            </div>
        </div>
    </div>
    <div class="pt-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('The Insured Name')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">{{ $is_en ? $travel['insured_name'] : $travel['insured_name_kh'] }}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Correspondence Address')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-khmer">
                    <span>
                        {{$travel['correspondence_address']}}
                    </span>
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
                        {{ $travel['business_occupation'] }}
                    @else
                        {{ $travel['business_occupation_kh'] }}
                    @endif
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Period Of Insurance')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $travel['period_of_insurance'] }}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Coverage')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="avoid-break">
                    {!! $travel['coverage'] !!}
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
                        'productPolicyWording' => __('Product Policy Wording', [
                            'productName' => !$is_en ?
                                optional($travel['product'])->name_kh : optional($travel['product'])->name
                        ])
                    ])}} ({{ $travel['policy_wording_version'] }})
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
                    {{ @$travel['geographical_limit']['clause'] }}
                @else
                    {{ @$travel['geographical_limit']['clause_kh'] }}
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
                    {!!$travel['insured_person_note']!!}
                @else
                    {!!$travel['insured_person_note_kh']!!}
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
        @if ($travel['standard_benefits']->count() >= 1)
            <table class="border-collapse w-full font-size-content">
                <thead style="display: table-row-group;">
                    <tr>
                        <th class="p-1">{{__('Item')}}</th>
                        <th class="p-1">{{__('Benefits')}}</th>
                        <th class="p-1">{{__('Number of Days')}}</th>
                        @if($travel['standard_total_premium']?->plan_1 > 0)
                        <th class="p-1">{{__('Plan I (USD)')}}</th> @endif
                        @if($travel['standard_total_premium']?->plan_2 > 0)
                        <th class="p-1">{{__('Plan II (USD)')}}</th> @endif
                        @if($travel['standard_total_premium']?->plan_3 > 0)
                        <th class="p-1">{{__('Plan III (USD)')}}</th> @endif
                        @if($travel['standard_total_premium']?->plan_4 > 0)
                        <th class="p-1">{{__('Plan IV (USD)')}}</th> @endif
                        @if($travel['standard_total_premium']?->plan_5 > 0)
                        <th class="p-1">{{__('Plan V (USD)')}}</th> @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($travel['standard_benefits'] as $index => $item)
                        <tr>
                            <td class="p-1 text-center">{{ $index + 1 }}</td>
                            <td class="p-1">{{ $is_en ? $item?->name : splitWordsKm($item?->name)}}</td>
                            <td class="p-1">{{ $item?->amount ? $item?->amount . __('Days') : '' }}</td>
                            @if($travel['standard_total_premium']?->plan_1 > 0)
                                <td class="p-1 text-right">{{ number_format($item?->plan_1, 2) }}</td>
                            @endif
                            @if($travel['standard_total_premium']?->plan_2 > 0)
                                <td class="p-1 text-right">{{ number_format($item?->plan_2, 2) }}</td>
                            @endif
                            @if($travel['standard_total_premium']?->plan_3 > 0)
                                <td class="p-1 text-right">{{ number_format($item?->plan_3, 2) }}</td>
                            @endif
                            @if($travel['standard_total_premium']?->plan_4 > 0)
                                <td class="p-1 text-right">{{ number_format($item?->plan_4, 2) }}</td>
                            @endif
                            @if($travel['standard_total_premium']?->plan_5 > 0)
                                <td class="p-1 text-right">{{ number_format($item?->plan_5, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach
                    @if($travel['standard_base_plan_amount'])
                    <tr>
                        <td colspan="3" class="p-1 text-right">
                            {{$is_en ? $travel['standard_base_plan_amount']?->name : splitWordsKm($travel['standard_base_plan_amount']?->name)}}
                        </td>
                        @if($travel['standard_total_premium']?->plan_1 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_base_plan_amount']?->plan_1, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_2 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_base_plan_amount']?->plan_2, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_3 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_base_plan_amount']?->plan_3, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_4 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_base_plan_amount']?->plan_4, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_5 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_base_plan_amount']?->plan_5, 2) }}
                        </td>@endif
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="p-1 text-right">{{$is_en ? $travel['standard_premium_per_person']?->name :
            splitWordsKm($travel['standard_premium_per_person']?->name)}}</td>
                        @if($travel['standard_total_premium']?->plan_1 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_premium_per_person']?->plan_1, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_2 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_premium_per_person']?->plan_2, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_3 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_premium_per_person']?->plan_3, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_4 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_premium_per_person']?->plan_4, 2) }}
                        </td>@endif
                        @if($travel['standard_total_premium']?->plan_5 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_premium_per_person']?->plan_5, 2) }}
                        </td>@endif
                    </tr>
                    <tr>
                        <td colspan="3" class="p-1 text-right">{{$is_en ? $travel['standard_total_premium']?->name :
            splitWordsKm($travel['standard_total_premium']?->name) }}</td>
                        @if($travel['standard_total_premium']?->plan_1 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_total_premium']?->plan_1, 2) }}
                        </td> @endif
                        @if($travel['standard_total_premium']?->plan_2 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_total_premium']?->plan_2, 2) }}
                        </td> @endif
                        @if($travel['standard_total_premium']?->plan_3 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_total_premium']?->plan_3, 2) }}
                        </td> @endif
                        @if($travel['standard_total_premium']?->plan_4 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_total_premium']?->plan_4, 2) }}
                        </td> @endif
                        @if($travel['standard_total_premium']?->plan_5 > 0)
                            <td class="p-1 text-right">
                                {{number_format($travel['standard_total_premium']?->plan_5, 2) }}
                        </td> @endif
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
            @if ($travel['optional_benefits']->count() >= 1)
                <table class="border-collapse w-full font-size-content" style="page-break-inside:auto">
                    <thead>
                        <tr>
                            <th class="p-1">{{__('Item')}}</th>
                            <th class="p-1">{{__('Benefits')}}</th>
                            @if($travel['optional_total_premium']?->plan_1)
                            <th class="p-1">{{__('Limit Plan I')}}</th>@endif
                            @if($travel['optional_total_premium']?->plan_2)
                            <th class="p-1">{{__('Limit Plan II')}}</th>@endif
                            @if($travel['optional_total_premium']?->plan_3)
                            <th class="p-1">{{__('Limit Plan III')}}</th>@endif
                            @if($travel['optional_total_premium']?->plan_4)
                            <th class="p-1">{{__('Limit Plan IV')}}</th>@endif
                            @if($travel['optional_total_premium']?->plan_5)
                            <th class="p-1">{{__('Limit Plan V')}}</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travel['optional_benefits'] as $index => $item)
                            <tr>
                                <td class="p-1 text-center">{{ $index + 1 }}</td>
                                <td class="p-1">{{ $item?->name }}</td>
                                @if($travel['optional_total_premium']?->plan_1)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_1, 2) }}</td>
                                @endif
                                @if($travel['optional_total_premium']?->plan_2)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_2, 2) }}</td>
                                @endif
                                @if($travel['optional_total_premium']?->plan_3)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_3, 2) }}</td>
                                @endif
                                @if($travel['optional_total_premium']?->plan_4)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_4, 2) }}</td>
                                @endif
                                @if($travel['optional_total_premium']?->plan_5)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_5, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="p-1 text-right">{{ $is_en ? $travel['optional_premium_per_person']?->name :
                    splitWordsKm($travel['optional_premium_per_person']?->name) }}</td>
                            @if($travel['optional_total_premium']?->plan_1)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_premium_per_person']?->plan_1, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_2)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_premium_per_person']?->plan_2, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_3)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_premium_per_person']?->plan_3, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_4)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_premium_per_person']?->plan_4, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_5)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_premium_per_person']?->plan_5, 2) }}</td>@endif
                        </tr>
                        <tr>
                            <td colspan="2" class="p-1 text-right">{{ $is_en ? $travel['optional_total_premium']?->name :
                    splitWordsKm($travel['optional_total_premium']?->name) }}</td>
                            @if($travel['optional_total_premium']?->plan_1)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_total_premium']?->plan_1, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_2)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_total_premium']?->plan_2, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_3)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_total_premium']?->plan_3, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_4)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_total_premium']?->plan_4, 2) }}</td>@endif
                            @if($travel['optional_total_premium']?->plan_5)
                                <td class="p-1 text-right">{{
                            number_format($travel['optional_total_premium']?->plan_5, 2) }}</td>@endif
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    @endif
    @if($travel['total_additional_premium'] > 0)
        <div class="pt-1 pb-3 avoid-break">
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Additional Extensions')}}</div>
                </div>
            </div>
            @if ($travel['additional_benefits']->count() >= 1)
                <table class="border-collapse w-full font-size-content">
                    <thead>
                        <tr>
                            <th class="p-1">{{__('Item')}}</th>
                            <th class="p-1">{{__('Benefits')}}</th>
                            @if($travel['additional_total_premium']?->plan_1)
                            <th class="p-1">{{__('Limit Plan I')}}</th>@endif
                            @if($travel['additional_total_premium']?->plan_2)
                            <th class="p-1">{{__('Limit Plan II')}}</th>@endif
                            @if($travel['additional_total_premium']?->plan_3)
                            <th class="p-1">{{__('Limit Plan III')}}</th>@endif
                            @if($travel['additional_total_premium']?->plan_4)
                            <th class="p-1">{{__('Limit Plan IV')}}</th>@endif
                            @if($travel['additional_total_premium']?->plan_5)
                            <th class="p-1">{{__('Limit Plan V')}}</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travel['additional_benefits'] as $index => $item)
                            <tr>
                                <td class="p-1 text-center">{{ $index + 1 }}</td>
                                <td class="p-1">{{$is_en ? $item?->name : splitWordsKm($item?->name) }}</td>
                                @if($travel['additional_total_premium']?->plan_1)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_1, 2) }}</td>
                                @endif
                                @if($travel['additional_total_premium']?->plan_2)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_2, 2) }}</td>
                                @endif
                                @if($travel['additional_total_premium']?->plan_3)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_3, 2) }}</td>
                                @endif
                                @if($travel['additional_total_premium']?->plan_4)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_4, 2) }}</td>
                                @endif
                                @if($travel['additional_total_premium']?->plan_5)
                                    <td class="p-1 text-right">{{ number_format($item?->plan_5, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
    @if(
            $travel['total_premium']?->plan_1 || $travel['total_premium']?->plan_2 || $travel['total_premium']?->plan_3 ||
            $travel['total_premium']?->plan_4 || $travel['total_premium']?->plan_5
        )
            <div class="pt-1 pb-3 avoid-break clearfix">
                <div class="w-1/5 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Total Premium (USD)')}}:</div>
                </div>
                <div class="w-4/5 inline-block float-right">
                    <div class="font-bold mb-3">
                        <table class="border-collapse w-full font-size-content">
                            <thead>
                                <tr>
                                    <th class="border p-2">{{__('Premium (USD)')}}</th>
                                    @if($travel['standard_total_premium']?->plan_1 > 0 || $travel['optional_total_premium']?->plan_1 > 0)
                                    <th class="p-1">{{__('Plan I (USD)')}}</th>@endif
                                    @if($travel['standard_total_premium']?->plan_2 > 0 || $travel['optional_total_premium']?->plan_2 > 0)
                                    <th class="p-1">{{__('Plan II (USD)')}}</th>@endif
                                    @if($travel['standard_total_premium']?->plan_3 > 0 || $travel['optional_total_premium']?->plan_3 > 0)
                                    <th class="p-1">{{__('Plan III (USD)')}}</th>@endif
                                    @if($travel['standard_total_premium']?->plan_4 > 0 || $travel['optional_total_premium']?->plan_4 > 0)
                                    <th class="p-1">{{__('Plan IV (USD)')}}</th>@endif
                                    @if($travel['standard_total_premium']?->plan_5 > 0 || $travel['optional_total_premium']?->plan_5 > 0)
                                    <th class="p-1">{{__('Plan V (USD)')}}</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-2 text-right">
                                        {{splitWordsKm(__('IPD Premium Per Person'))}}:
                                    </td>
                                    @if($travel['standard_total_premium']?->plan_1 > 0 || $travel['optional_total_premium']?->plan_1 > 0)
                                        <td class="p-1 text-right">
                                            {{ $travel['standard_total_premium']?->plan_1 > 0 ? number_format($travel['standard_premium_per_person']?->plan_1, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_2 > 0 || $travel['optional_total_premium']?->plan_2 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['standard_total_premium']?->plan_2 > 0 ? number_format($travel['standard_premium_per_person']?->plan_2, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_3 > 0 || $travel['optional_total_premium']?->plan_3 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['standard_total_premium']?->plan_3 > 0 ? number_format($travel['standard_premium_per_person']?->plan_3, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_4 > 0 || $travel['optional_total_premium']?->plan_4 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['standard_total_premium']?->plan_4 > 0 ? number_format($travel['standard_premium_per_person']?->plan_4, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_5 > 0 || $travel['optional_total_premium']?->plan_5 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['standard_total_premium']?->plan_5 > 0 ? number_format($travel['standard_premium_per_person']?->plan_5, 2) : '' }}
                                    </td>@endif
                                </tr>
                                @if($hasTotalOptional)
                                <tr>
                                    <td class="p-2 text-right">
                                        {{splitWordsKm(__('Optional Extensions Premium Per Person'))}}:
                                    </td>
                                    @if($travel['standard_total_premium']?->plan_1 > 0 || $travel['optional_total_premium']?->plan_1 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['optional_total_premium']?->plan_1 > 0 ? number_format($travel['optional_premium_per_person']?->plan_1, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_2 > 0 || $travel['optional_total_premium']?->plan_2 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['optional_total_premium']?->plan_2 > 0 ? number_format($travel['optional_premium_per_person']?->plan_2, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_3 > 0 || $travel['optional_total_premium']?->plan_3 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['optional_total_premium']?->plan_3 > 0 ? number_format($travel['optional_premium_per_person']?->plan_3, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_4 > 0 || $travel['optional_total_premium']?->plan_4 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['optional_total_premium']?->plan_4 > 0 ? number_format($travel['optional_premium_per_person']?->plan_4, 2) : '' }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_5 > 0 || $travel['optional_total_premium']?->plan_5 > 0)
                                        <td class="p-1 text-right">
                                            {{$travel['optional_total_premium']?->plan_5 > 0 ? number_format($travel['optional_premium_per_person']?->plan_5, 2) : '' }}
                                    </td>@endif
                                </tr>
                                @endif
                                <tr>
                                    <td class="border p-2 text-right">
                                        {{ $is_en ? $travel['premium']?->name : splitWordsKm($travel['premium']?->name) }}
                                    </td>
                                    @if($travel['standard_total_premium']?->plan_1 > 0 || $travel['optional_total_premium']?->plan_1 > 0)
                                        <td class="border p-2 text-right">
                                            {{number_format($travel['premium']?->plan_1, 2) }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_2 > 0 || $travel['optional_total_premium']?->plan_2 > 0)
                                        <td class="border p-2 text-right">
                                            {{number_format($travel['premium']?->plan_2, 2) }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_3 > 0 || $travel['optional_total_premium']?->plan_3 > 0)
                                        <td class="border p-2 text-right">
                                            {{number_format($travel['premium']?->plan_3, 2)}}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_4 > 0 || $travel['optional_total_premium']?->plan_4 > 0)
                                        <td class="border p-2 text-right">
                                            {{ number_format($travel['premium']?->plan_4, 2) }}
                                    </td>@endif
                                    @if($travel['standard_total_premium']?->plan_5 > 0 || $travel['optional_total_premium']?->plan_5 > 0)
                                        <td class="border p-2 text-right">
                                            {{ number_format($travel['premium']?->plan_5, 2) }}
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="border p-2 text-right">
                                        {{ $is_en ? $travel['total_premium']?->name : splitWordsKm($travel['total_premium']?->name) }}
                                    </td>
                                    @if($travel['standard_total_premium']?->plan_1 > 0 || $travel['optional_total_premium']?->plan_1 > 0)
                                        <td class="border p-2 text-right">{{ number_format($travel['total_premium']?->plan_1, 2) }}</td>
                                    @endif
                                    @if($travel['standard_total_premium']?->plan_2 > 0 || $travel['optional_total_premium']?->plan_2 > 0)
                                        <td class="border p-2 text-right">{{number_format($travel['total_premium']?->plan_2, 2)}}</td>
                                    @endif
                                    @if($travel['standard_total_premium']?->plan_3 > 0 || $travel['optional_total_premium']?->plan_3 > 0)
                                        <td class="border p-2 text-right">{{number_format($travel['total_premium']?->plan_3, 2)}}</td>
                                    @endif
                                    @if($travel['standard_total_premium']?->plan_4 > 0 || $travel['optional_total_premium']?->plan_4 > 0)
                                        <td class="border p-2 text-right">{{number_format($travel['total_premium']?->plan_4, 2)}}</td>
                                    @endif
                                    @if($travel['standard_total_premium']?->plan_5 > 0 || $travel['optional_total_premium']?->plan_5 > 0)
                                        <td class="border p-2 text-right">{{number_format($travel['total_premium']?->plan_5, 2)}}</td>
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
            <div class="mb-3 avoid-break"> {{ number_format($travel['grand_total_premium'], 2) }}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Endorsements/Clauses')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach ($travel['endorsement_clauses'] as $item)
                <div class="mb-3 avoid-break">- {{ $is_en ? $item->clause : $item->clause_kh }}</div>
            @endforeach
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('General Exclusions')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach ($travel['general_exclusions'] as $item)
                <div class="mb-3 avoid-break">- {{ $is_en ? $item->clause : $item->clause_kh }}</div>
            @endforeach
        </div>
    </div>
    @if($travel['warranty'] || $travel['warranty_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Warranty')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $is_en ? $travel['warranty'] : $travel['warranty_kh'] !!}</div>
            </div>
        </div>
    @endif
    @if($travel['memorandum'] || $travel['memorandum_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Memorandum')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $is_en ? $travel['memorandum'] : $travel['memorandum_kh']  !!}</div>
            </div>
        </div>
    @endif
    @if($travel['subjectivity'] || $travel['subjectivity_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Subjectivity')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $is_en ? $travel['subjectivity'] : $travel['subjectivity_kh'] !!}</div>
            </div>
        </div>
    @endif
    @if($travel['remark'] || $travel['remark_kh'])
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Remark')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $is_en ? $travel['remark'] : $travel['remark_kh'] !!}</div>
            </div>
        </div>
    @endif
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Jurisdiction')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$travel['jurisdiction']}}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$travel['issued_on']}}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Issued By')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{$travel['issued_by']}}</div>
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
                                request('noStamp') && !$travel['hasLetterHead'] && isset($travel['signature']) &&
                                file_exists(public_path($travel['signature']->file_url))
                            )
                                                    <img class="img-over" src="{{ public_path($travel['signature']->file_url) }}" alt=""
                                                        style="max-height: 150px">
                        @elseif (
                                $travel['hasLetterHead'] && isset($travel['signature']) &&
                                file_exists(public_path($travel['signature']->file_url))
                            )
                                                    <img class="img-over" src="{{ public_path($travel['signature']->file_url) }}" alt=""
                                                        style="max-height: 150px">
                                                    <img class="img-under" src="{{ public_path('/images/stamp/phillip_insurance.png') }}" alt=""
                                                        style="max-height: 150px">
                        @endif
                    </div>
                    <hr class="my-3 border-t border-b-0 border-x-0 border-solid">
                    <div class="mb-3">{{__('Authorised Signature')}}</div>
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
        display: block;
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

    th {
        vertical-align: top;
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

    .mt-0 {
        margin-top: 0 !important;
    }
</style>
