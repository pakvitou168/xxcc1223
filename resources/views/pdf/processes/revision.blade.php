@extends('pdf.layout')

@section('content')

    <body class="px-20 my-0 font-candara">
        <div class="w-full text-center mb-3">
            <p class="inline-block text-2xl font-bold leading-6" style="border-bottom: 1px solid #000">CLAIMS REVISION</p>
        </div>

        <div class="mb-5 avoid-break">
            <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                Vehicle PolicyDetails
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="font-bold w-1/3">Claims No.</td>
                    <td class="w-2/3">{{ $claim_payment_details[0]->claim_no }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Insured Name</td>
                    <td>{{ $claim_payment_details[0]->insured_name }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Policy No.</td>
                    <td>{{ $claim_payment_details[0]->document_no }}</td>
                </tr>
                <tr>
                    <td class="font-bold font-khmerOS">Address</td>
                    <td>{{ $claim_payment_details[0]->address??'' }}</td>
                </tr>
                <tr>
                    @php
                        $startDate = date('d F Y', strtotime($claim_payment_details[0]->insured_period_from));
                        $endDate = date('d F Y', strtotime($claim_payment_details[0]->insured_period_to));
                    @endphp
                    <td class="font-bold">Insurance Cover Period</td>
                    <td class="uppercase">{{ $startDate . ' to ' . $endDate }}</td>
                </tr>
            </table>
        </div>
        <div class="mb-5 avoid-break">
            <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                Accident Details
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="font-bold w-1/3">Date of Loss</td>
                    <td colspan="2" class="w-2/3">
                        {{ $claim_payment_details[0]->incident_date ? date('d/m/Y', strtotime($claim_payment_details[0]->incident_date)) : '' }}
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">Date of Notification</td>
                    <td colspan="2">
                        {{ $claim_payment_details[0]->notification_date ? date('d/m/Y', strtotime($claim_payment_details[0]->notification_date)) : '' }}
                    </td>
                </tr>
                <tr>
                    @php
                        $titleRowSpan = 1;
                        $detailRowSpans = count($claim_payment_details) - 1;
                        
                        $rowSpan = $titleRowSpan + $detailRowSpans;
                    @endphp
                    <td class="font-bold" rowspan="{{ $rowSpan }}">*Cause of Loss.</td>
                </tr>
                @foreach ($claim_payment_details->slice(1) as $index => $detail)
                        <tr>
                            <td>{{ $detail['cause_of_loss_desc'] }}</td>
                            {{-- <td class="text-right">{{ '$'.number_format($detail['amount'], 2) }}</td> --}}
                        </tr>
                @endforeach
                <tr>
                    <td class="font-bold font-khmerOS">Location of Loss</td>
                    <td colspan="2">{{ $claim_payment_details[0]->incident_location }}</td>
                </tr>
                <tr>
                    <td class="font-bold font-khmerOS">Description of Loss</td>
                    <td colspan="2">{{ $claim_payment_details[0]->remark }}</td>
                </tr>
            </table>
        </div>

        <div class="mb-5 clearfix avoid-break">
            <div class="float-left" style="width: 49.7%">
                <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                    Insured Vehicle Details
                </p>
                <table class="w-full border-collapse">
                    <tr>
                        <td class="font-bold w-1/2">Make Model</td>
                        <td class="w-1/2">{{ $claim_payment_details[0]->make_model }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Registration No.</td>
                        <td>{{ $claim_payment_details[0]->registeration_no }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Engine No.</td>
                        <td>{{ $claim_payment_details[0]->engine_no }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Chassis No.</td>
                        <td>{{ $claim_payment_details[0]->chassis_no }}</td>
                    </tr>
                </table>
            </div>
            <div class="float-right" style="width: 49.7%">
                <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                    Authorized Driver's Details
                </p>
                <table class="w-full border-collapse">
                    <tr>
                        <td class="font-bold">Name</td>
                        <td>{{ $claim_payment_details[0]->driver_name }}</td>
                        <td class="font-bold">Gender</td>
                        <td>{{ $claim_payment_details[0]->gender }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Driver's age</td>
                        <td>{{ $claim_payment_details[0]->driver_age }}</td>
                        <td class="font-bold">Occupation</td>
                        <td>{{ $claim_payment_details[0]->occupation }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold" colspan="2">Driver License No.</td>
                        <td colspan="2">{{ $claim_payment_details[0]->license_no }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Issued Date</td>
                        <td>{{ $claim_payment_details[0]->license_issue_date ? date('d/m/Y', strtotime($claim_payment_details[0]->license_issue_date)) : '' }}
                        </td>
                        <td class="font-bold">Expiry Date</td>
                        <td>{{ $claim_payment_details[0]->license_expire_date ? date('d/m/Y', strtotime($claim_payment_details[0]->license_expire_date)) : '' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mb-5 clearfix avoid-break">
            <div class="float-left" style="width: 49.7%">
                <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                    Third Party Vehicle Details
                </p>
                <table class="w-full border-collapse">
                    <tr>
                        <td class="font-bold">Make Model</td>
                        <td>{{ $claim_payment_details[0]->vehicle_model }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Registration No.</td>
                        <td>{{ $claim_payment_details[0]->plate_no }}</td>
                    </tr>
                </table>
            </div>
            <div class="float-right" style="width: 49.7%">
                <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                    Claims Estimation
                </p>
                <table class="w-full border-collapse">
                    <tr>
                        <td class="font-bold text-right" style="height: 40px">
                            {{ '$'.number_format($claim_estimation, 2) }}</td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="mb-5 avoid-break">
            <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
                Re-Insurance Arrangement
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="font-bold w-1/2">Re-insurance Type</td>
                    <td class="font-bold text-center w-1/4">Share Rate</td>
                    <td class="font-bold text-center w-1/4">Reserve Amount</td>
                </tr>
                @foreach ($reinsurance_details as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-center">{{ $item['percentaged_share'] }}%</td>
                        <td class="text-right">{{ '$'.number_format($item['claim_payable'], 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="font-bold text-right">TOTAL</td>
                    <td class="font-bold text-center">{{ $total_share_percentage }}%</td>
                    <td class="font-bold text-right">{{'$'.number_format($claim_estimation, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="avoid-break">
            <div class="clearfix">
              <div class="mb-5 clearfix">
                <div class="w-1/4 float-left font-bold">
                  Remark
                </div>
                <div class="w-3/4 float-right leading-8">
                  ____________________________________________________________________________
                  ____________________________________________________________________________
                  ____________________________________________________________________________
                </div>
              </div>
                <div class="float-left" style="width: 49%">
                    <div class="w-1/2 float-left font-bold">
                        Prepared by
                    </div>
                    <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px;">
                        {{ $updated_by_name }}
                    </div>
                </div>
                <div class="float-right" style="width: 49%">
                    <div class="w-1/2 float-left font-bold">
                        Prepared date
                    </div>
                    <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px;">
                        {{ $updated_at ? date('d/m/Y', strtotime($updated_at)) : '' }}
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="float-left" style="width: 49%">
                    <div class="w-1/2 float-left font-bold">
                        Approved by
                    </div>
                    <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px;">
                        {{ $approved_by_name }}
                    </div>
                </div>
                <div class="float-right" style="width: 49%">
                    <div class="w-1/2 float-left font-bold">
                        Approved date
                    </div>
                    <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px;">
                        {{ $approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}
                    </div>
                </div>
            </div>
        </div>


    </body>
@endsection

<style>
    .font-candara {
        font-family: Candara, sans-serif;
    }
    .font-khmerOS{
    font-family: KhmerOS,sans-serif;
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
</style>
