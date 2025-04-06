@extends('pdf.layout')
@section('content')

<body class="px-5 my-0 font-candara">
    <div class="w-full text-center mb-3">
        <p class="inline-block text-xl font-bold leading-6 mt-1" style="border-bottom: 1px solid #000">តារាងគណនាទូទាត់សំណង
            SCHEME CALCULATION
        </p>
    </div>
    <div class="mb-5 font-khmerOS">
        <table class="w-full border-collapse">
            <tbody>
                <tr>
                    <td width="25%">
                        <p>Policyholder: </p>
                        <p>ម្ចាស់បណ្ណសន្យារ៉ាប់រង:</p>
                    </td>
                    <td colspan="3">{{$claim->insured_name}}</td>
                    <td colspan="2">
                        <p>Period of insurance: </p>
                        <p>រយៈពេលធានារ៉ាប់រង</p>
                    </td>
                    <td colspan="2">{{$claim->insurance_cover_period}}</td>
                </tr>
                <tr>
                    <td>
                        <p>Claimant's Name: </p>
                        <p>ឈ្មោះអ្នកទាមទារសំណង:</p>
                    </td>
                    <td>{{$claim->claimant_name}}</td>
                    <td>
                        <p>Sex: {{$claim->claimant_gender}}</p>
                        <p> ភេទ :</p>
                    </td>
                    <td>
                        <p class="no-wrap">Age: {{$claim->claimant_age}}</p>
                        <p> អាយុ: </p>
                    </td>
                    <td colspan="2"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2">Policy No.លេខបណ្ណសន្យារ៉ាប់រង </td>
                    <td colspan="2">{{$claim->document_no}}</td>
                </tr>
                <tr>
                    <td rowspan="2">Disability:លើករណី</td>
                    <td rowspan="2" colspan="3">{{$claim->cause_of_loss_disability}}</td>
                    <td colspan="2">លេខទាមទារសំណង Claims No.</td>
                    <td colspan="2">{{ $claim->claim_no }}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p>Date of Disability:</p>
                        <p>កាលបរិច្ឆេទគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ</p>
                    </td>
                    <td colspan="2">{{$claim->date_of_disability}}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="2">
                        <p>DATE OF COMPLETED DOCUMENTS:​</p>
                        <p>កាលបរិច្ឆេទដែរទទួលបានឯកសារគ្រប់គ្រាន់</p>
                    </td>
                    <td colspan="2">{{$claim->date_of_completed_doc}}</td>
                </tr>
                <tr>
                    <td colspan="2"><p>Benefits Plan per disability</p> <p>គំរោងអត្ថប្រយោន៍ ក្នុងមួយករណី</p></td>
                    <td colspan="2">{{$claim->schema_plan_name}}</td>
                    <td class="text-center">
                        <p>អតិបរិមា </p>
                        <p>Limit</p>
                    </td>
                    <td class="text-center">
                        <p>ចំណាយជាក់ស្តែង </p>
                        <p>Actual Incurred Expenses</p>
                    </td>
                    <td class="text-center">
                        <p>សំណងអតិបរិមា </p>
                        <p>Maxinum Payable</p>
                    </td>
                    <td class="text-center">
                        <p>ចំណាយមិនមានសំណង</p>
                        <p>Non-Payable Expense</p>
                    </td>
                </tr>
                @foreach($schemaData as $key => $schema)
                    @php    $rowspan = $schema->max_number_of_day && $schema->number_of_day ? 3 : 1; @endphp
                    <tr>
                        <td colspan="4">
                            @if($claim->schema_type === 'STANDARD'){{$key + 1}}. @endif {{splitWordsKm($schema->schema_name)}}
                        </td>
                        <td rowspan="{{$rowspan}}" class="text-right">{{number_format($schema->limit_amount, 2)}}</td>
                        <td rowspan="{{$rowspan}}" class="text-right">{{number_format($schema->actual_incurred_expense, 2)}}
                        </td>
                        <td rowspan="{{$rowspan}}" class="text-right">{{number_format($schema->maximum_payable, 2)}}</td>
                        <td rowspan="{{$rowspan}}" class="text-right">{{number_format($schema->non_payable_expense, 2)}}
                        </td>
                    </tr>
                    @if($schema->max_number_of_day && $schema->number_of_day)
                        <tr>
                            <td>Admission Date/ថ្ងៃចូលសម្រាក</td>
                            <td class="no-wrap">
                                {{$schema->admission_date ? date('d-M-y', strtotime($schema->admission_date)) : null}}</td>
                            <td>
                                <p>Days: </p>
                                <p>ចំនួនថ្ងៃ</p>
                            </td>
                            <td>{{$schema->number_of_day}}</td>
                        </tr>
                        <tr>
                            <td>Discharge Date/ថ្ងៃចាកចេញ</td>
                            <td class="no-wrap">
                                {{$schema->discharge_date ? date('d-M-y', strtotime($schema->discharge_date)) : null}}</td>
                            <td colspan="2">
                                <p>Fee per day: </p>
                                <p>តម្លៃក្នុងមួយថ្ងៃ</p>
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="4">
                        <p>DUE TO: </p>
                        <p>មូលហេតុមិនទូទាត់</p>
                    </td>
                    <td colspan="4">{{$claim->due_to}}</td>
                </tr>
                <tr>
                    <td colspan="4">TOTAL AMOUNT (USD)ចំនួនសរុប ជាដុល្លារអាមេរិច</td>
                    <td></td>
                    <td>USD {{number_format($claim->total_actual_incurred_expense, 2)}}</td>
                    <td>USD {{number_format($claim->total_maximum_payable, 2)}}</td>
                    <td>{{number_format($claim->total_non_payable_expense, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="4">PREVIOUS PAYMENT (USD)សំណងបានទូទាត់លើកមុន​ ជាដុល្លារអាមេរិច </td>
                    <td></td>
                    <td colspan="3">USD {{$claim->previous_payment}}</td>
                </tr>
                <tr>
                    <td colspan="4">TOTAL AMOUNT DUE (USD)សំណងត្រូវទូទាត់នៅសល់​ ជាដុល្លារអាមេរិច </td>
                    <td></td>
                    <td colspan="3">USD {{$claim->total_amount_due}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="avoid-break text-md">
        @if ($claim->total_maximum_payable <= 3000)
            <div class="clearfix">
                <div class="w-1/3 float-left">
                    <p class="w-2/3 font-bold"
                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                        ស្នើសុំដោយ/Requested by
                    </p>
                    <p class="mt-1">{{__('Name')}}: {{ $claim->schema_prepared_by }}</p>
                    <p class="mt-1">{{__('Date')}}: {{ $claim->schema_prepared_at ? date('d/m/Y', strtotime($claim->schema_prepared_at)) : '' }}
                    </p>
                </div>
                <div class="w-1/3 float-left">
                    <p class="w-2/3 m-auto font-bold"
                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                        ត្រួតពិនិត្យ/Verified by
                    </p>
                    <p class="w-2/3 m-auto mt-1">{{__('Name')}}: {{ $claim->schema_approved_by }}</p>
                    <p class="w-2/3 m-auto mt-1">{{__('Date')}}:
                        {{ $claim->schema_approved_at ? date('d/m/Y', strtotime($claim->schema_approved_at)) : '' }}</p>
                    
                </div>
                <div class="w-1/3 float-right">
                    <div class="w-2/3 float-right">
                        <p class="font-bold"
                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                            អនុម័តដោយ/Approved by
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
                        ស្នើសុំដោយ/Requested by
                    </p>
                    <p class="mt-1">ឈ្មោះ/Name: {{ $claim->schema_prepared_by }}</p>
                    <p class="mt-1">កាលបរិច្ឆេទ/Date: {{ $claim->schema_prepared_at ? date('d/m/Y', strtotime($claim->schema_prepared_at)) : '' }}
                    </p>
                </div>
                <div class="w-1/4 float-left">
                    <p class="w-2/3 m-auto font-bold"
                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                        ត្រួតពិនិត្យ/Verified by
                    </p>
                    <p class="w-2/3 m-auto mt-1">ឈ្មោះ/Name: {{ $claim->schema_approved_by }}</p>
                    <p class="w-2/3 m-auto mt-1">កាលបរិច្ឆេទ/Date:
                        {{ $claim->schema_approved_at ? date('d/m/Y', strtotime($claim->schema_approved_at)) : '' }}</p>
                    
                </div>
                <div class="w-1/4 float-right">
                    <p class="w-2/3 ml-auto font-bold"
                        style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                        សហអនុម័តដោយ/Co Approved by
                    </p>
                    <p class="w-2/3 mt-1 ml-auto">ឈ្មោះ/Name: </p>
                    <p class="w-2/3 mt-1 ml-auto">កាលបរិច្ឆេទ/Date: </p>
                </div>
                <div class="w-1/4 float-right">
                    <div class="w-2/3 mx-auto float-right">
                        <p class="font-bold"
                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                            អនុម័តដោយ/Approved by
                        </p>
                        <p class="mt-1 mx-auto">ឈ្មោះ/Name: </p>
                        <p class="mt-1 mx-auto">កាលបរិច្ឆេទ/Date: </p>
                    </div>
                </div>
            </div>
        @endif
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
        padding: 0 3px;
        vertical-align: top;
        font-size: 0.75rem;
    }

    td p {
        line-height: 1rem;
        margin: 0;
        padding: 5px 0;
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    .no-wrap {
        white-space: nowrap;
    }

    .text-md {
        font-size: 0.75rem;
    }

    .text-xl {
        font-size: 1.25rem;
        line-height: 1.75rem;
    }
    tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
</style>