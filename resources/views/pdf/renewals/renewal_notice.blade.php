@extends('pdf.layout')

@section('content')
@php
    $periodOfInsurance = '';

    if ($data->effective_date_from && $data->effective_date_to) {
        $periodOfInsurance = __('From').' '
            .\Carbon\Carbon::parse($data->effective_date_from)->format('d/m/Y').' '
            .__('To').' '
            .\Carbon\Carbon::parse($data->effective_date_to)->format('d/m/Y').' ('
            .__('Both days inclusive').')';
    }
@endphp
<body class="{{ App::isLocale('km') ? 'font-khmer-os' : 'font-candara' }} mb-0">
    <div class="text-center">
        <div class="pt-1">
            <div
                class="{{ App::isLocale('km') ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-title font-bold text-center uppercase">
                @if (App::isLocale('km'))
                {{ $data->product_name_kh }}
                @else
                {{ $data->product_name }}
                @endif
                <div class="pt-2">{{__('Renewal Notice') }}</div>
            </div>
        </div>
        <div class="flex flex-col py-2">
            <div class="text-right">
                <div class="mt-1">{{__('Renewal Policy No')}}. : <span>{{ $data->documentNo }}</span></div>
                <div class="mt-1">{{__('Business Code')}} : <span>{{ $data->business_code }}</span></div>
            </div>
        </div>
    </div>
    <div class="pt-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Attention To')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">{{ App::isLocale('en') ? $data->insured_name : $data->insured_name_kh }}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="font-bold mb-3">{{__('Dear Valued Client,')}}</div>
            <div class="mb-3">
            {{__('Phillip General Insurance (Cambodia) Plc. is pleased to invite you to renew your expiring Policy. To ensure the full coverage, please review and advise us for any revision such as insurable interest, sum insured, and other information. We reserve the rights to revise the premium, terms and conditions if there is any claim incurred before the expiry.')}}
            </div>
            <div class="mb-3">
                {{__('Kindly check and sign on this renewal notice, and return us before the expiry, here are your renewal terms and premium:')}}
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('The Insured Name')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">{{ App::isLocale('en') ? $data->insured_name : $data->insured_name_kh }}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Correspondence Address')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">
                    {{ $data->address }}
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
                    {{ $data->occupation }}
                    @else
                    {{ $data->occupation_kh }}
                    @endif
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Period Of Insurance')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                {{ $data->effective_day }} {{__('Days')}} - {{$periodOfInsurance}}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Coverage')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                @foreach ($data->coverage as $item)
                <div>
                    @if (App::isLocale('en'))
                    <div class="font-bold mb-1">{{ $item->cover_name }} ({{ $item->cover_code }})</div>
                    <p class="mb-2">{!! $item->html_detail !!}</p>
                    @else
                    <div class="font-bold mb-1">{{ $item->cover_name_kh }} ({{ $item->cover_code }})</div>
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
                    {{App::isLocale('km') ? $data->policy_wording_kh : $data->policy_wording }} </div>
            </div>
        </div>
    </div>
    <div class="pt-1 pb-3 avoid-break">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Insured Vehicle')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $data->vehicles->count() > 1 ? $data->vehicles->count() . ' units as per list
                    attached' : '' }}</div>
            </div>
        </div>
        @if ($data->vehicles->count() == 1)
        <table class="border-collapse w-full font-size-content">
            <thead>
                <tr>
                    <th class="p-1">{{__('Make and Model') }}</th>
                    <th class="p-1">{{__('Plate No.') }}</th>
                    <th class="p-1">{{__('Chassis No.') }}</th>
                    <th class="p-1">{{__('Engine No.') }}</th>
                    <th class="p-1">{{__('Manufacturing Year') }}</th>

                    @if ($data->has_passenger_tonnage)
                    <th class="p-1">{{__('Seats/Tonnage') }}</th>
                    @else
                    <th class="p-1">{{__('Cubic Capacity') }}</th>
                    @endif

                    <th class="p-1">{{__('Sum Insured') }} (USD)</th>
                    <th class="p-1">{{__('Premium') }} (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->vehicles as $item)
                <tr>
                    <td class="p-1">{{ $item->make_model }}</td>
                    <td class="p-1 font-khmer-os">{{ $item->plate_no }}</td>
                    <td class="p-1">{{ $item->chassis_no }}</td>
                    <td class="p-1">{{ $item->engine_no }}</td>
                    <td class="p-1 text-right">{{ $item->manufacturing_year }}</td>

                    @if ($data->has_passenger_tonnage)
                    <th class="p-1 text-right">{{ $item->passenger_tonnage }}</th>
                    @else
                    <td class="p-1 text-right">{{ $item->cubic }}</td>
                    @endif

                    <td class="p-1 text-right">{{ number_format($item->sum_insured, 2) }}</td>
                    <td class="p-1 text-right">{{ number_format($item->premium, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @if ($data->vehicles->count() > 1)
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Total Sum Insured')}} (USD):</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="font-bold mb-3">{{number_format($data->total_sum_insured,2)}}</div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Total Premium')}} (USD):</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="font-bold mb-3">{{number_format($data->total_premium,2)}}</div>
        </div>
    </div>
    @endif
    <div class="py-5">
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Limitation As To Use')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3" style="word-wrap: break-word;">
                    {{
                    App::isLocale('km') ? $data->limitation_to_use_kh: $data->limitation_to_use
                    }}
                </div>
            </div>
        </div>
        @if ($data->vehicles->count() == 1)
        @if($data->nf_discount)
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Discount')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{$data->discount}}</div>
            </div>
        </div>
        @endif
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('No Claim Discount')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{$data->no_claim_discount}}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Deductible')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right mb-1">
                <div class="font-bold mb-2">{{__('It is applicable to each and every claim for:')}}</div>
                @foreach ($data->deductibles as $item)
                @if (App::isLocale('en'))
                <div class="mb-2">{{$item->deductible}}</div>
                @else
                <div class="mb-2">{{ $item->deductible_kh }}</div>
                @endif
                @endforeach
            </div>
        </div>
        @elseif ($data->vehicles->count() > 1)
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
                @foreach ($data->endorsement_clauses as $item)
                <div class="mb-3 avoid-break">- {{$item }}</div>
                @endforeach
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('General Exclusions')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                @foreach ($data->exclusion_clauses as $item)
                <div class="mb-3 avoid-break">- {{ $item }}</div>
                @endforeach
            </div>
        </div>
        @if ($data->warranty)
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Warranty')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $data->warranty !!}</div>
            </div>
        </div>
        @endif
        @if ($data->memorandum)
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Memorandum')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $data->memorandum !!}</div>
            </div>
        </div>
        @endif
        @if ($data->subjectivity)
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Subjectivity')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $data->subjectivity !!}</div>
            </div>
        </div>
        @endif
        @if ($data->remark)
        <div class="clearfix avoid-break">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Remark')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-candara">{!! $data->remark !!}</div>
            </div>
        </div>
        @endif
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Jurisdiction')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                    {{App::isLocale('km') ? $data->jurisdiction_kh: $data->jurisdiction}}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Claim Amount (USD)')}}:</div>
            </div>
            <div class="w-1/5 inline-block float-left">
                <div class="mb-3">
                    {{__('Claim Incurred')}}: {{ number_format($data->claim_incurred,2) }}
                </div>
            </div>
            <div class="w-1/5 inline-block float-left">
                <div class="mb-3">
                    {{__('Claim Paid')}}: {{ number_format($data->claim_paid,2) }}
                </div>
            </div>
            <div class="w-1/5 inline-block float-right">
                <div class="mb-3">
                    {{__('Claim Outstanding')}}: {{ number_format($data->claim_outstanding,2) }}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                @php
                $issuedDate = $data->updated_at ?? $data->created_at;
                @endphp
                <div class="mb-3">{{ $issuedDate->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Issued By')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $data->issued_by }}</div>
            </div>
        </div>
    </div>

    <div class="avoid-break">
        <div class="py-2">
            <div class="clearfix">
                <div class="w-2/3 inline-block float-left">
                    <div
                        class="{{ App::isLocale('km') ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization font-bold mb-3 uppercase">
                        {{__('Phillip General Insurance (Cambodia) Plc.')}}</div>
                </div>
            </div>
        </div>
        <div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="relative top-0 left-0" style="min-height: 150px;">
                        @if ($data->hasLetterHead && $data->signature && file_exists(public_path($data->signature->file_url)))
                            <img class="img-over" src="{{ public_path($data->signature->file_url) }}" style="max-height: 150px">
                            @if(!request('noStamp'))
                                <img class="img-under" src="{{ public_path('/images/stamp/phillip_insurance.png') }}" style="max-height: 150px">                       
                            @endif
                        @endif
                    </div>
                        <hr class="my-3 border-t border-b-0 border-x-0 border-solid">
                        <div class="mb-3">{{__('Authorised Signature')}}</div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left"></div>
                <div class="w-2/3 inline-block float-right">
                    <div class="{{ App::isLocale('km') ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization mb-3 font-bold uppercase" style="text-decoration: underline;">{{__('Acceptance By Client')}}:</div>
                    <div class="mb-3">
                        {{__('Acceptance By Client Context')}}
                    </div>
                    <div class="w-2/3 inline-block my-20">
                        <hr class="my-3 border-t border-b-0 border-x-0 border-solid mt-12">
                        <div class="pt-1 border-t border-gray-200">{{__('Authorised Signature')}} ({{__('Company Stamp if Applicable')}})</div>
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