@extends('pdf.layout')

@section('content')
    <body class='font-candara'>
        <h3 class="font-size-title font-bold text-center"><span class='font-khmer-os-moul-light'>បណ្ណឥណទាន​​</span> - <span>CREDIT NOTE</span></h3>
        <h4 class="font-bold text-center"><span class='font-khmer-os'>(លេខអត្តសញ្ញាណកម្ម​សារពើពន្ធ</span> - <span>TIN :​ L001-901700885)</h4>

        <div class="clearfix">
            <div class="w-2/3 inline-block float-right">
                <div class="text-right">
                    <span class='font-khmer-os'>លេខរៀងបណ្ណឥណទាន​​</span> - <span>Credit Note.</span> : {{ $credit->inv_cdn_no ?? '-' }}
                </div>
                <div class="text-right">
                    <span class='font-khmer-os'>កាលបរិច្ឆេទ</span> - <span>Date of Issue</span> :
                    @if(empty($credit->issue_date))
                        <span> - </span>
                    @else
                        <span> {{ \Carbon\Carbon::parse($credit->issue_date)->format('d/m/Y') }} </span>
                    @endif
                </div>
                <div class="text-right">
                    <span class='font-khmer-os'>កូដ</span> - <span>Code</span> : {{ $credit->code ?? '-' }}
                </div>
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>ឈ្មោះក្រុមហ៊ុន<span class="absolute right-0">:</span></div>
                    <div>Company Name</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block font-bold float-right">
                {!! "&nbsp;" !!} {{ $credit->customer_name ?? '-' }}
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>អាសយដ្ឋាន<span class="absolute right-0">:</span></div>
                    <div>Correspondence Address</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block float-right">
                {!! "&nbsp;" !!} {{ $credit->address ?? '-' }}
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>លេខអត្តសញ្ញាណកម្ម​សារពើពន្ធ<span class="absolute right-0">:</span></div>
                    <div>Tin</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block float-right">
                {!! "&nbsp;" !!} {{ $credit->tin_code ?? '-' }}
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>លេខបណ្ណសន្យារ៉ាប់រង​ និងឬ បដ្ឋិលេខ<span class="absolute right-0">:</span></div>
                    <div>Policy and/or Endorsement No.</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block float-right">
                {!! "&nbsp;" !!} {{ $credit->policy_document_no ?? '-' }}
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>ប្រភេទបណ្ណសន្យារ៉ាប់រង​​<span class="absolute right-0">:</span></div>
                    <div>Class of Policy</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block float-right">
                {!! "&nbsp;" !!} {{ !empty($credit->product_name) ? $credit->product_name : '-' }}
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>រយៈពេលធានារ៉ាប់រង<span class="absolute right-0">:</span></div>
                    <div>Period of Insurance</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block float-right">
                {!! "&nbsp;" !!} {{ $credit->insurance_period ?? '-' }}
            </div>
        </div>

        <div class="clearfix margin-bottom-2">
            <div class="w-1/3 inline-block float-left">
                <div class="relative">
                    <div class='font-khmer-os'>ថ្ងៃប្រសិទ្ធិភាពនៃបដ្ឋិលេខ<span class="absolute right-0">:</span></div>
                    <div>Endorsement Effective Date</div>
                </div>
            </div>
            <div class="w-2/3 padding-top-6 inline-block float-right">
                {!! "&nbsp;" !!}
                @if(empty($credit->endorsement_e_date))
                    <span> - </span>
                @else
                    <span> {{ \Carbon\Carbon::parse($credit->endorsement_e_date)->format('d/m/Y') }} </span>
                @endif
            </div>
        </div>

        <div class="clearfix mb-1">
            <table class="w-full border border-black border-collapse font-size-table">
                <tr>
                    <td class="font-bold w-1/2 text-center">
                        <span class='font-khmer-os'>សេចក្តីបរិយាយ</span> - <span>Description</span>
                    </td>
                    <td class="font-bold text-center">
                        <span class='font-khmer-os'>តម្លៃ</span> / <span>Amount</span>
                    </td>
                </tr>
                <tr>
                    <td class="padding-left-5">
                        <span class='font-khmer-os'>បុព្វលាភធានារ៉ាប់រង</span> - <span>Gross Premium</span>
                    </td>
                    @if(empty($credit->total_premuim))
                        <td class="text-center">-</td>
                    @else
                        @if($credit->total_premuim < 0)
                            <td class="font-bold text-center">{{ '$ ('.number_format(abs($credit->total_premuim) , 2).')' }}</td>
                        @else
                            <td class="font-bold text-center">{{ '$'.number_format($credit->total_premuim , 2) }}</td>
                        @endif
                    @endif
                </tr>
                <tr>
                    <td class="padding-left-5 font-bold w-1/2">
                        <span class='font-khmer-os'>តម្លៃសរុបរួម ​​(ដុល្លារអាមេរិក)</span> - <span>Total Amount (USD)</span>
                    </td>
                    @if(empty($credit->total_premuim))
                        <td class="text-center">-</td>
                    @else
                        @if($credit->total_premuim < 0)
                            <td class="font-bold text-center">{{ '$ ('.number_format(abs($credit->total_premuim) , 2).')' }}</td>
                        @else
                            <td class="font-bold text-center">{{ '$'.number_format($credit->total_premuim , 2) }}</td>
                        @endif
                    @endif
                </tr>
                <tr>
                    <td class="padding-left-5 font-bold w-1/2">
                        <span class='font-khmer-os'>តម្លៃសរុបរួម ​​(រៀល)</span> - <span>Total Amount (Riel)</span>
                    </td>
                    @if(empty($credit->total_premuim) || empty($credit->exch_rate))
                        <td class="text-center">-</td>
                    @else
                        @if($credit->total_premuim < 0)
                            <td class="font-bold text-center">{{ '('. number_format(round(abs($credit->total_premuim) * $credit->exch_rate), 2) . ')' }} <span class='font-khmer-os'>រៀល</span></td>
                        @else
                            <td class="font-bold text-center">{{ number_format(round($credit->total_premuim * $credit->exch_rate), 2) }} <span class='font-khmer-os'>រៀល</span></td>
                        @endif
                    @endif
                </tr>
                <tr>
                    <td class="padding-left-5">
                        <span class='font-khmer-os'>អត្រាប្តូរប្រាក់ ​​(រៀល)</span> - <span>Exchange Rate (Riel)</span>
                    </td>
                    @if(empty($credit->exch_rate))
                        <td class="text-center">-</td>
                    @else
                        <td class="text-center">{{ number_format($credit->exch_rate,2) }} <span class='font-khmer-os'>រៀល</span></td>
                    @endif
                </tr>
            </table>
        </div>

        <div class="clearfix font-size-mode-of-payment">
            <h4 class="font-bold mb-1">MODE OF PAYMENT:</h4>
            <p>
                Please follow the premium payment instructions to these below bank accounts:
            </p>
            <p class="ml-2">1. Input your invoice number(s)</p>
            <p class="ml-2">2. Input your premium amounts for the above invoice number(s)</p>
        </div>

        <div class="clearfix font-size-mode-of-payment">
            @foreach ($bank_list as $bank)
                <div class="w-1/2 inline-block float-left mt-2">
                    <div class="clearfix">
                        <div class="w-1/3 inline-block float-left font-bold">Bank Name:</div>
                        <div class="w-2/3 inline-block float-right font-bold">{{ $bank->name }}</div>
                    </div>
                    <div class="clearfix">
                        <div class="w-1/3 inline-block float-left font-bold">Account Name:</div>
                        <div class="w-2/3 inline-block float-right">{{ $bank->account_name }}</div>
                    </div>
                    <div class="clearfix">
                        <div class="w-1/3 inline-block float-left font-bold">Account No.:</div>
                        <div class="w-2/3 inline-block float-right">{{ $bank->account_no }}</div>
                    </div>
                    <div class="clearfix">
                        <div class="w-1/3 inline-block float-left font-bold">Currency:</div>
                        <div class="w-2/3 inline-block float-right">({{ $bank->ccy }})</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="avoid-break clearfix">
            <div class="w-1/3 inline-block float-right ">
                <div class="relative" style="min-height: 100px;">
                    @if ($signature && file_exists(public_path($signature->file_url)))
                        <img class="img-over" src="{{ public_path($signature->file_url) }}" style="max-height: 100px">
                    @endif
                </div>
                <div class="w-full"></div>
                <div class="text-center">
                    <hr class="border">
                    <p class="font-bold font-khmer-os">ហត្ថលេខាអ្នកតំណាងក្រុមហ៊ុន</p>
                    <p class="font-bold">Authorised Signature</p>
                </div>
            </div>
        </div>
    </body>
@endsection

<style>
    body {
        font-size: 9pt;
    }
    .font-size-title {
        font-size: 11pt !important;
    }
    .font-size-table {
        font-size: 8pt !important;
    }
    .font-size-mode-of-payment {
        font-size: 8pt !important;
    }
    .font-candara {
        font-family: Candara, sans-serif;
    }
    .font-khmer-os {
        font-family: KhmerOS !important;
    }
    .font-khmer-os-moul-light {
        font-family: KhmerOSMoulLight !important;
    }
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
    table, th, td {
        border: 1px solid #000 !important;
    }
    .avoid-break {
        page-break-inside: avoid;
    }
    .padding-top-6 {
        padding-top: 6px;
    }
    .padding-left-5 {
        padding-left: 5px
    }
    .margin-bottom-2 {
        margin-bottom: 2px
    }
</style>
