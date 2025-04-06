@extends('pdf.layout')

@section('content')
    @php 
        $isEn = App::getLocale() !== 'km';
    @endphp

    <body class="{{ !$isEn ? 'font-khmer-os' : 'font-candara' }} mb-0">
        <div class="text-center">
            <div class="pt-1">
                <div
                    class="{{ !$isEn ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-title font-bold text-center uppercase">
                    {{ $data->product?->name }}
                    <div class="pt-2" id="testing1">{{__('Insurance Quotation') }}</div>
                </div>
            </div>
            <div class="flex flex-col py-2">
                <div class="text-right">
                    <div class="mt-1">{{__('Quotation No')}} : <span>{{ $data->quotation?->document_no }}</span></div>
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
                    <div class="font-bold mb-3">{{ $data->insured_name }}
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Correspondence Address')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="mb-3 font-khmer">
                        <span>{{$data->customer?->address}}</span>
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
                    <div class="font-bold mb-3 uppercase">{{__('Itinerary')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class="avoid-break">
                        {!! $data->insurance_data?->country !!}
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="w-1/3 inline-block float-left">
                    <div class="font-bold mb-3 uppercase">{{__('Policy Wording')}}:</div>
                </div>
                <div class="w-2/3 inline-block float-right">
                    <div class=" mb-3">
                        {{__('Subject to Product Policy Wording', ['productPolicyWording' => __('Product Policy Wording', ['productName' => $data->product->name])])}}
                        ({{ $data->policy_wording_version }})
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Known Accumulation Limit Per Conveyance Limit')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">
                   USD {{ number_format($data->accumulation_limit,2) }}
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
            @if (count($data->dataDetailsView) > 0)
                <table class="border-collapse w-full font-size-content">
                    <thead style="display: table-row-group;">
                        <tr>
                            <th class="p-1 text-nowrap">{{__('No')}}</th>
                            <th class="p-1">{{__('Full Name')}}</th>
                            <th class="p-1">{{__('Date of Birth')}}</th>
                            <th class="p-1">{{__('Passport No.')}}</th>
                            <th class="p-1">{{__('Plan')}}</th>
                            <th class="p-1">{{__('Premium Per Person (USD)')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->dataDetailsView as $index => $item)
                        
                            <tr>
                                <td class="p-1 text-center">{{ $index + 1 }}</td>
                                <td class="p-1 text-center">{{ $item->full_name }}</td>
                                <td class="p-1 text-center">{{ $item->date_of_birth }}</td>
                                <td class="p-1 text-center">{{ $item->passport_no}}</td>
                                <td class="p-1 text-center">{{ $item->plan }}</td>
                                <td class="p-1 text-right">{{ $item->premium }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="p-4 text-right">{{ __('Grand Total (USD)') }}: </td>
                            <td class="p-4 text-right">{{ number_format($data->total_premium,2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Schedule of Benefits')}}:</div>
            </div>
        </div>
        <div class="pt-1 pb-3">
            @if (count($data->coverageStandard) > 0)
                        <table class="border-collapse w-full font-size-content">
                            <thead style="display: table-row-group;">
                                <tr>
                                    <th class="p-1 text-nowrap">{{__('Items')}}</th>
                                    <th class="p-1">{{__('Benefits and Limits')}}</th>
                                    <th class="p-1 text-right">{{__('Standard Plan')}}</th>
                                    <th class="p-1 text-right">{{__('Deluxe Plan')}}</th>
                                    <th class="p-1 text-right">{{__('Executive Plan')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->coverageStandard as $index => $item)
                                    <tr>
                                        @if(!$item->seq_no)
                                            <td  colspan="2" class="font-bold">{{ $item->name }}</td>
                                        @else
                                            <td class="p-1 text-center">{{ $item->seq_no }}</td>
                                            <td class="p-1">{{ $item->name }}</td>
                                        @endif
                                        <td class="p-1 text-right">{{ $item->standard_limit }}</td>
                                        <td class="p-1 text-right">{{ $item->deluxe_limit }}</td>
                                        <td class="p-1 text-right">{{ $item->executive_limit }}</td>
                                       
                                    </tr>
                                @endforeach
                                @if (count($data->coverageAdditional) > 0)
                                   <tr>
                                        <td colspan="5" class="font-bold">{{ __('Additional Benefits') }}</td>
                                    </tr>
                                @endif
                                @foreach($data->coverageAdditional as $index => $item)
                                    <tr>
                                    <td>{{ $item->seq_no }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-right">{{ $item->standard_limit }}</td>
                                    <td class="text-right">{{ $item->deluxe_limit }}</td>
                                    <td class="text-right">{{ $item->executive_limit }}</td>
                                  </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Deductible')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right mb-1">
                <div class="font-bold mb-2">{{__('It is applicable to each and every claim for:')}}</div>
                @foreach ($data->deductibles as $item)
                    @if (App::isLocale('en'))
                        <div class="mb-2">{{ $item->label }}: {{ $item->value }}</div>
                    @else
                        <div class="mb-2">{{ $item->label_kh }}: {{ $item->value }}</div>
                    @endif
                @endforeach
            </div>
        </div>
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
                <div class="font-bold mb-3 uppercase">{{__('Quotation Validity')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{$data->quotation_validity}}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Issued On')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{$data->issued_on}}</div>
            </div>
        </div>
        <div class="clearfix">
            <div class="w-1/3 inline-block float-left">
                <div class="font-bold mb-3 uppercase">{{__('Issued By')}}:</div>
            </div>
            <div class="w-2/3 inline-block float-right">
                <div class="mb-3">{{$data->quotation?->issued_by}}</div>
            </div>
        </div>
        <div class="avoid-break">
            <div class="py-2">
                <div class="clearfix">
                    <div class="w-2/3 inline-block float-left">
                        <div
                            class="{{ !$isEn ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization font-bold mb-3 uppercase">
                            {{__('Phillip General Insurance (Cambodia) Plc.')}}
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left">
                        <div class="relative" style="min-height: 150px;">
                            @if(!request()->letterhead && $data->signature && file_exists(public_path($data->signature)))
                                <img class="img-over" src="{{ public_path($data->signature) }}" style="max-height: 150px">
                            @elseif (request()->letterhead && $data->signature && file_exists(public_path($data->signature)))
                                <img class="img-over" src="{{ public_path($data->signature) }}"
                                    style="max-height: 70px;top:66.666666%;left:0">
                                <img class="img-under" src="{{ public_path('/images/stamp/phillip_insurance.png') }}"
                                    style="max-height: 150px">
                            @endif
                        </div>
                        <hr class="my-3 border-t border-b-0 border-x-0 border-solid">
                        <div class="mb-3">{{__('Authorised Signature')}}</div>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="w-1/3 inline-block float-left"></div>
                    <div class="w-2/3 inline-block float-right">
                        <div class="{{ !$isEn ? 'font-khmer-os-moul-light' : 'font-candara' }} font-size-authorization mb-3 font-bold uppercase"
                            style="text-decoration: underline;">{{__('Acceptance By Client')}}:</div>
                        <div class="mb-3">
                            {{__('Acceptance By Client Context')}}
                        </div>
                        <div class="w-2/3 inline-block my-20">
                            <hr class="my-3 border-t border-b-0 border-x-0 border-solid mt-12">
                            <div class="pt-1 border-gray-200">{{__('Authorised Signature')}}
                                ({{__('Company Stamp if Applicable')}})</div>
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

    tr {
        page-break-inside: avoid;
        page-break-after: auto
    }

    th,
    td {
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

    .text-nowrap {
        white-space: nowrap;
    }

    .text-right {
        text-align: right;
    }
</style>