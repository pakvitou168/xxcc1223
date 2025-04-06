@extends('pdf.layout')

@section('content')
@php $is_en = App::getLocale() == 'en'; @endphp
  <body class="px-20 my-0 {{ $is_en ? 'font-candara':'font-khmer-os' }}">
    <div class="w-full text-center mb-3">
      <p class="inline-block font-size-title font-bold leading-6 {{$is_en ? 'font-candara':'font-khmer-os-moul-light'}}" style="border-bottom: 1px solid #000">{{__('Claim Registration')}}</p>
    </div>

    <div class="mb-5 avoid-break">
      <p class="p-1 font-lg font-bold {{$is_en ? 'font-candara':'font-khmer-os-moul-light'}}" style="background: #8EA9DB;">
      {{__('Vehicle Policy Detail')}}
      </p>
      <table class="w-full border-collapse">
        <tr>
          <td class="font-bold w-1/3">{{__('Claims No')}}</td>
          <td class="w-2/3">{{ $claim->claim_no }}</td>
        </tr>
        <tr>
          <td class="font-bold">{{__('Insured Name')}}</td>
          <td>{{ $claim->insured_name }}</td>
        </tr>
        <tr>
          <td class="font-bold">{{__('Policy No')}}</td>
          <td>{{ $claim->document_no }}</td>
        </tr>
        <tr>
          <td class="font-bold font-khmerOS">{{__('Address')}}</td>
          <td>{{ $claim->address }}</td>
        </tr>
        <tr>
          @php
                $periodOfInsurance  = '';
                $effective_days     = (new DateTime($claim->insured_period_from))->diff(new DateTime($claim->insured_period_to))->days+1;
                if ($claim->insured_period_from && $claim->insured_period_to) {
                    $periodOfInsurance = $effective_days.trans('Days').'-'.trans('From').' '
                        .\Carbon\Carbon::parse($claim->insured_period_from)->format('d/m/Y').' '
                        .trans('To').' '
                        .\Carbon\Carbon::parse($claim->insured_period_to)->format('d/m/Y')
                        .'('.trans('Both Days Inclusive').')';
                }
            @endphp
          <td class="font-bold">{{__('Insurance Cover Period')}}</td>
          <td class="uppercase">{{ $periodOfInsurance }}</td>
        </tr>
      </table>
    </div>
  <div class="mb-5 avoid-break">
    <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
      {{__('Accident Details')}}
    </p>
    <table class="w-full border-collapse">
      <tr>
        <td class="font-bold w-1/3">{{__('Date of Accident/Loss')}}</td>
        <td colspan="2" class="w-2/3">{{ date('d/m/Y', strtotime($claim->incident_date)) }}</td>
      </tr>
      <tr>
        <td class="font-bold">{{__('Date of Notifications')}}</td>
        <td colspan="2">{{ date('d/m/Y', strtotime($claim->notification_date)) }}</td>
      </tr>
      <tr>
        @php
          $titleRowSpan = 1;
          $detailRowSpans = count($claim_details);

          $rowSpan = $titleRowSpan + $detailRowSpans;
          $recoveryFromThirdParty = $claim_details->where('cause_of_loss_code', 'OD')->value('recovery_from_third_party');
          
          if ($recoveryFromThirdParty > 0) $rowSpan++;
        @endphp
        <td class="font-bold" rowspan="{{ $rowSpan }}">{{__('Cause of Loss/Accident')}}</td>
        <td class="font-bold text-center">{{__('Cause of Loss/Accident')}}</td>
        <td class="font-bold text-center">{{__('Reserve Amount')}}</td>
      </tr>
      @foreach ($claim_details as $detail)
        <tr>
          <td>{{ $is_en ? $detail->cause_of_loss_desc : $detail->cause_of_loss_desc_kh }}</td>
          <td class="text-right">{{ number_format($detail->amount, 2) }}</td>
        </tr>
      @endforeach
      @foreach ($claim_details as $detail)
        @if ($detail->cause_of_loss_code === 'OD' && $detail->recovery_from_third_party > 0)
          <tr>
            <td>{{__('Recovery from Third Party')}}</td>
            <td class="text-right">{{ number_format($detail->recovery_from_third_party, 2) }}</td>
          </tr>
        @endif
      @endforeach
      <tr>
        <td class="font-bold font-khmerOS">{{__('Location of Loss')}}</td>
        <td colspan="2">{{ $claim->incident_location }}</td>
      </tr>
      <tr>
        <td class="font-bold font-khmerOS">{{__('Circumstances of Accident')}}</td>
        <td colspan="2">{{ $claim->remark }}</td>
      </tr>
    </table>
  </div>

    <div class="mb-5 clearfix avoid-break">
      <div class="float-left" style="width: 49.7%">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        {{__('Insured Vehicle Details')}}
        </p>
        <table class="w-full border-collapse">
          <tr>
            <td class="font-bold w-1/2">{{__('Make Model')}}</td>
            <td class="w-1/2">{{ $claim->make_model }}</td>
          </tr>
          <tr>
            <td class="font-bold">{{__('Registration No')}}</td>
            <td>{{ $claim->registeration_no }}</td>
          </tr>
          <tr>
            <td class="font-bold">{{__('Engine No')}}</td>
            <td>{{ $claim->engine_no }}</td>
          </tr>
          <tr>
            <td class="font-bold">{{__('Chassis No')}}</td>
            <td>{{ $claim->chassis_no }}</td>
          </tr>
        </table>
      </div>
      <div class="float-right" style="width: 49.7%">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        {{__("Authorized Driver's Details")}}
        </p>
        <table class="w-full border-collapse">
          <tr>
            <td class="font-bold">{{__('Name')}}</td>
            <td>{{ $claim->driver_name }}</td>
            <td class="font-bold">{{__('Gender')}}</td>
            <td>{{ $claim->gender }}</td>
          </tr>
          <tr>
            <td class="font-bold">{{__("Driver's Age")}}</td>
            <td>{{ $claim->driver_age }}</td>
            <td class="font-bold">{{__('Occupation')}}</td>
            <td>{{ $claim->occupation }}</td>
          </tr>
          <tr>
            <td class="font-bold" colspan="2">{{__('Driver License No')}}</td>
            <td colspan="2">{{ $claim->license_no }}</td>
          </tr>
          <tr>
            <td class="font-bold">{{__('Issued Date')}}</td>
            <td>{{ date('d/m/Y', strtotime($claim->license_issue_date)) }}</td>
            <td class="font-bold">{{__('Expiry Date')}}</td>
            <td>{{ date('d/m/Y', strtotime($claim->license_expire_date)) }}</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="mb-5 clearfix avoid-break">
      <div class="float-left" style="width: 49.7%">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        {{__('Third Party Vehicle Details')}}
        </p>
        <table class="w-full border-collapse">
          <tr>
            <td class="font-bold">{{__('Make Model')}}</td>
            <td>{{ $claim->vehicle_model }}</td>
          </tr>
          <tr>
            <td class="font-bold">{{__('Registration No')}}</td>
            <td>{{ $claim->plate_no }}</td>
          </tr>
        </table>
      </div>
      <div class="float-right" style="width: 49.7%">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        {{__('Total Reserve Amount')}}
        </p>
        <table class="w-full border-collapse">
          <tr>
            <td class="font-bold text-right" style="height: 40px">{{ number_format($claim_estimation, 2) }}</td>
          </tr>
          
        </table>
      </div>
    </div>

    <div class="mb-5 avoid-break">
      <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
      {{__('Re-Insurance Arrangement')}}
      </p>
      <table class="w-full border-collapse">
        <tr>
          <td class="font-bold w-1/2">{{__('Re-insurance Type')}}</td>
          <td class="font-bold text-center w-1/4">{{__('Share Rate')}}</td>
          <td class="font-bold text-center w-1/4">{{__('Reserve Amount')}}</td>
        </tr>
        @foreach ($reinsurance_details as $detail)
          <tr>
            <td>{{ $detail->name }}</td>
            <td class="text-center">{{ $detail->share }}%</td>
            <td class="text-right">{{ number_format($detail->reserve_amount, 2) }}</td>
          </tr>
        @endforeach
        <tr>
          <td class="font-bold text-right">{{__('Total')}}</td>
          <td class="font-bold text-center">{{ $total_share_percentage }}%</td>
          <td class="font-bold text-right">{{ number_format($claim_estimation, 2) }}</td>
        </tr>
      </table>
    </div>

    <div class="avoid-break">
      <div class="clearfix">
        <div class="float-left" style="width: 49%">
          <div class="w-1/2 float-left font-bold">
          {{__('Prepared By')}}
          </div>
          <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px">
            {{ $updated_by_name }}
          </div>
        </div>
        <div class="float-right" style="width: 49%">
          <div class="w-1/2 float-left font-bold">
          {{__('Prepared Date')}}
          </div>
          <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px">
            {{ $updated_at ? date('d/m/Y', strtotime($updated_at)) : '' }}
          </div>
        </div>
      </div>
      <div class="clearfix mt-10">
        <div class="float-left" style="width: 49%">
          <div class="w-1/2 float-left font-bold">
          {{__('Approved By')}}
          </div>
          <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px">
            {{ $approved_by_name }}
          </div>
        </div>
        <div class="float-right" style="width: 49%">
          <div class="w-1/2 float-left font-bold">
          {{__('Approved Date')}}
          </div>
          <div class="w-1/2 float-right" style="border-bottom: 1px solid #000;height:18px">
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
  table, th, td {
    border: 1px solid #000 !important;
  }
  td {
    padding: 0 3px;
  }
  .avoid-break {
    page-break-inside: avoid;
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
</style>