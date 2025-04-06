@extends('pdf.layout')

@section('content')
<body class="m-0">
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="title">
                <h1>INSURANCE CERTIFICATE</h1>
                <p>Personal Accident Insurance</p>
            </div>
        </div>

        <!-- Certificate Details -->
        <div class="content">
            <div class="main-content">
                <div class="left-column">
                    <div class="field">
                        <div class="label">Certificate No:</div>
                        <div class="value">{{ $data->policy->document_no }}</div>
                    </div>

                    <div class="field">
                        <div class="label">Insured Name:</div>
                        <div class="value">{{ $data->insured_name }}</div>
                    </div>

                    <div class="field">
                        <div class="label">Address:</div>
                        <div class="value">{{ $data->customer->address }}</div>
                    </div>

                    <div class="field">
                        <div class="label">Period of Insurance:</div>
                        <div class="value">
                            {{ $data->effective_day }} days from
                            {{ \Carbon\Carbon::parse($data->effective_date_from)->format('d F Y') }}
                            to {{ \Carbon\Carbon::parse($data->effective_date_to)->format('d F Y') }}
                            (Both days inclusive)
                        </div>
                    </div>

                    <div class="section-title">Coverage Details</div>

                    <div class="field">
                        <div class="label">Coverage Type:</div>
                        <div class="value">Personal Accident</div>
                    </div>

                    <div class="field">
                        <div class="label">Geographical Limit:</div>
                        <div class="value">{{ $data->coverage->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Insured Persons Table -->
            <div class="section-title">Insured Persons</div>

            <table class="insured-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Insured Person</th>
                        <th>Death Benefit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->insuredPersons as $index => $insuredPerson)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="font-khmer-os">{{ $insuredPerson->insured_person }}</td>
                        <td>{{ $insuredPerson->accidental_death }} USD</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="total-label">Total Insured Persons:</td>
                        <td>{{ count($data->insuredPersons) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Certificate Summary -->
            <div class="summary-container">
                <div class="certificate-summary">
                    <div class="summary-header">CERTIFICATE SUMMARY</div>
                    <div class="summary-content">
                        <div class="summary-columns">
                            <div class="summary-column">
                                <div class="summary-item">
                                    <div class="summary-label">Certificate No:</div>
                                    <div class="summary-value">{{ $data->policy->document_no }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Period:</div>
                                    <div class="summary-value">
                                        {{ \Carbon\Carbon::parse($data->effective_date_from)->format('d M Y') }}
                                        To
                                        {{ \Carbon\Carbon::parse($data->effective_date_to)->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Insured:</div>
                                    <div class="summary-value">{{ $data->insured_name }}</div>
                                </div>
                            </div>
                            <div class="summary-column">
                                <div class="summary-item">
                                    <div class="summary-label">Coverage:</div>
                                    <div class="summary-value">Personal Accident</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Geographical Limit:</div>
                                    <div class="summary-value">{{ $data->coverage->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Signature -->
            <div class="footer-section">
                <div class="terms">
                    <p>This certificate is subject to the terms and conditions of the policy.</p>
                    <p>For inquiries, please contact our customer service.</p>
                </div>

                <div class="signature">
                    <div class="sign-line">
                        <div class="line"></div>
                        <div class="text">Authorized Signature</div>
                    </div>
                    <div class="date">Issue Date: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.4;
        color: #000;
        margin: 0;
        padding: 0;
        font-size: 11pt;
    }

    .page {
        padding: 20px;
        max-width: 21cm;
        margin: 0 auto;
        position: relative;
    }

    .header {
        margin-bottom: 20px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
    }

    .title {
        text-align: center;
    }

    .title h1 {
        margin: 0;
        font-size: 18pt;
    }

    .title p {
        margin: 5px 0 0;
        font-size: 12pt;
    }

    .content {
        margin-top: 15px;
    }

    .main-content {
        display: flex;
        margin-bottom: 15px;
    }

    .left-column {
        flex: 1;
    }

    .field {
        margin-bottom: 8px;
        display: flex;
    }

    .label {
        font-weight: bold;
        width: 140px;
    }

    .value {
        flex: 1;
    }

    .section-title {
        margin: 15px 0 10px;
        font-weight: bold;
        font-size: 12pt;
        border-bottom: 1px solid #000;
        padding-bottom: 3px;
    }

    /* Insured Persons Table */
    .insured-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        border: 1px solid #000;
    }

    .insured-table th,
    .insured-table td {
        padding: 6px;
        border: 1px solid #000;
        text-align: left;
        font-size: 10pt;
    }

    .total-label {
        text-align: right;
        font-weight: bold;
    }

    /* Certificate Summary */
    .summary-container {
        margin: 15px 0;
    }

    .certificate-summary {
        width: 100%;
        border: 1px solid #000;
    }

    .summary-header {
        background-color: #f0f0f0;
        padding: 6px;
        font-weight: bold;
        text-align: center;
        font-size: 12pt;
    }

    .summary-content {
        width: 100%;
    }

    .summary-columns {
        display: flex;
        padding: 10px;
    }

    .summary-column {
        flex: 1;
    }

    .summary-item {
        display: flex;
        margin-bottom: 5px;
    }

    .summary-label {
        font-weight: bold;
        width: 120px;
        font-size: 10pt;
    }

    .summary-value {
        flex: 1;
        font-size: 10pt;
    }

    .footer-section {
        margin-top: 15px;
    }

    .terms {
        text-align: center;
        font-size: 9pt;
        margin-bottom: 15px;
    }

    .terms p {
        margin: 3px 0;
    }

    .signature {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .sign-line {
        width: 150px;
    }

    .line {
        border-bottom: 1px solid #000;
        margin-bottom: 3px;
    }

    .text {
        font-size: 9pt;
        text-align: center;
    }

    .date {
        font-size: 9pt;
    }

    .font-khmer-os {
        font-family: Arial, sans-serif;
    }

    @media print {
        .page {
            padding: 15px;
        }

        @page {
            size: A4;
            margin: 0;
        }
    }
</style>
