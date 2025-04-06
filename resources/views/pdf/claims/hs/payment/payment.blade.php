@extends('pdf.layout')

@section('content')

<body class="px-20 my-0 font-candara">
    <div class="break-page">
        <div class="w-full text-center mb-3">
            <p class="inline-block text-2xl font-bold leading-6 pt-2" style="border-bottom: 1px solid #000">
            ប័ណ្ណទូរទាត់សំណង/CLAIM PAYMENT VOUCHER
            </p>
        </div>
    </div>
    <div class="avoid-break">
        <p class="p-2 font-lg font-bold" style="background: #8EA9DB;">
        ពត៍មានលម្អិតនៃការទូរទាត់សំណង/Payment Details
        </p>
        <table class="w-full border-collapse">
            <tr>
                <td class="font-bold w-1/2">ម្ចាស់បណ្ណសន្យារ៉ាប់រង/Policy Holder</td>
                <td class="w-1/2">
                    {{ $data['EN']['policy_holder'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">លេខបណ្ណសន្យារ៉ាប់រង/Policy No.</td>
                <td class="w-1/2">
                    {{ $data['EN']['policy_no'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">លេខទាមទារសំណង/Claim No.</td>
                <td class="w-1/2">
                    {{ $data['EN']['claim_no'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">កាលបរិច្ឆេទគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ/Date of Disability</td>
                <td class="w-1/2">
                    {{ date('d/m/Y', strtotime($data['EN']['date_of_disability'])) }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">កាលបរិច្ឆេទការជូនដំណឹងការគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ/Reporting Date</td>
                <td class="w-1/2">
                    {{ $data['EN']['reporting_date'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">កាលបរិច្ឆេទដែរទទួលបានឯកសារគ្រប់គ្រាន់/Date of Completed Documents</td>
                <td class="w-1/2">
                    {{ date('d/m/Y', strtotime($data['EN']['date_of_completed_doc'])) }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">ឈ្មោះអ្នកទាមទារសំណង/Claimant Name</td>
                <td class="w-1/2">
                    {{ $data['EN']['claimant_name'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">លើករណី/Disability</td>
                <td class="w-1/2 font-khmerOS">
                    {{ $data['EN']['cause_of_loss_disability'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">ប្រភេទនៃការទូរទាត់/Payment Type</td>
                <td class="w-1/2">
                  {{ $data['EN']['payment_type_name'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">ចំនួន/Amount</td>
                <td class="w-1/2">
                    {{ 'USD ' . number_format($data['EN']['amount'], 2) }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">ឈ្មោះអ្នកធ្វើការទូរទាត់/Payee Name</td>
                <td class="w-1/2">
                    {{ $data['EN']['payee_name_en'] }}
                </td>
            </tr>
            <tr>
                <td class="font-bold w-1/2">ក្រោមការព្យាបាលនៃ/For Treatment of</td>
                <td class="w-1/2">
                    {{ $data['EN']['cause_of_loss'] }}
                </td>
            </tr>
        </table>
    </div>
    <div class="avoid-break">
        <div class="mt-5 clearfix">
            <div class="w-1/4 float-left font-bold">
            កំណត់សម្គាល់/Remark
            </div>
            <div class="w-3/4 float-right leading-8 mb-5">
                <div class="w-full float-right" style="border-bottom: 1px solid #000;height:30px;font-khmerOS">
                    {{ $data['EN']['approved_cmt'] }}
                </div><br>
                <div class="w-full float-right" style="border-bottom: 1px solid #000;height:30px;"></div><br>
                <div class="w-full float-right" style="border-bottom: 1px solid #000;height:30px;"></div>
            </div>
            <div class="clear-fix">
                <div class="w-full float-left mt-20">
                </div>
            </div>
            @if ($data['EN']['amount'] <= 3000)
                <div class="clearfix">
                    <div class="w-1/3 float-left">
                        <p class="w-2/3 font-bold"
                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                            ស្នើសុំដោយ/Requested by
                        </p>
                        <p class="mt-1">ឈ្មោះ/Name:  {{ $data['EN']['prepared_by_name'] }}</p>
                        <p class="mt-1">កាលបរិច្ឆេទ/Date:  {{ $data['EN']['requested_date'] ? date('d/m/Y', strtotime($data['EN']['requested_date'])) : '' }} </p>
                    </div>
                    <div class="w-1/3 float-left">
                        <p class="w-2/3 m-auto font-bold"
                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                            ត្រួតពិនិត្យដោយ/Verified by
                        </p>
                        <p class="w-2/3 m-auto mt-1">ឈ្មោះ/Name: {{ $data['EN']['approved_by_name'] }}</p>
                        <p class="w-2/3 m-auto mt-1">កាលបរិច្ឆេទ/Date:
                            {{ $data['EN']['approved_at'] ? date('d/m/Y', strtotime($data['EN']['approved_at'])) : '' }}</p>
                    
                    </div>
                    <div class="w-1/3 float-right">
                        <div class="w-2/3 float-right">
                            <p class="font-bold"
                                style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                អនុម័តដោយ/Approved by
                            </p>
                            <p class="mt-1">ឈ្មោះ/Name:</p>
                            <p class="mt-1">កាលបរិច្ឆេទ/Date:</p>
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
                        <p class="mt-1">ឈ្មោះ/Name:  {{ $data['EN']['prepared_by_name'] }}</p>
                        <p class="mt-1">កាលបរិច្ឆេទ/Date:  {{ $data['EN']['requested_date'] ? date('d/m/Y', strtotime($data['EN']['requested_date'])) : '' }} </p>
                    </div>
                    <div class="w-1/4 float-left">
                        <p class="w-2/3 mx-auto font-bold"
                            style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                            ត្រួតពិនិត្យដោយ/Verified by
                        </p>
                        <p class="w-2/3 mt-1 mx-auto">ឈ្មោះ/Name: {{ $data['EN']['approved_by_name'] }}</p>
                        <p class="w-2/3 mt-1 mx-auto">កាលបរិច្ឆេទ/Date: {{ $data['EN']['approved_at'] ? date('d/m/Y', strtotime($data['EN']['approved_at'])) : '' }}</p>
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

    .break-page {
        page-break-before: always;
    }
    .mt-20{
        margin-top: 5rem;
    }
</style>