@extends('pdf.layout')

@section('content')
  <body class="px-20 my-0 font-candara">
    <div class="w-full text-center mb-3">
      <p class="inline-block text-2xl font-bold leading-6 whitespace-nowrap" style="border-bottom: 1px solid #000">DEDUCTIBLE/SALVAGE</p>
    </div>

    <div class="mb-5 avoid-break">
      <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        Vehicle Policy Details
      </p>
      <table class="w-full border-collapse">
        <tr>
          <td class="font-bold w-1/3">Claims No.</td>
          <td class="w-2/3">{{ $claim_recovery->claim_no }}</td>
        </tr>
        <tr>
          <td class="font-bold">Insured Name</td>
          <td>{{ $claim_recovery->insured_name }}</td>
        </tr>
        <tr>
          <td class="font-bold">Policy No.</td>
          <td>{{ $claim_recovery->document_no }}</td>
        </tr>
        <tr>
          <td class="font-bold font-khmerOS">Address</td>
          <td>{{ $claim_recovery->address }}</td>
        </tr>
        <tr>
          @php
            $startDate = date('d F Y', strtotime($claim_recovery->insured_period_from));
            $endDate = date('d F Y', strtotime($claim_recovery->insured_period_to));
          @endphp
          <td class="font-bold">Insurance Cover Period</td>
          <td class="uppercase">{{ $startDate . ' to ' . $endDate }}</td>
        </tr>
      </table>
    </div>
    <div class="mb-5 avoid-break">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
          Payment Details
        </p>
        <table class="w-full border-collapse">
          <tr>
            <td class="font-bold w-1/3">Date of Loss</td>
            <td colspan="2" class="w-2/3">{{ date('d/m/Y', strtotime($claim_recovery->incident_date)) }}</td>
          </tr>
          <tr>
            <td class="font-bold">Processing Month/Year</td>
            <td colspan="2">{{ $claim_recovery->processing_month?\Carbon\Carbon::createFromFormat('m-Y',$claim_recovery->processing_month)->format('m/Y'):'' }}</td>
          </tr>
          <tr>
            <td class="font-bold">Date of Payment</td>
            <td colspan="2">{{ $approved_at ? date('d/m/Y', strtotime($approved_at)) : '' }}</td>
          </tr>
          <tr>
            <td class="font-bold">Third Party Recovery (USD)</td>
            <td colspan="2">{{ number_format($claim_recovery->third_party_recovery,2) }}</td>
          </tr>
          <tr>
            <td class="font-bold">Insured Sharing (USD)</td>
            <td colspan="2">{{ number_format($claim_recovery->insured_sharing_request,2) }}</td>
          </tr>
          <tr>
            <td class="font-bold">Salvage (USD)</td>
            <td colspan="2">{{ $claim_recovery->salvage?number_format($claim_recovery->salvage,2):'' }}</td>
          </tr>
        </table>
      </div>
    <div class="mb-5 avoid-break">
      <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        Re-Insurance Arrangement
      </p>
      <table class="w-full border-collapse">
        <tr>
          <td class="font-bold w-1/2">Re-insurance Type</td>
          <td class="font-bold text-center w-1/4">Share Rate</td>
          <td class="font-bold text-center w-1/4">Reserve Amount (USD)</td>
        </tr>
        @foreach ($reinsurance_details as $detail)
          <tr>
            <td>{{ $detail['name'] }}</td>
            <td class="text-center">{{ $detail['share'] }}%</td>
            <td class="text-right">{{ number_format($detail['reserve_amount'], 2) }}</td>
          </tr>
        @endforeach
        <tr>
          <td class="font-bold text-right">TOTAL</td>
          <td class="font-bold text-center">{{ $total_share_rate }}%</td>
          <td class="font-bold text-right">{{ number_format($total_share,2) }}</td>
        </tr>
      </table>
    </div>

    <div class="avoid-break">
      <div class="mb-5 clearfix">
        <div class="w-1/4 float-left font-bold">
            Remark
        </div>
        <div class="w-3/4 float-right leading-8">
            <div class="w-full float-right" style="border-bottom: 1px solid #000;height:18px;"></div><br>
            <div class="w-full float-right" style="border-bottom: 1px solid #000;height:18px;"></div><br>
            <div class="w-full float-right" style="border-bottom: 1px solid #000;height:18px;"></div>
        </div>
    </div>
      <div class="mb-5 clearfix">
        <div class="float-left" style="width: 49%">
          <div class="w-1/2 float-left font-bold">
            Prepared by
          </div>
          <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px">
            {{ $updated_by_name }}
          </div>
        </div>
        <div class="float-right" style="width: 49%">
          <div class="w-1/2 float-left font-bold">
            Prepared date
          </div>
          <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px">
            {{ $updated_at ? date('d/m/Y', strtotime($updated_at)) : '' }}
          </div>
        </div>
      </div><br>
      <div class="clearfix">
        <div class="w-1/2 float-left">
          <p class="w-1/2 font-bold" style="height: 150px; border-bottom: 1px solid #000;heith:180px">Checked & Raised by:</p>
          <p class="font-bold mt-1">Date:</p>
        </div>
        <div class="w-1/2 float-right">
          <div class="w-1/2 float-right">
            <p class="font-bold" style="height: 150px; border-bottom: 1px solid #000;heith:180px">Approved by: {{ $approved_by_name }}</p>
            <p class="font-bold mt-1">Date: {{ $approved_at ? date('d/m/Y',strtotime($approved_at)):'' }}</p>
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
  table, th, td {
    border: 1px solid #000 !important;
  }
  td {
    padding: 0 3px;
  }
  .avoid-break {
    page-break-inside: avoid;
  }
</style>