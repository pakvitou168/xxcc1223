@extends('pdf.layout')

@section('content')
@php
    $is_en = App::getLocale() !== 'km';
@endphp

<body class="{{ !$is_en ? 'font-khmer-os' : 'font-candara' }} mb-0">
    <div class="text-center">
        <div class="pt-1">
            <div
                class="{{ !$is_en ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-title font-bold text-center uppercase">
                @if (!$is_en)
                    {{ $data->product?->name }}
                @else
                    {{ $data->product?->name }}
                @endif
                <div class="pt-2">{{__('Policy Schedule') }}</div>
            </div>
        </div>
        <div class="flex flex-col py-2">
            <div class="text-right">
                <div class="mt-1">{{__('Policy No')}} : <span>{{ $data->policy?->policy_no }}</span></div>
                <div class="mt-1">{{__('Business Code')}} : <span>{{ $data->business_code }}</span></div>
            </div>
        </div>
    </div>
    <div class="pt-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('The Insured Name')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">{{ $is_en ? $data->insured_name : $data->insured_name_kh }}
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
                        {{$data->customer->address}}
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
                    {{ $data->customer->classification?->occupation }}
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Period Of Insurance')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $data->insurance_period }}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Coverage')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="avoid-break">
                    {!! $data->product->coverage !!}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Geographical Limit')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                    {{ $data->coverage->name }}
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Known Accumulation Limit')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                    USD {{ number_format($data->accumulation_limit_amount, 2) }}
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
        'productName' => $data->product->name
    ])
])}} ({{ $data->policy_wording_version }})
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Insured Persons')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">
                {!! $data->insured_person_note !!}
            </div>
        </div>
    </div>
    <div class="pt-1 pb-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Schedule Of Benefits')}}</div>
            </div>
        </div>
        <table class="border-collapse w-full font-size-content">
            <thead style="display: table-row-group;">
                <tr>
                    <th class="p-1">{{__('Risk No.')}}</th>
                    <th class="p-1">{{__('Insured Persons')}}</th>
                    <th class="p-1">{{__('Accidental Death (USD)')}}</th>
                    <th class="p-1">{{__('Permanent Disablement (USD)')}}</th>
                    <th class="p-1">{{__('Accident Medical Expenses (USD)')}}</th>
                    <th class="p-1">{{__('Premium Per Person (USD)')}}</th>
                    <th class="p-1">{{__('Total Premium (USD)')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->insuredPersons as $index => $insuredPerson)
                <tr>
                    <td class="p-1 text-center">{{ $index + 1 }}</td>
                    <td class="p-1">{{ $insuredPerson->insured_person }}</td>
                    <td class="p-1 text-right">{{ $insuredPerson->accidental_death }}</td>
                    <td class="p-1 text-right">{{ $insuredPerson->permanent_disablement }}</td>
                    <td class="p-1 text-right">{{ $insuredPerson->medical_expense }}</td>
                    <td class="p-1 text-right">{{ $insuredPerson->premium_per_person }}</td>
                    <td class="p-1 text-right font-bold">{{ $insuredPerson->premium }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="p-1 text-right font-bold" colspan="6">{{__('Grand Total')}}</td>
                    <td class="p-1 text-right font-bold">{{ $data->total_premium }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if(isset($data->clauses['AUTOMATIC_EXTENSION']) && count($data->clauses['AUTOMATIC_EXTENSION']) > 0)
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Automatic Extensions')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach($data->clauses['AUTOMATIC_EXTENSION'] as $clause)
            <div class="mb-3">- {{ $clause->name }}</div>
            @endforeach
        </div>
    </div>
    @endif

    @if(count($data->optionalExtensions) > 0)
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Optional Extensions')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach($data->optionalExtensions as $extension)
            <div class="mb-3">
                <h6 class="font-bold">{{ $extension->extension_name }}</h6>
                <div>{!! $extension->extension_description !!}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($data->clauses['ENDORSEMENT']) && count($data->clauses['ENDORSEMENT']) > 0)
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Endorsements/Clauses')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach($data->clauses['ENDORSEMENT'] as $clause)
            <div class="mb-3">- {{ $clause->name }}</div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($data->clauses['EXCLUSION']) && count($data->clauses['EXCLUSION']) > 0)
    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('General Exclusions')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            @foreach($data->clauses['EXCLUSION'] as $clause)
            <div class="mb-3">- {{ $clause->name }}</div>
            @endforeach
        </div>
    </div>
    @endif

    @if($data->warranty)
    <div class="clearfix avoid-break">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Warranty')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3 font-candara">{!! $data->warranty !!}</div>
        </div>
    </div>
    @endif

    @if($data->memorandum)
    <div class="clearfix avoid-break">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Memorandum')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3 font-candara">{!! $data->memorandum !!}</div>
        </div>
    </div>
    @endif

    @if($data->subjectivity)
    <div class="clearfix avoid-break">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Subjectivity')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3 font-candara">{!! $data->subjectivity !!}</div>
        </div>
    </div>
    @endif

    @if($data->remark)
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
            <div class="mb-3">{{ $data->jurisdiction }}</div>
        </div>
    </div>

    <div class="clearfix">
        <div class="w-1/3 inline-block float-left">
            <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
        </div>
        <div class="w-2/3 inline-block float-right">
            <div class="mb-3">{{ $data->issued_on }}</div>
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
                        @if(request('noStamp') && !$hasLetterHead && isset($data->signature))
                            <img class="img-over" src="{{ public_path($data->signature) }}" alt=""
                                style="max-height: 150px">
                        @elseif($hasLetterHead && isset($data->signature))
                            <img class="img-over" src="{{ public_path($data->signature) }}" alt=""
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
        <div class="clearfix">
            <div class="w-1/3"></div>
            <div class="w-2/3">
                <div class="text-md mb-3 font-bold pt-1" style="
                text-decoration-line: underline;
                text-decoration-style: double;
              ">
                    ACCEPTANCE BY CLIENT:
                </div>
                <div class="text-md font-medium mb-3">
                    We examine and understand the above terms and premium payment. We
                    hereby accept and agree to the terms to issue the Policy with an
                    effective on ...................................................
                </div>
            </div>
        </div>
        <br />
        <div class="flex mt-12">
            <div class="w-1/3"></div>
            <div class="w-2/3">
                <div class="text-md font-medium pt-1 border-t border-gray-200">
                    Authorised Signature (Company Stamp if Applicable)
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
