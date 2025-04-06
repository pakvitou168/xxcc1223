@extends('pdf.layout')

@section('content')
    <body class="{{ App::isLocale('km') ? 'font-khmer-os' : 'font-candara' }} mb-0">
        <div class="text-center">
            <div class="pt-1">
                <div class="{{ App::isLocale('km') ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-title font-bold text-center uppercase">
                    @if (App::isLocale('km'))
                        {{ optional($auto->product)->name_kh }}
                    @else
                        {{ optional($auto->product)->name }}
                    @endif
                    <div class="pt-2">{{__('Policy Schedule') }}</div>
                </div>
            </div>
            <div class="flex flex-col py-2">
                <div class="text-right">
                    <div class="mt-1">{{__('Policy No')}} : <span>{{ $documentNo }}</span></div>
                    <div class="mt-1">{{__('Business Code')}} : <span>{{ $auto->business_code }}</span></div>
                </div>
            </div>
        </div>
        <div class="pt-3">
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('The Insured Name')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="font-bold mb-3">{{ App::isLocale('en') ? $auto->insured_name : $auto->insured_name_kh }}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Correspondence Address')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3 font-khmer">
                        <span>
                            @if($auto->customer)
                                @if ($auto->addressData)
                                    @if($auto->country)
                                        {{ $auto->customer->address_en ? $auto->customer->address_en. ', ' : '' }}
                                        {{ $auto->customer->village_en ? $auto->customer->village_en. ', ' : '' }}
                                        {{ $auto->addressData->commune ? $auto->addressData->commune. ', '  : '' }}
                                        {{ $auto->addressData->district ? $auto->addressData->district. ', '  : '' }}
                                        {{ $auto->addressData->province ? $auto->addressData->province : '' }}
                                        {{ $auto->addressData->province == 'Phnom Penh' ? ' Capital, ' : ' Province, ' }}
                                        {{ $auto->country }}
                                    @else
                                        {{ $auto->customer->address_en ? $auto->customer->address_en. ', ' : '' }}
                                        {{ $auto->customer->village_en ? $auto->customer->village_en. ', ' : '' }}
                                        {{ $auto->addressData->commune ? $auto->addressData->commune. ', '  : '' }}
                                        {{ $auto->addressData->district ? $auto->addressData->district. ', '  : '' }}
                                        {{ $auto->addressData->province ? $auto->addressData->province : '' }}
                                        {{ $auto->addressData->province == 'Phnom Penh' ? ' Capital' : ' Province' }}
                                    @endif
                                @elseif($auto->country)
                                        {{ $auto->customer->address_en ? $auto->customer->address_en. ', ' : '' }}
                                        {{ $auto->customer->village_en ? $auto->customer->village_en. ', ' : '' }}
                                        {{ $auto->country }}
                                @elseif($auto->customer->village_en)
                                    {{ $auto->customer->address_en ? $auto->customer->address_en. ', ' : '' }}
                                    {{ $auto->customer->village_en }}
                                @else
                                    {{ $auto->customer->address_en }}
                                @endif
                            @endif
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
                        @if (App::isLocale('en'))
                            {{ $auto->customer_classification }}
                        @else
                            {{ $auto->customer_classification_kh }}
                        @endif
                    </div>
                </div>
            </div>
            @php
                $periodOfInsurance = '';

                if ($auto->effective_date_from && $auto->effective_date_to) {
                    $periodOfInsurance = __('From').' '
                        .\Carbon\Carbon::parse($auto->effective_date_from)->format('d/m/Y').' '
                        .__('To').' '
                        .\Carbon\Carbon::parse($auto->effective_date_to)->format('d/m/Y').' '.'('.__('Both Days Inclusive').')';
                }
            @endphp
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Period Of Insurance')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3">{{ $auto->effective_day }} {{__('Days')}} - {{ $periodOfInsurance }}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Coverage')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    @foreach ($coverage as $item)
                        <div class="avoid-break">
                            @if (App::isLocale('en'))
                                <div class="font-bold mb-1">{{ $item->name }} ({{ $item->code }})</div>
                                <p class="mb-2">{!! $item->html_detail !!}</p>
                            @else
                                <div class="font-bold mb-1">{{ $item->name_kh }} ({{ $item->code }})</div>
                                <p class="mb-2 " style="word-wrap: break-word;">{!! $item->html_detail_kh !!}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Policy Wording')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class=" mb-3">
                        {{__('Subject to Product Policy Wording', [
                            'productPolicyWording' => __('Product Policy Wording', ['productName' => App::isLocale('km') ? optional($auto->product)->name_kh : optional($auto->product)->name])
                        ])}} ({{ $auto->policy_wording_version }})
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-1 pb-3 avoid-break">
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Insured Vehicle')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3">{{ $auto->vehicles->count() > 1 ? $auto->vehicles->count() . ' units as per list attached' : '' }}</div>
                </div>
            </div>
            @if ($auto->vehicles->count() == 1)
                <table class="border-collapse w-full font-size-content">
                    <thead>
                        <tr>
                            <th class="p-1">{{__('Make and Model') }}</th>
                            <th class="p-1">{{__('Plate No') }}</th>
                            <th class="p-1">{{__('Chassis No') }}</th>
                            <th class="p-1">{{__('Engine No') }}</th>
                            <th class="p-1">{{__('Manufacturing Year') }}</th>

                            @if ($auto->has_passenger_tonnage)
                                <th class="p-1">{{__('Seats/Tonnage') }}</th>
                            @else
                                <th class="p-1">{{__('Cubic Capacity') }}</th>
                            @endif

                            <th class="p-1">{{__('Sum Insured') }} (USD)</th>
                            <th class="p-1">{{__('Premium') }} (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auto->vehicles as $item)
                            <tr>
                                <td class="p-1">{{ $item->make_name }} {{ $item->model_name }}</td>
                                <td class="p-1 font-khmer-os">{{ $item->plate_no }}</td>
                                <td class="p-1">{{ $item->chassis_no }}</td>
                                <td class="p-1">{{ $item->engine_no }}</td>
                                <td class="p-1 text-right">{{ $item->manufacturing_year }}</td>

                                @if ($auto->has_passenger_tonnage)
                                    <th class="p-1 text-right">{{ $item->passenger_tonnage }}</th>
                                @else
                                    <td class="p-1 text-right">{{ $item->cubic }}</td>
                                @endif

                                <td class="p-1 text-right">{{ number_format($item->vehicle_value, 2) }}</td>
                                <td class="p-1 text-right">{{ number_format($item->premium, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        @if ($auto->vehicles->count() > 1)
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Total Sum Insured')}} (USD):</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="font-bold mb-3">{{number_format($auto->sum_insured,2)}}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Total Premium')}} (USD):</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="font-bold mb-3">{{number_format($auto->total_premium,2)}}</div>
                </div>
            </div>
        @endif
        <div class="py-5">
            <div class="clearfix avoid-break">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Limitation As To Use')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3" style="word-wrap: break-word;">{{
                        App::isLocale('km') ? optional($auto->product)->limitation_to_use_kh
                            : optional($auto->product)->limitation_to_use_en
                    }}</div>
                </div>
            </div>
            @if ($auto->vehicles->count() == 1)
                @if($auto->vehicles[0]->discount)
                    <div class="clearfix">
                        <div class="w-1/3 inline-block float-left">
                            <div class="font-bold mb-3 uppercase">{{__('Discount')}}:</div>
                        </div>
                        <div class="w-2/3 inline-block float-right">
                            <div class="mb-3">{{$auto->vehicles[0]->discount}} %</div>
                        </div>
                    </div>
                @endif
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('No Claim Discount')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right">
                        <div class="mb-3">{{$auto->vehicles[0]->ncd ?? 0}} %</div>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('Deductible')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right mb-1">
                        <div class="font-bold mb-2">{{__('It is applicable to each and every claim for:')}}</div>
                        @foreach ($deductibles as $item)
                            @if (App::isLocale('en'))
                                <div class="mb-2">{{ $item->cover ? $item->cover->deductible_label : '' }}: {{ $item->value }}</div>
                            @else
                                <div class="mb-2">{{ $item->cover ? $item->cover->deductible_label_kh : '' }}: {{ $item->value }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @elseif ($auto->vehicles->count() > 1)
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('No Claim Discount')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right">
                        As per list attached
                    </div>
                </div>
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('Deductible')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right mb-1">
                        As per list attached
                    </div>
                </div>
            @endif
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Endorsements/Clauses')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    @foreach ($auto->endorsement_clause as $item)
                        <div class="mb-3 avoid-break">- {{ App::isLocale('en') ? $item->clause : $item->clause_kh }}</div>
                    @endforeach
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('General Exclusions')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    @foreach ($auto->general_exclusive as $item)
                        <div class="mb-3 avoid-break">- {{ App::isLocale('en') ? $item->clause : $item->clause_kh }}</div>
                    @endforeach
                </div>
            </div>
            @if ($auto->warranty)
                <div class="clearfix avoid-break">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('Warranty')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right">
                        <div class="mb-3 font-candara">{!! $auto->warranty !!}</div>
                    </div>
                </div>
            @endif
            @if ($auto->memorandum)
                <div class="clearfix avoid-break">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('Memorandum')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right">
                        <div class="mb-3 font-candara">{!! $auto->memorandum !!}</div>
                    </div>
                </div>
            @endif
            @if ($auto->subjectivity)
                <div class="clearfix avoid-break">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('Subjectivity')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right">
                        <div class="mb-3 font-candara">{!! $auto->subjectivity !!}</div>
                    </div>
                </div>
            @endif
            @if ($auto->remark)
                <div class="clearfix avoid-break">
                    <div class="w-1/3 inline-block float-left">
                        <div class="font-bold mb-3 uppercase">{{__('Remark')}}:</div>
                    </div>
                    <div class="w-2/3 inline-block float-right">
                        <div class="mb-3 font-candara">{!! $auto->remark !!}</div>
                    </div>
                </div>
            @endif
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Jurisdiction')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3">{{__('Kingdom of Cambodia')}}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    @php
                        $issuedDate = $auto->updated_at ?? $auto->created_at;
                    @endphp
                    <div class="mb-3">{{ $issuedDate->format('d/m/Y') }}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Issued By')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3">{{ $auto->issued_by }}</div>
                </div>
            </div>
        </div>

        <div class="avoid-break">
            <div class="py-2">
                <div class="clearfix">
                    <div class="w-2/3 inline-block float-left">
                        <div class="{{ App::isLocale('km') ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization font-bold mb-3 uppercase">{{__('Phillip General Insurance (Cambodia) Plc.')}}</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left">
                        <div class="relative top-0 left-0" style="min-height: 150px;">
                            @if(request('noStamp') && !$hasLetterHead && (($signature && file_exists(public_path($signature->file_url))) || $isEpolicy))
                                <img class="img-over" src="{{ public_path($isEpolicy ? '/images/stamp/epolicy_signature.png' : $signature->file_url) }}" style="max-height: 150px">
                            @elseif ($hasLetterHead && (($signature && file_exists(public_path($signature->file_url))) || $isEpolicy))
                                <img class="img-over" src="{{ public_path($isEpolicy ? '/images/stamp/epolicy_signature.png' : $signature->file_url) }}" style="max-height: 150px">
                                <img class="img-under" src="{{ public_path('/images/stamp/phillip_insurance.png') }}" style="max-height: 150px">
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
    table, th, td {
        border: 1px solid #000 !important;
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
