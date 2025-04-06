@extends('pdf.layout')

@section('content')

<body class="px-20 my-0 font-khmerOS font-candara">
    <div class="break-page">
        <div class="w-full text-center mb-3">
            <p class="text-2xl font-bold mb-4 pt-2" style="line-height: 3rem">
                ផ្នែកដោះស្រាយសំណង CLAIMS DEPARTMENT
            </p>
            <p class="text-2xl font-bold leading-6">
                ស្នើសុំទូរទាត់សំណង CHEQUE REQUEST
            </p>
        </div>
        <div class="w-full">
            <div class="float-right mt-5">
                <p class="mb-2 white-space-nowrap">កាលបរិច្ឆេទ DATE:
                    {{ $data['EN']['approved_at'] ? date('d/m/Y', strtotime($data['EN']['approved_at'])) : '' }}
                </p>
                <p class="mb-2 white-space-nowrap">កាលបរិច្ឆេទ PAYMENT NO. : {{ $data['EN']['payment_no'] }}</p>
                <p class="mb-2 white-space-nowrap">លេខកូដគណនី ACCOUNT CODE : {{ $data['EN']['account_code'] }}</p>
            </div>
            <div style="border-bottom: 3px solid #000" class="w-full mb-5"></div>
            <div class="mb-5 avoid-break">
                <table class="w-full border-collapse">
                    <tr>
                        <td class="w-1/3">លេខទាមទារសំណង/CLAIMS NO.</td>
                        <td class="w-2/3">: {{ $data['EN']['claim_no'] }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/3">ទូទាត់ជូន/PAY TO</td>
                        <td class="w-2/3">: {{ $data['EN']['payee_name_en'] }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/3">សម្រាប់/FOR</td>
                        <td class="w-2/3">: Being Paid to {{ $data['EN']['payee_name_en'] }} Under Claim Number
                            {{ $data['EN']['claim_no'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class="w-1/3">ចំនួនជាអក្សរ/AMOUNT IN WORD</td>
                        <td class="w-2/3">: {{ $data['EN']['amount_in_words'] }}</td>
                    </tr>
                    <tr>
                        <td class="w-1/3">ចំនួន/AMOUNT</td>
                        <td class="w-2/3">: {{ 'USD ' . number_format($data['EN']['amount'], 2) }}</td>
                    </tr>
                </table><br><br>
                <div class="mb-5 avoid-break">

                    <table class="w-full border-collapse table-bordered">
                        <tr>
                            <td colspan="3" class="font-bold">Re-Insurance Arrangement</td>
                        </tr>
                        <tr>
                            <td class="font-bold w-3/7">Re-insurance Type</td>
                            <td class="font-bold text-right w-2/7">Share Rate</td>
                            <td class="font-bold text-right w-2/7">Reserve Amount (USD)</td>
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
                                                    <td class="text-right">{{ $detail->share_rate }}%</td>
                                                    <td class="text-right">{{ number_format($detail->reserve_amount, 2) }}</td>
                                                </tr>
                        @endforeach
                        <tr>
                            <td class="font-bold text-right">TOTAL</td>
                            <td class="font-bold text-right">{{ $totalPercent }}%</td>
                            <td class="font-bold text-right">{{ number_format($totalAmount, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <br><br>
                @if ($data['EN']['amount'] <= 3000)
                    <div class="clearfix">
                        <div class="w-1/3 float-left">
                            <p class="w-2/3 font-bold text-center"
                                style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                ស្នើសុំដោយ<br>Requested by
                            </p>
                            <p class="mt-1">ឈ្មោះ/Name: {{ $data['EN']['prepared_by_name'] }}</p>
                            <p class="mt-1">កាលបរិច្ឆេទ/Date:
                                {{ $data['EN']['requested_date'] ? date('d/m/Y', strtotime($data['EN']['requested_date'])) : '' }}
                            </p>
                        </div>
                        <div class="w-1/3 float-left">
                            <p class="w-2/3 m-auto font-bold text-center"
                                style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                ត្រួតពិនិត្យដោយ<br>Verified by
                            </p>
                            <p class="w-2/3 m-auto mt-1">ឈ្មោះ/Name: {{ $data['EN']['approved_by_name'] }}</p>
                            <p class="w-full ms-1" style="margin-left: 45px">កាលបរិច្ឆេទ/Date:
                                {{ $data['EN']['approved_at'] ? date('d/m/Y', strtotime($data['EN']['approved_at'])) : '' }}
                            </p>

                        </div>
                        <div class="w-1/3 float-right">
                            <div class="w-2/3 float-right">
                                <p class="font-bold text-center"
                                    style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                    អនុម័តដោយ<br>Approved by
                                </p>
                                <p class="mt-1">ឈ្មោះ/Name:</p>
                                <p class="mt-1">កាលបរិច្ឆេទ/Date:</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="clearfix">
                        <div class="w-1/4 float-left">
                            <p class="w-2/3 font-bold" style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                ស្នើសុំដោយ<br>Requested by
                            </p>
                            <p class="mt-1">ឈ្មោះ/Name: {{ $data['EN']['prepared_by_name'] }}</p>
                            <p class="mt-1">កាលបរិច្ឆេទ/Date:
                                {{ $data['EN']['requested_date'] ? date('d/m/Y', strtotime($data['EN']['requested_date'])) : '' }}
                            </p>
                        </div>
                        <div class="w-1/4 float-left">
                            <p class="w-2/3 mx-auto font-bold"
                                style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                ត្រួតពិនិត្យដោយ<br>Verified by
                            </p>
                            <p class="w-2/3 mt-1 mx-auto">ឈ្មោះ/Name: {{ $data['EN']['approved_by_name'] }}</p>
                            <p class="w-2/3 mt-1 mx-auto">កាលបរិច្ឆេទ/Date:
                                {{ $data['EN']['approved_at'] ? date('d/m/Y', strtotime($data['EN']['approved_at'])) : '' }}
                            </p>
                        </div>
                        <div class="w-1/4 float-right">
                            <p class="w-2/3 ml-auto font-bold"
                                style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                សហអនុម័តដោយ<br>Co Approved by
                            </p>
                            <p class="w-2/3 mt-1 ml-auto">ឈ្មោះ/Name: </p>
                            <p class="w-2/3 mt-1 ml-auto">កាលបរិច្ឆេទ/Date: </p>
                        </div>
                        <div class="w-1/4 float-right">
                            <div class="w-2/3 mx-auto float-right">
                                <p class="font-bold" style="height: 150px; border-bottom: 1px solid #000;height:180px;">
                                    អនុម័តដោយ<br>Approved by
                                </p>
                                <p class="mt-1 mx-auto">ឈ្មោះ/Name: </p>
                                <p class="mt-1 mx-auto">កាលបរិច្ឆេទ/Date: </p>
                            </div>
                        </div>
                    </div>
                @endif
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

    .white-space-nowrap {
        white-space: nowrap;
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    table.table-bordered, .table-bordered tr,.table-bordered td {
        border: 1px solid;
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
</style>