@extends('pdf.layout')
@section('content')
@php $isEn = !App::getLocale() == 'KM'; @endphp

<body class="font-candara mb-0">
    <div class="text-center">
        <div class="pt-1">
            <div class="font-bold font-size-title text-center uppercase pt-2">
                HOSPITALISATION & SURGERY INSURANCE <br> ENDORSEMENT
            </div>
        </div>
        <div class="flex flex-col py-2">
            <div class="text-right">
                <div class="mt-1">{{__('Policy & Endorsement No')}} : <span>{{ $documentNo }}</span></div>
                <div class="mt-1">{{__('Business Code')}} : <span>{{ $hs->business_code }}</span></div>
            </div>
        </div>
    </div>
    <div class="pt-3">
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('The Insured Name')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="font-bold mb-3">{{ $isEn ? @$hs->insured_name : @$hs->insured_name_kh }}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Correspondence Address')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3 font-khmer">
                    <span>{{$hs->address}}</span>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Period Of Insurance')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $hs->period_of_insurance }}</div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Endorsement Effective Date')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                    {{ \Carbon\Carbon::parse(@$hs->endorsement_e_date)->format('d/F/Y') }}
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Type of Endorsement')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                    {{ $hs->endorsement_type }}
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Endorsement Description')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="text-sm mb-3">{!! $hs->end_description !!}</div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Endorsement Premium')}}({{__('USD')}}):</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $hs->endorsement_premium >= 0 ? number_format($hs->endorsement_premium,2) : '('.number_format(abs($hs->endorsement_premium),2).')' }}</div>
            </div>
        </div>

        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $hs->issued_on }}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Issued By')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{ $hs->issued_by }}</div>
            </div>
        </div>
    </div>

    <div class="avoid-break">
        <div class="py-5">
            <div class="clearfix">
                <div class="w-2/3 inline-block float-left">
                    <div class="font-candara font-size-title font-bold mb-3 uppercase">
                        {{__('Phillip General Insurance (Cambodia) Plc.')}}</div>
                </div>
            </div>
        </div>
        <div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="relative top-0 left-0" style="min-height: 150px;">
                        @if ($letterhead && $signature && file_exists(public_path($signature->file_url)))
                            <img class="img-over" src="{{ public_path($signature->file_url) }}" style="max-height: 150px">
                            <img class="img-under" src="{{ public_path('/images/stamp/phillip_insurance.png') }}"
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
        font-size: 10pt;
        padding: 0 35px !important;
    }

    .font-candara {
        font-family: Candara, sans-serif;
    }

    .font-khmer-os {
        font-family: KhmerOS;
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

    .font-size-content {
        font-size: 10pt !important;
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