@extends('pdf.layout')
@section('content')

<body class="px-20 my-0 font-candara">
    <div class="w-full text-center mb-3">
        <p class="inline-block text-2xl font-bold leading-6 mt-6" style="border-bottom: 1px solid #000">ការចុះឈ្មោះទាមទារសំណង CLAIMS REGISTRATION</p>
    </div>
    <div class="mb-5 avoid-break">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        ព័ត៌មានលម្អិតអំពីប័ណ្ណសន្យាធានារ៉ាប់រងការសម្រាកពេទ្យនិងការវះកាត់/Hospitalisation and Surgical Policy Details 
        </p>
        <table class="w-full border-collapse">
            <tr>
                <td class="font-bold w-1/3">លេខទាមទារសំណង/Claims No.</td>
                <td class="w-2/3">{{ $claim->claim_no }}</td>
            </tr>
            <tr>
                <td class="font-bold">ម្ចាស់បណ្ណសន្យារ៉ាប់រង/Insured Name</td>
                <td>{{ $claim->insured_name }}</td>
            </tr>
            <tr>
                <td class="font-bold">លេខបណ្ណសន្យារ៉ាប់រង/Policy No.</td>
                <td>{{ $claim->document_no }}</td>
            </tr>
            <tr>
                <td class="font-bold font-khmerOS">អាសយដ្ឋាន/Address</td>
                <td>{{ $claim->address }}</td>
            </tr>
            <tr>
                <td class="font-bold">រយៈពេលធានារ៉ាប់រង/Insurance Cover Period</td>
                <td class="uppercase">{{$claim->insurance_cover_period}}</td>
            </tr>
        </table>
    </div>
    <div class="mb-5 avoid-break">
        <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
        ព័ត៌មានលម្អិតអំពីគ្រោះថ្នាក់/Accident Details
        </p>
        <table class="w-full border-collapse">
            <tr>
                <td class="font-bold w-1/3">កាលបរិច្ឆេទគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ/Date of Loss</td>
                <td colspan="2" class="w-2/3">{{ $claim->date_of_loss}}</td>
            </tr>
            <tr>
                <td class="font-bold">កាលបរិច្ឆេទការជូនដំណឹងការគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ/Date of Notification</td>
                <td colspan="2">{{ $claim->notification_date }}</td>
            </tr>
            <tr>
                <td class="font-bold">មូលហេតុនៃការគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ/Cause of Loss</td>
                <td class="font-bold text-center">{{$claim->cause_of_loss}}</td>
                <td class="font-bold text-center">USD {{$claim->reserve_amount}}</td>
            </tr>
            <tr>
                <td class="font-bold font-khmerOS">ទីកន្លែងនៃគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ/Location of Loss</td>
                <td colspan="2">{{ $claim->location_of_loss }}</td>
            </tr>
            <tr>
                <td class="font-bold font-khmerOS">សូមពន្យល់លម្អិតអំពីគ្រោះថ្នាក់/Loss Description</td>
                <td colspan="2">{{ $claim->loss_description }}</td>
            </tr>
        </table>
    </div>

    <div class="mb-5 clearfix avoid-break table-wrap">
        <div class="float-left cell-wrap" style="width: 49.7%">
            <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
            ព័ត៌មានលម្អិតអំពីអ្នកទាមទារសំណង/Claimant Details
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="font-bold w-1/2">ឈ្មោះ​អ្នក​ទាម​ទារ​សំណង​/Claimant's Name</td>
                    <td class="font-bold w-1/2">ពេទ្យ​ដៃ​គូ​​ ឬ​មិន​មែន​ដៃ​គូរ​/Penel Clinic or Non-Panel</td>
                </tr>
                <tr>
                    <td>{{ $claim->claimant_name }}</td>
                    <td>{{ $claim->clinic_name }}</td>
                </tr>
            </table>
        </div>
        <div class="float-right cell-wrap" style="width: 49.7%">
            <p class="p-1 font-lg font-bold" style="background: #8EA9DB;">
            ការ​ប៉ាន់​ប្រមាណ​ចំនួន​និង​ធ្វើ​សំណង​/Claims Estimation
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="font-bold">{{$claim->cause_of_loss}}</td>
                    <td>ទឹក​ប្រាក់​ដែល​ប៉ាន់​ប្រមាណ​/Reserve Amount</td>
                </tr>
                <tr>
                    <td>{{$claim->cause_of_loss_disability}}</td>
                    <td><b>USD {{ $claim->reserve_amount }}</b></td>
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
                <td class="font-bold text-center w-1/4">Reserve Amount (USD)</td>
            </tr>
            @php $totalPercent = 0;
            $totalAmount = 0;@endphp
            @foreach ($reinsurances as $detail)
                        @php
                            $totalAmount += $detail->reserve_amount;
                            $totalPercent += $detail->share_rate;
                        @endphp
                        <tr>
                            <td>{{ $detail->name }}</td>
                            <td class="text-center">{{ $detail->share_rate }}%</td>
                            <td class="text-right">{{ number_format($detail->reserve_amount, 2) }}</td>
                        </tr>
            @endforeach
            <tr>
                <td class="font-bold text-right">TOTAL</td>
                <td class="font-bold text-center">{{ $totalPercent }}%</td>
                <td class="font-bold text-right">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="avoid-break mt-10">
        <div class="clearfix">
            <div class="float-left" style="width: 49%">
                <div class="w-3/5 float-left font-bold">
                រៀបចំឯកសារដោយ/Prepared by
                </div>
                <div class="w-2/5 float-right" style="border-bottom: 1px solid #000;height:18px">
                    {{ $claim->prepared_by }}
                </div>
            </div>
            <div class="float-right" style="width: 49%">
                <div class="w-3/5 float-left font-bold">
                    កាលបរិច្ឆេទ/Prepared Date
                </div>
                <div class="w-2/5 float-right" style="border-bottom: 1px solid #000;height:18px">
                    {{ $claim->prepared_at ? date('d/m/Y', strtotime($claim->prepared_at)) : '' }}
                </div>
            </div>
        </div>
        <div class="clearfix mt-10">
            <div class="float-left" style="width: 49%">
                <div class="w-3/5 float-left font-bold">
                អនុម័តដោយ/Approved by 
                </div>
                <div class="w-2/5 float-right" style="border-bottom: 1px solid #000;height:18px">
                    {{ $claim->approved_by }}
                </div>
            </div>
            <div class="float-right" style="width: 49%">
                <div class="w-3/5 float-left font-bold">
                    កាលបរិច្ឆេទ/Approval Date
                </div>
                <div class="w-2/5 float-right" style="border-bottom: 1px solid #000;height:18px">
                    {{ $claim->approved_at ? date('d/m/Y', strtotime($claim->approved_at)) : '' }}
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
        padding: 5px 8px;
    }

    .avoid-break {
        page-break-inside: avoid;
    }
    .table-wrap{
        display: table;
    }
    .cell-wrap{
        display: table-cell;
    }
</style>