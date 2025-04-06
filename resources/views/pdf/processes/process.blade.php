@extends('pdf.layout')

@section('content')

    <body class="px-20 my-0 {{ App::isLocale('km') ? 'font-khmer-os' : 'font-candara' }}">
        @foreach ($cause_of_loss as $cause)
            <div class="break-page">
                <div class="w-full text-center mb-3">
                    <p class="inline-block font-size-title font-bold leading-6 {{App::isLocale('km') ? 'font-khmer-os-moul-light' : 'font-candara uppercase'}}" style="border-bottom: 1px solid #000">{{__('Claim Payment')}}</p>
                </div>

                <div class="avoid-break">
                    <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                        {{__('Vehicle Policy Details')}}
                    </p>
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="font-bold w-1/3">{{__('Claim No')}}</td>
                            <td class="w-2/3">{{ $claim_payment_detail->claim_no }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Insured Name')}}</td>
                            <td>{{ $claim_payment_detail->insured_name }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Policy No')}}</td>
                            <td>{{ $claim_payment_detail->document_no }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-khmerOS">{{__('Address')}}</td>
                            <td>{{ $claim_payment_detail->address }}</td>
                        </tr>
                        <tr>
                            @php
                                $startDate = date('d F Y', strtotime($claim_payment_detail->insured_period_from));
                                $endDate = date('d F Y', strtotime($claim_payment_detail->insured_period_to));
                            @endphp
                            <td class="font-bold">{{__('Insurance Cover Period')}}</td>
                            <td class="uppercase">{{ $startDate . trans('To') . $endDate }}</td>
                        </tr>
                    </table>
                </div>
                <div class="avoid-break">
                    <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                        {{__('Payment Details')}}
                    </p>
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="font-bold w-1/2">{{__('Date of Loss')}}</td>
                            <td class="w-1/2">
                                {{ $claim_payment_detail->incident_date ? date('d/m/Y', strtotime($claim_payment_detail->incident_date)) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Processing Month/Year')}}</td>
                            <td>{{ $claim_payment_detail->processing_month ? date('m/Y', strtotime($claim_payment_detail->processing_month)) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Date of Payment')}}</td>
                            <td>{{ $approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Payee Type')}}</td>
                            <td>{{ $cause['payee_type'] }}</td>
                        </tr>
                        {{-- todo: loop --}}
                        <tr>
                            <td class="font-bold">{{__('Payee Name')}}</td>
                            <td>{{ $cause['payee_name'] }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-khmerOS">{{__('Payee Address')}}</td>
                            <td>{{ $cause['payee_address'] }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Payment Type')}}</td>
                            <td>{{ $claim_payment_detail->payment_type }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Payment No')}}</td>
                            <td>{{ $cause['payment_no'] }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Payment Status')}}</td>
                            <td class="uppercase">{{ $claim_payment_detail->payment_status }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold" rowspan="2">{{__('In Payment of')}} (USD)</td>
                            <td>{{ $cause['cause_of_loss_desc'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">{{ number_format($cause['remain_amount'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Deductible')}} (USD)</td>
                            <td class="text-right">{{ number_format($cause['deductible'], 2) }}</td>
                        </tr>
                        @if (isset($cause['recovery_from_third_party']))
                            <tr>
                                <td class="font-bold">{{__('Recovery from Third Party')}} (USD)</td>
                                <td class="text-right">{{ number_format($cause['recovery_from_third_party'], 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="font-bold">{{__('Claim Payable')}} (USD)</td>
                            <td class="text-right">{{ number_format($cause['claim_payable'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Claim Request')}} (USD)</td>
                            <td class="text-right">{{ number_format($cause['claim_request'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{__('Total Claim Payable')}} (USD)</td>
                            <td class="text-right">{{ number_format($cause['total_claim'], 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="mb-5 avoid-break">
                    <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                        {{__('Re-Insurance Arrangement')}}
                    </p>
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="font-bold w-1/2">{{__('Re-insurance Type')}}</td>
                            <td class="font-bold text-center w-1/4">{{__('Share Rate')}}</td>
                            <td class="font-bold text-center w-1/4">{{__('Payable Amount')}} (USD)</td>
                        </tr>
                        @foreach ($cause['reinsurance'] as $detail)
                            <tr>
                                <td>{{ $detail['name'] }}</td>
                                <td class="text-center">{{ $detail['percentaged_share'] }}%</td>
                                <td class="text-right">{{ number_format($detail['claim_payable'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="font-bold text-right">{{__('Total')}}</td>
                            <td class="font-bold text-center">{{ $cause['total_share_rate'] }}%</td>
                            <td class="font-bold text-right">{{ number_format($cause['total_claim'], 2) }}</td>
                        </tr>
                    </table>
                </div>
                @if (isset($cause['recovery_from_third_party_from_register']) && $cause['recovery_from_third_party_from_register'])
                    <div class="avoid-break">
                        <div class="-mt-3 clearfix">
                            <div class="w-1/2 float-left font-bold">
                                {{__('Recovery from Third Party')}} (USD)
                            </div>
                            <div class="w-1/2 float-right leading-8">
                                <div class="w-full float-right text-right">
                                    {{ number_format($cause['recovery_from_third_party_from_register'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="avoid-break">
                    <div class="mb-5 clearfix">
                        <div class="w-1/4 float-left font-bold">
                            {{__('Remark')}}
                        </div>
                        <div class="w-3/4 float-right leading-8">
                            <div class="w-full float-right" style="border-bottom: 1px solid #000;height:18px;font-khmerOS">
                                {{ $cause['remark'] }}</div><br>
                            <div class="w-full float-right" style="border-bottom: 1px solid #000;height:18px;"></div><br>
                            <div class="w-full float-right" style="border-bottom: 1px solid #000;height:18px;"></div>
                        </div>
                    </div>
                    {{-- Show approver differently absed on total claim amount --}}
                    @if ($cause['total_claim'] < 2000)
                            <div class="clearfix">
                                <div class="w-1/2 float-left">
                                    <p class="w-2/3 font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Requested by')}}
                                    </p>
                                    <p class="mt-1">{{__('Name')}}: {{ $updated_by_name }}</p>
                                    <p class="mt-1">{{__('Date')}}: {{ $updated_at ? date('d/m/Y', strtotime($updated_at)) : '' }}
                                    </p>
                                </div>
                                <div class="w-1/2 float-right">
                                    <div class="w-2/3 float-right">
                                        <p class="font-bold"
                                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                            {{__('Approved by')}}
                                        </p>
                                        <p class="mt-1">{{__('Name')}}: {{ $approved_by_name }}</p>
                                        <p class="mt-1">{{__('Date')}}:
                                            {{ $approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}</p>
                                    </div>
                                </div>
                            </div>
                            @elseif($cause['total_claim'] < 50000)
                            <div class="clearfix">
                                <div class="w-1/3 float-left">
                                    <p class="w-2/3 font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Requested by')}}
                                    </p>
                                    <p class="mt-1">{{__('Name')}}: {{ $updated_by_name }}</p>
                                    <p class="mt-1">{{__('Date')}}: {{ $updated_at ? date('d/m/Y', strtotime($updated_at)) : '' }}
                                    </p>
                                </div>
                                <div class="w-1/3 float-left">
                                    <p class="w-2/3 m-auto font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Verified by')}}
                                    </p>
                                    <p class="w-2/3 m-auto mt-1">{{__('Name')}}: {{ $approved_by_name }}</p>
                                    <p class="w-2/3 m-auto mt-1">{{__('Date')}}:
                                        {{ $approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}</p>
                                   
                                </div>
                                <div class="w-1/3 float-right">
                                    <div class="w-2/3 float-right">
                                        <p class="font-bold"
                                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                            {{__('Approved by')}}
                                        </p>
                                        <p class="mt-1">{{__('Name')}}:</p>
                                        <p class="mt-1">{{__('Date')}}:</p>
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
                                    <p class="mt-1">{{__('Name')}}: {{ $updated_by_name }}</p>
                                    <p class="mt-1">{{__('Date')}}: {{ $updated_at ? date('d/m/Y', strtotime($updated_at)) : '' }}
                                    </p>
                                </div>
                                <div class="w-1/4 float-left">
                                    <p class="w-2/3 mx-auto font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Verified by')}}
                                    </p>
                                    <p class="w-2/3 mt-1 mx-auto">{{__('Name')}}: {{ $approved_by_name }}</p>
                                    <p class="w-2/3 mt-1 mx-auto">{{__('Date')}}: {{ $approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}</p>
                                </div>
                                <div class="w-1/4 float-right">
                                    <p class="w-2/3 ml-auto font-bold"
                                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                        {{__('Co Approved by')}}
                                    </p>
                                    <p class="w-2/3 mt-1 ml-auto">{{__('Name')}}: </p>
                                    <p class="w-2/3 mt-1 ml-auto">{{__('Date')}}: </p>
                                </div>
                                <div class="w-1/4 float-right">
                                    <div class="w-2/3 mx-auto float-right">
                                        <p class="font-bold"
                                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                            {{__('Approved by')}}
                                        </p>
                                        <p class="mt-1 mx-auto">{{__('Name')}}: </p>
                                        <p class="mt-1 mx-auto">{{__('Date')}}: </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                </div>
            </div>
        @endforeach
    </body>
@endsection

<style>
    .font-candara {
        font-family: Candara, sans-serif;
    }

    .font-khmerOS {
        font-family: KhmerOS, sans-serif;
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

    td {
        padding: 0 3px;
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    .break-page {
        page-break-before: always;
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
