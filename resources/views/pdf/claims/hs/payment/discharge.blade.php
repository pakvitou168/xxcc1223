@extends('pdf.layout')

@section('content')

<body class="p-15 my-0 font-candara {{ App::isLocale('km') ? 'font-khmerOS' : 'font-candara' }}">
    <div class="break-page">
        <div class="w-full text-center mb-3">
            <p class="inline-block text-xl font-bold leading-8">
                ប័ណ្ណទទួលយកការទូទាត់សំណង {{__('CLAIM DISCHARGE VOUCHER')}}
            </p>
        </div>
    </div>

    <div class="w-full ">
        <div class="w-full float-left text-right font-bold">
            <p class="p-5 text-xl">
                នាយកដ្ឋានដោះស្រាយសំណង {{__('CLAIMS DEPARTMENT')}}
            </p>
        </div>
        <table class="w-full" style="border:none; border-collapse:collapse; cellspacing:0; cellpadding:0">
            <tr>
                <td style="width: 45%">
                    លេខទាមទារសំណង/{{__('CLAIM NUMBER')}}
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['EN']['claim_no'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    លេខបណ្ណសន្យារ៉ាប់រង/{{__('POLICY NUMBER')}}
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['EN']['policy_no'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    ម្ចាស់បណ្ណសន្យារ៉ាប់រង
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['KM']['policy_holder'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    {{__('POLICY HOLDER')}}
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['EN']['policy_holder'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    អ្នកទាមទារសំណង <br><br>{{__('CLAIMANT')}}
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['KM']['claimant_name'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    ក្នុងករណី
                    <br><br>{{__('DISABILITY')}}
                </td>
                <td style="width: 55%" class="font-khmerOS">
                    : &nbsp; {{ $data['EN']['cause_of_loss_disability'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    កាលបរិច្ឆេទគ្រោះថ្នាក់ឬឈឺចូលពេទ្យ
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['KM']['date_of_disability'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    {{__('DATE OF DISABILITY')}}
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['EN']['date_of_disability'] }}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    សំណងដែលទូទាត់បាន
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ 'USD ' . number_format($data['KM']['amount'], 2) }} {{$data['KM']['amount_in_words']}}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    {{__('CLAIM PAYABLE')}}
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ 'USD ' . number_format($data['EN']['amount'], 2)  }} {{$data['EN']['amount_in_words']}}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    សំណងដែលមិនទូទាត់
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ 'USD ' . number_format($data['KM']['total_non_payable_expense'], 2) }} {{$data['KM']['non_payable_in_words']}}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    {{__('NON PAYABLE')}}
                </td>
                <td style="width: 55%">
                : &nbsp; {{ 'USD ' . number_format($data['EN']['total_non_payable_expense'], 2) }} {{$data['EN']['non_payable_in_words']}}
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    {{__('DUE TO')}}<br><br> មូលហេតុមិនទូទាត់
                </td>
                <td style="width: 55%">
                    : &nbsp; {{ $data['EN']['due_to'] }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>ខ្ញុំ/
                        I,.................................................,តំណាងស្របច្បាប់របស់អ្នកត្រូវបានធានាឬអ្នកទាមទារសំណង
                        THE LEGAL REPRESENTATIVE OF THE INSURED/CLAIMANT បានទទួលយកសំណងពីក្រុមហ៊ុនហ្វីលីពចេណឺរល អ៊ីនសួរេន
                        (ខេមបូឌា) ម.ក HAVE RECEIVED FROM PHILLIP GENERAL INSURANCE (CAMBODIA) PLC,
                        ដោយ​ការ​ពេញ​ចិត្ត​ទទួល​យក​ពេញ​លេញ​និង​ជា​ចុង​ក្រោយ​នៃការ​ទាម​ទារ​សំណង​
                        និង​យល់​ព្រម​បញ្ចប់​ការ​ទាម​ទារ​សំណង​ទាំង​អស់​ពី ​ក្រុម​ហ៊ុន​ហ្វី​លីព​ចេណឺរល ​អ៊ីន​សួរេន​
                        (ខេមបូឌា) ​ម.ក ​យោង​តាម​បណ្ណ​សន្យារ៉ាប់​រង​ខាង​លើ/
                        IN FULL AND FINAL SATISFACTION OF CLAIM PAYABLE AND AGREE TO DISCHARGE ALL CLAIMS,MADE OR TO BE
                        MADE, AGAINST PHILLIP GENERAL INSURANCE (CAMBODIA) PLC, UNDER THE ABOVE REFERENCE POLICY,</p>
                </td>
            </tr>
            <tr>
                <td style="width: 45%">
                    ក្រោមការព្យាបាលនៃ/{{__('IN RESPECT OF TREATMENT OF')}}
                </td>
                <td style="width: 55%">
                    : &nbsp;{{ $data['KM']['cause_of_loss'] }}/ {{ $data['EN']['cause_of_loss'] }}
                </td>
            </tr>

        </table>
        <div class="avoid-break">
            <div class="w-full  float-left {{ App::isLocale('km') ? 'mt-7 mb-7' : 'mt-10 mb-8' }}"
                style="border-bottom: 1px solid #000">
                ហត្ថាលេខា {{__('SIGNATURE')}}:
            </div>
            <div class="w-full float-left {{ App::isLocale('km') ? 'mt-8 mb-7' : 'mt-10 mb-8' }}"
                style="border-bottom: 1px solid #000">
                ត្រាក្រុមហ៊ុន {{__("COMPANY'S STAMP")}}:
            </div>
            <div class="w-full  float-left {{ App::isLocale('km') ? 'mt-8 mb-7' : 'mt-10 mb-8' }}"
                style="border-bottom: 1px solid #000">
                ឈ្មោះ {{__('NAME')}}:
            </div>
            <div class="w-full  float-left {{ App::isLocale('km') ? 'mt-8 mb-7' : 'mt-10 mb-8' }}"
                style="border-bottom: 1px solid #000">
                តួនាទី {{__('TITLE')}}:
            </div>
            <div class="w-full  float-left {{ App::isLocale('km') ? 'mt-8' : 'mt-10' }}"
                style="border-bottom: 1px solid #000">
                កាលបរិច្ឆេទ {{__('DATE')}}:
            </div>
        </div>
    </div>
</body>
@endsection

<style>
    .p-15 {
        padding-left: 4rem;
        padding-right: 4rem;
    }

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
        /* border: 1px solid #000 !important; */
    }

    td {
        display: flex;
        align-items: center;
        padding: 10px 3px;
        vertical-align: top;
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    .font-size-title {
        font-size: 15pt !important;
    }

    .break-page {
        page-break-before: always;
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
</style>