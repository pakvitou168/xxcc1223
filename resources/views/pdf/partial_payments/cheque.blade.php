@extends('pdf.layout')

@section('content')
@php $is_en = App::isLocale('en'); @endphp
    <body class="px-20 my-0 {{ $is_en ? 'font-candara':'font-khmer-os' }}">
        @foreach ($claim_payment_details as $detail)
            <div class="break-page">
                <div class="w-full text-center mb-3">
                    <p class="inline-block font-size-title font-bold leading-6 {{$is_en ? 'font-candara uppercase':'font-khmer-os-moul-light'}}" style="border-bottom: 1px solid #000">{{__('Claims Cheque Request')}}</p>
                </div>
                <div class="avoid-break">
                    <div class="float-right mt-5">
                        <p class="mb-2 white-space-nowrap {{$is_en?'uppercase':''}}"> {{ __('Date').': '.$approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}</p>
                        <p class="mb-2 white-space-nowrap {{$is_en?'uppercase':''}}"> {{ __('Payment No').': '.$detail['payment_no'] }}</p>
                        <p class="mb-2 white-space-nowrap {{$is_en?'uppercase':''}}"> {{ __('Account Code').': '.$product_code }}</p>
                    </div>
                    <div style="border-bottom: 3px solid #000" class="w-full mb-5"></div>
                    <div class="mb-5 avoid-break">
                        <table class="w-full border-collapse">
                            <tr>
                                <td class="w-1/3 {{$is_en?'uppercase':''}}">{{__('Claims No')}}</td>
                                <td class="w-2/3">: {{ $claim_payment->claim_no }}</td>
                            </tr>
                            <tr>
                                <td class="w-1/3 {{$is_en?'uppercase':''}}">{{__('Pay to')}}</td>
                                <td class="w-2/3">: {{ $detail['payee_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="w-1/3 {{$is_en?'uppercase':''}}">{{__('For')}}</td>
                                <td class="w-2/3">{{': '.__('Being Paid to').' '.$detail['payee_name'] .' '.__('Under Claim Number').' '. $claim_payment->claim_no }}</td>
                            </tr>
                            <tr>
                                <td class="w-1/3 {{$is_en?'uppercase':''}}">{{__('Amount in Word')}}</td>
                                <td class="w-2/3">: {{ ucwords($detail['amount_in_letters'] ?? '') }}</td>
                            </tr>
                            <tr>
                                <td class="w-1/3 {{$is_en?'uppercase':''}}">{{__('Amount')}}</td>
                                <td class="w-2/3">{{':USD '.number_format($detail['amount'], 2) }}</td>
                            </tr>
                        </table><br><br><br>
                        {{-- <table class="w-full border-collapse">
                            <tr>
                                <td class="w-1/2">REQUESTED BY: {{ $updated_by_name??'___________________________________' }}</td>
                                <td class="w-1/2">DEPARTMENT: Claim</td>
                            </tr>
                        </table><br><br><br><br>
                        <table class="w-full border-collapse">
                            <tr>
                                <td class="w-1/2">CHECKED AND CONFIRM BY: {{ '________________________' }}</td>
                                <td class="w-1/2">APPROVED BY: {{ $approved_by_name??'____________________________________' }}</td>
                            </tr>
                        </table><br> --}}
                        <table class="w-full border-collapse">
                            @foreach ($reinsurance_details->chunk(2) as $reinsurances)
                                <tr>
                                    @foreach ($reinsurances as $item)
                                        <td class="w-1/2">{{ $item['name'] }}: {{ $item['percentaged_share'] . '%' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table><br><br><br>
                        @if ($detail['amount'] < 2000)
                            <div class="clearfix">
                                <div class="w-1/2 float-left">
                                    <p class="w-2/3 font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Requested by')}}
                                    </p>
                                    <p class="mt-1">{{__('Name').": ".$updated_by_name }}</p>
                                    <p class="mt-1">{{__('Date').': '.($updated_at ? date('d/m/Y', strtotime($updated_at)) : '') }}
                                    </p>
                                </div>
                                <div class="w-1/2 float-right">
                                    <div class="w-2/3 float-right">
                                        <p class="font-bold"
                                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                            {{__('Approved by')}}
                                        </p>
                                        <p class="mt-1">{{__('Name').": ". $approved_by_name }}</p>
                                        <p class="mt-1">{{__('Date').": ". ($approved_at ? date('d/m/Y', strtotime($approved_at)) : '') }}</p>
                                    </div>
                                </div>
                            </div>
                            @elseif($detail['amount'] < 50000)
                            <div class="clearfix">
                                <div class="w-1/3 float-left">
                                    <p class="w-2/3 font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Requested by')}}
                                    </p>
                                    <p class="mt-1">{{__('Name').": ". $updated_by_name }}</p>
                                    <p class="mt-1">{{__('Date').": ". ($updated_at ? date('d/m/Y', strtotime($updated_at)) : '') }}
                                    </p>
                                </div>
                                <div class="w-1/3 float-left">
                                    <p class="w-2/3 m-auto font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Verified by')}}
                                    </p>
                                    <p class="w-2/3 m-auto mt-1">{{__('Name').": ".$approved_by_name }}</p>
                                    <p class="w-2/3 m-auto mt-1">{{__('Date').": ". ($approved_at ? date('d/m/Y', strtotime($approved_at)) : '') }}</p>
                                   
                                </div>
                                <div class="w-1/3 float-right">
                                    <div class="w-2/3 float-right">
                                        <p class="font-bold"
                                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                            {{__('Approved by')}}
                                        </p>
                                        <p class="mt-1">{{__('Name').":"}}</p>
                                        <p class="mt-1">{{__('Date').":"}}</p>
                                    </div>
                                </div>
                            </div>
                            @else
                              <div class="clearfix">
                                <div class="w-1/4 float-left">
                                    <p class="w-2/3 font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Requested by')}}
                                    </p>
                                    <p class="mt-1">{{__('Name').": ".$updated_by_name }}</p>
                                    <p class="mt-1">{{__('Date').": ".($updated_at ? date('d/m/Y', strtotime($updated_at)) : '') }}
                                    </p>
                                </div>
                                <div class="w-1/4 float-left">
                                    <p class="w-2/3 mx-auto font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Verified by')}}
                                    </p>
                                    <p class="w-2/3 mt-1 mx-auto">{{__('Name').": ". $approved_by_name }}</p>
                                    <p class="w-2/3 mt-1 mx-auto">{{__('Date').": ". ($approved_at ? date('d/m/Y', strtotime($approved_at)) : '') }}</p>
                                </div>
                                <div class="w-1/4 float-right">
                                    <p class="w-2/3 ml-auto font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Co Approved by')}}
                                    </p>
                                    <p class="w-2/3 mt-1 ml-auto">{{__('Name').': '}}</p>
                                    <p class="w-2/3 mt-1 ml-auto">{{__('Date').': '}}</p>
                                </div>
                                <div class="w-1/4 float-right">
                                    <div class="w-2/3 mx-auto float-right">
                                        <p class="font-bold"
                                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                            {{__('Approved by')}}
                                        </p>
                                        <p class="mt-1 mx-auto">{{__('Name').': '}}</p>
                                        <p class="mt-1 mx-auto">{{__('Date').': '}}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </body>
@endsection

<style>
    .white-space-nowrap {
        white-space: nowrap;
    }

    .font-candara {
        font-family: Candara, sans-serif;
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    td {
        padding: 5px 5px;
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    .break-page {
        page-break-before: always;
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
    html {
        margin: 0;
        padding: 0;
    }
    body {
        font-size: 12pt;
        padding: 0 35px !important;
    }
    .font-size-title {
        font-size: 15pt !important;
    }
</style>
