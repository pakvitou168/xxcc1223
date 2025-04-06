@extends('pdf.layout')


@section('content')
    <body class="m-0">
        @foreach ($data_details as $data_detail)
            {{-- Add class skeleton to inspect layout --}}
            <div class="break-page mx-10">

                <div class="w-full" style="height: 220px;"></div>

                <div class="clearfix" style="height: 42px;">
                    <div class="w-3/5 inline-block float-right ml-2 text-10pt font-candara mt-2.5">
                        - {{ $policy->document_no }}
                    </div>
                </div>

                <div class="clearfix" style="height: 100px;">
                    <div class="w-full inline-block text-10pt font-candara ml-6">
                        <p class="mt-8 w-full" style="height: 38px;">{{ $data_master->insured_name }}</p>
                        <p class="mt-1.5 w-full" style="height: 38px;">
                            @if($data_master->customer)
                                @if ($data_master->addressData)
                                        {{ $data_master->customer->address_en ? $data_master->customer->address_en. ', ' : '' }}
                                        {{ $data_master->customer->village_en ? $data_master->customer->village_en : '' }}
                                        {{ $data_master->addressData->commune ? $data_master->addressData->commune. ', '  : '' }}
                                        {{ $data_master->addressData->district ? $data_master->addressData->district. ', '  : '' }}
                                        {{ $data_master->addressData->province ? $data_master->addressData->province : '' }}
                                        {{ $data_master->addressData->province == 'Phnom Penh' ? ' Capital, ' : ' Province' }}
                                        {{ $data_master->country?', '.$data_master->country:'' }}
                                @elseif($data_master->country)
                                        {{ $data_master->customer->address_en ? $data_master->customer->address_en. ', ' : '' }}
                                        {{ $data_master->customer->village_en ? $data_master->customer->village_en. ', ' : '' }}
                                        {{ $data_master->country }}
                                @elseif($data_master->customer->village_en)
                                    {{ $data_master->customer->address_en ? $data_master->customer->address_en. ', ' : '' }}
                                    {{ $data_master->customer->village_en }}
                                @else
                                    {{ $data_master->customer->address_en }}
                                @endif
                            @endif
                        </p>
                    </div>
                </div>

                <div class="clearfix" style="height: 60px;">
                    <div class="w-3/5 inline-block float-right ml-2 text-10pt font-candara">
                        <p class="w-full mt-5" style="min-height: 20px;">
                            - {{ $data_detail->endos_day_remaining ?? $data_master->effective_day }}
                            days from
                            {{ $policy->version ? \Carbon\Carbon::parse($data_detail->endorsement_e_date)->format('d M Y') : \Carbon\Carbon::parse($data_master->effective_date_from)->format('d F Y') }}
                            to {{ \Carbon\Carbon::parse($data_master->effective_date_to)->format('d F Y') }}
                            (Both days inclusive)
                        </p>
                    </div>
                </div>

                <div class="clearfix" style="height: 67px;">
                    <div class="w-3/5 inline-block float-right ml-2 text-10pt font-candara">
                        <p class="w-full mt-7" style="min-height: 20px;">- {{ $data_detail->covers }}</p>
                    </div>
                </div>

                <div class="clearfix" style="height: 65px;">
                    <div class="w-3/5 h-100 inline-block float-right ml-2 text-10pt font-candara">
                        @php
                            $deductibleCount = count($data_detail->deductibles);

                            $marginClass = '';

                            if ($deductibleCount === 1) $marginClass = 'mt-6';
                            if ($deductibleCount === 2) $marginClass = 'mt-4';
                            if ($deductibleCount === 3) $marginClass = 'mt-2';

                        @endphp
                        <p class="{{ $marginClass }}">
                            @foreach ($data_detail->deductibles as $deductible)
                                <p>- {{ optional($deductible->cover)->deductible_label }}: {{ $deductible->value }}</p>
                            @endforeach
                        </p>
                    </div>
                </div>

                <div class="clearfix" style="height:55px;">
                    <div class="w-3/5 inline-block float-right ml-2 text-10pt font-candara">
                        <p class="w-full mt-5" style="min-height: 20px;">- Kingdom of Cambodia</p>
                    </div>
                </div>

                <div class="mx-auto mt-7 circle" style="width: 360px; height: 360px;">
                    <div class="clearfix w-full">
                        <div class="w-3/5 inline-block float-right ml-2 text-10pt font-candara" style="margin-top: 138px;">
                            <p class="w-full" style="height: 20px;">{{ $policy->document_no }}</p>
                            <p class="w-full mt-2" style="height: 20px;">
                                {{ $policy->version ?  \Carbon\Carbon::parse($data_detail->endorsement_e_date)->format('d M Y') : \Carbon\Carbon::parse($data_master->effective_date_from)->format('d M Y')}}
                                To
                                {{ \Carbon\Carbon::parse($data_master->effective_date_to)->format('d M Y') }}
                            </p>
                            <p class="w-full mt-0.5 font-candara font-khmer-os" style="height: 18px;font-size: 12px;">{{ $data_detail->plate_no }}</p>
                            <p class="w-full mt-1 table" style="height: 22px;width: 200px;line-height: 0.95; font-size: 12px;">
                                <span class="table-cell align-bottom">{{ $data_detail->chassis_no }}/{{ $data_detail->engine_no }}</span>
                            </p>
                            <p class="w-full mt-1 table" style="height: 22px;width: 180px;line-height: 0.95;font-size: 12px;">
                                <span class="table-cell align-bottom">{{ $data_detail->covers }}</span>
                            </p>
                            <p class="w-full mt-3" style="height: 20px;">Kingdom of Cambodia</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </body>
@endsection

<style>
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
    .text-10pt {
        font-size: 10pt;
    }
    .font-khmer-os {
        font-family: KhmerOS !important;
    }
    .font-candara {
        font-family: Candara, sans-serif;
    }

    .break-page {
        page-break-before: always;
    }
    .skeleton > div:nth-child(even) {
        background-color: lightgray;
    }
    .skeleton > div:nth-child(odd) {
        background-color: white;
    }
    .skeleton div.circle {
        background: lightgray;
        border-radius: 50%;
    }
</style>
 