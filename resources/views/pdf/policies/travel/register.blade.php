<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th colspan="123">
                    <h2>PHILLIP GENERAL INSURANCE (CAMBODIA) PLC.</h2>
                </th>
            </tr>
            <tr>
                <th colspan="123">
                    <h3>POLICY REGISTER</h3>
                </th>
            </tr>
            <tr>
                <th colspan="123">
                    <h4> {{$reportPeriod}}</h4>
                </th>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">LOB</th>
                <th rowspan="2">Risk Type</th>
                <th rowspan="2">Policy/Endorsement No.</th>
                <th rowspan="2">Number of risk</th>
                <th rowspan="2">Reference Policy No. (Last Degist)</th>
                <th rowspan="2">Inception Date</th>
                <th rowspan="2">Expiry Date</th>
                <th rowspan="2">Number of Days</th>
                <th rowspan="2">Endorsement Effective Date</th>
                <th rowspan="2">Type of Client</th>
                <th rowspan="2">The Insured Name</th>
                <th rowspan="2">Risk Coverage</th>
                <th rowspan="2">Risk Occupation</th>

                <th colspan="6">For Property</th>

                <th>For Medical &amp; PA</th>
                <th>For Auto</th>

                <th rowspan="2">Location of Risk(Full address)</th>
                <th rowspan="2">Tower/ Block of Buildings, e.g. Borey</th>
                <th rowspan="2">Street No.</th>
                <th rowspan="2">Commune Name</th>
                <th rowspan="2">Commune Code</th>
                <th rowspan="2">Province/ City</th>
                <!-- For Corporate Customer-Local -->
                <th colspan="5">For Corporate Customer-Local</th>

                <th colspan="7">Foreign TIN Number (Registration number with their Tax Authority)</th>
                <th colspan="9">For Individual Customer:</th>

                <th rowspan="2">Total Sum Insured(USD)</th>
                <th rowspan="2">Gross Written Premium(USD)</th>
                <th rowspan="2">Handler Code</th>
                <th rowspan="2">Business Channel</th>
                <th rowspan="2">Business Code</th>
                <th rowspan="2">Source of Referral</th>
                <th rowspan="2">Type of Policy</th>
                <th rowspan="2">Referral Code</th>

                <th colspan="6">COMMISSION</th>
                <th colspan="7">CAMBODIA RE COMPULSORY CESSION</th>
                <th colspan="11">QUOTA SHARE CESSION</th>
                <th colspan="5">PGI RETENTION</th>
                <th colspan="8">OTHER TREATY CESSIONS</th>
                <th colspan="8">OUTWARD FACULTATIVE REINSURANCE</th>
                <th colspan="7">OUTWARD CO-INSURANCE</th>


                <th rowspan="2">Reinsurance Premium Ceded</th>
                <th rowspan="2">RI Capacity 100%</th>
                <th rowspan="2">Type of Policy</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">UW Year (Standard: Jan to Dec)</th>
                <th rowspan="2">UW Year (Quota Share Cession)</th>
                <th rowspan="2">Date of issue</th>
                <th rowspan="2">Accounting Month</th>
                <th rowspan="2">Invoice No.</th>
                <th rowspan="2">Issued by</th>
                <th rowspan="2">Approved by</th>
                <th rowspan="2">Unearned Premium Reserved</th>
                <th rowspan="2">Deferred Commission and Brokerage</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <!-- For Property -->
                <th>Risk Occupation Code</th>
                <th>Construction Class</th>
                <th>MD/LOP</th>
                <th>Add Perils Covred</th>
                <th>FEA Disc %</th>
                <th>Voluntary Deductible %</th>
                <!-- for medical -->
                <th>Numbers of Insured Persons</th>
                <!-- for auto -->
                <th>Numbers of Vehicles</th>
                <!-- For Corporate Customer-Local -->
                <th>TIN Code/Number</th>
                <th>Company Phone Number</th>
                <th>Contact Person</th>
                <th>Phone Number</th>
                <th>Email Address</th>
                <!-- Foreign TIN Number  -->
                <th>Foreign TIN Number <br> (Registration number with their Tax Authority) </th>
                <th>Name in Khmer</th>
                <th>Name in LATIN <br> (Capital Letter)</th>
                <th>Country</th>
                <th>Company Phone Number</th>
                <th>Email Address</th>
                <th>Company Full Address</th>
                <!-- For Individual Customer: -->
                <th>TID Number/National ID/Passport/ACmily Book</th>
                <th>Name in Khmer</th>
                <th>Name in LATIN (Capital Letter)</th>
                <th>Sex (Male/Female)</th>
                <th>Date of Birth (dd/mm/yyyy)</th>
                <th>National</th>
                <th>Nationality</th>
                <th>Phone Number</th>
                <th>Email Address</th>
                <!-- COMMISSION -->
                <th>Tax and Fee Amount 5.5% (USD)</th>
                <th>Net Written Premium(USD)</th>
                <th>Commission Rate (%)</th>
                <th>Commission Amount(USD)</th>
                <th>Withholding Tax Amount (USD)</th>
                <th>Commission Amount Due(USD)</th>
                <!-- CAMBODIA RE COMPULSORY CESSION -->
                <th>Share%</th>
                <th>Sum Insured Ceded</th>
                <th>Premium Ceded</th>
                <th>Tax &amp; Fee Amount (5.5%)</th>
                <th>RI. Comm.%</th>
                <th>RI. Comm. Amount</th>
                <th>Net Due</th>
                <!-- QUOTA SHARE CESSION -->
                <th>Share%</th>
                <th>Sum Insured Ceded</th>
                <th>Premium Ceded</th>
                <th>Tax &amp; Fee Amount (5.5%)</th>
                <th>RI. Comm. % from Re</th>
                <th>RI. Comm. Amount From re</th>
                <th>RI. Comm. % from Hub</th>
                <th>RI. Comm. Amount from Hub Risk</th>
                <th>RI. Comm. % In Sytem </th>
                <th>RI. Comm. Amount In Sytem </th>
                <th>Net Due</th>
                <!-- PGI RETENTION -->
                <th>Share%</th>
                <th>Sum Insured Retented</th>
                <th>Premium Retented</th>
                <th>Tax &amp; Fee Amount (5.5%)</th>
                <th>Net Retention</th>
                <!-- OTHER TREATY CESSIONS -->
                <th>Treay Name</th>
                <th>Share%</th>
                <th>Sum Insured Ceded</th>
                <th>Premium Ceded</th>
                <th>Tax &amp; Fee Amount (5.5%)</th>
                <th>RI. Comm.%</th>
                <th>RI. Comm. Amount</th>
                <th>Net Due</th>
                <!-- OUTWARD FACULTATIVE REINSURANCE -->
                <th>Reinsurer</th>
                <th>Share%</th>
                <th>Sum Insured Ceded</th>
                <th>Premium Ceded</th>
                <th>Tax &amp; Fee Amount (5.5%)</th>
                <th>RI. Comm.%</th>
                <th>RI. Comm. Amount</th>
                <th>Net Due</th>
                <!-- OUTWARD CO-INSURANCE -->
                <th>Co-Insurer</th>
                <th>Share%</th>
                <th>Sum Insured Ceded</th>
                <th>Premium Ceded</th>
                <th>Co-Free + Agency Comm.%</th>
                <th>Co-Free + Agency Comm. Amount</th>
                <th>Net Due</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->cursor() as $key => $policy)
                        @php
                            if (!is_null($policy->reinsure_other_treaty_name)) {
                                $policy->reinsure_other_treaty_name = @$treaties[$policy->reinsure_other_treaty_name];
                            }
                        @endphp
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$policy->lob}}</td>
                            <td>{{$policy->risk_type}}</td>
                            <td>{{$policy->document_no}}
                            <td>{{$policy->number_of_risk}}</td>
                            <td>{{$policy->ref_policy_no}}</td>
                            <td>{{$policy->inception_date}}</td>
                            <td>{{$policy->expiry_date}}</td>
                            <td>{{$policy->number_of_day}}</td>
                            <td>{{$policy->endorsement_effect_date}}</td>
                            <td>{{$policy->client_type}}</td>
                            <td>{{$policy->insured_name}}</td>
                            <td>{{$policy->risk_coverage}}</td>
                            <td>{{$policy->risk_occupation}}</td>
                            <!-- // For Property -->
                            <td>{{$policy->risk_occupation_code}}</td>
                            <td>{{$policy->construction_class}}</td>
                            <td>{{$policy->md_lop}}</td>
                            <td>{{$policy->add_perils_covered}}</td>
                            <td>{{$policy->fea_disc}}</td>
                            <td>{{$policy->voluntary_deductible}}</td>
                            <!-- // For Medical & PA -->
                            <td>{{$policy->number_of_insured_person}}</td>
                            <!-- // For Auto -->
                            <td>{{$policy->number_of_vehicle}}</td>
                            <!-- // For system -->
                            <td>{{$policy->full_address_en}}</td>
                            <!-- // -->
                            <td>{{$policy->tower_block_of_building}}</td>
                            <td>{{$policy->street_no}}</td>
                            <td>{{$policy->commune_name}}</td>
                            <td>{{$policy->commune_code}}</td>
                            <td>{{$policy->city}}</td>
                            <!-- // For Corporate Customer-Local -->
                            <td>{{$policy->cl_tin_code}}</td>
                            <td>{{$policy->cl_company_phone}}</td>
                            <td>{{$policy->cl_contact_person}}</td>
                            <td>{{$policy->cl_phone_number}}</td>
                            <td>{{$policy->cl_email_address}}</td>
                            <!-- // For Corporate Customer-Abroad -->
                            <td>{{$policy->ca_tin_code}}</td>
                            <td>{{$policy->ca_name_kh}}</td>
                            <td>{{$policy->ca_name_en}}</td>
                            <td>{{$policy->ca_country}}</td>
                            <td>{{$policy->ca_company_phone}}</td>
                            <td>{{$policy->ca_email_address}}</td>
                            <td>{{$policy->ca_full_address}}</td>
                            <!-- // For Individual Customer -->
                            <td>{{$policy->ic_national_id}}</td>
                            <td>{{$policy->ic_name_kh}}</td>
                            <td>{{$policy->ic_name_en}}</td>
                            <td>{{$policy->ic_gender}}</td>
                            <td>{{$policy->ic_dob}}</td>
                            <td>{{$policy->ic_national}}</td>
                            <td>{{$policy->ic_nationality}}</td>
                            <td>{{$policy->ic_phone_number}}</td>
                            <td>{{$policy->ic_email}}</td>
                            <!-- // -->
                            <td>{{$policy->total_sum_insured_usd < 0 ? '(' . number_format(abs($policy->total_sum_insured_usd), 2, '.', ',') . ')' : $policy->total_sum_insured_usd}}
                            </td>
                            <td>{{$policy->gross_writen_premium < 0 ? '(' . number_format(abs($policy->gross_writen_premium), 2, '.', ',') . ')' : $policy->gross_writen_premium}}
                            </td>
                            <td>{{$policy->handler_code}}</td>
                            <td>{{$policy->sale_channel}}</td>
                            <td>{{$policy->business_code}}</td>
                            <td>{{$policy->source_of_referal}}</td>
                            <td>{{$policy->type_of_policy}}</td>
                            <td>{{$policy->referral_code}}</td>


                            <td>{{$policy->tax_and_fee_amount_usd < 0 ? '(' . number_format(abs($policy->tax_and_fee_amount_usd), 2, '.', ',') . ')' : $policy->tax_and_fee_amount_usd}}
                            </td>
                            <td>{{$policy->net_writen_premuium < 0 ? '(' . number_format(abs($policy->net_writen_premuium), 2, '.', ',') . ')' : $policy->net_writen_premuium}}
                            </td>
                            <td>{{$policy->commission_rate * 100}}</td>
                            <td>{{$policy->commission_amt < 0 ? '(' . number_format(abs($policy->commission_amt), 2, '.', ',') . ')' : $policy->commission_amt}}
                            </td>
                            <td>{{$policy->with_holding_amt < 0 ? '(' . number_format(abs($policy->with_holding_amt), 2, '.', ',') . ')' : $policy->with_holding_amt}}
                            </td>
                            <td>{{$policy->commission_amt_due < 0 ? '(' . number_format(abs($policy->commission_amt_due), 2, '.', ',') . ')' : $policy->commission_amt_due}}
                            </td>
                            <!-- // CAMBODIA RE COMPULSORY CESSION -->
                            <td>{{$policy->cambodia_re_share * 100}}</td>
                            <td>{{$policy->cambodia_re_sum_insured_ceded < 0 ? '(' . number_format(abs($policy->cambodia_re_sum_insured_ceded), 2, '.', ',') . ')' : $policy->cambodia_re_sum_insured_ceded}}
                            </td>
                            <td>{{$policy->cambodia_re_premium_ceded < 0 ? '(' . number_format(abs($policy->cambodia_re_premium_ceded), 2, '.', ',') . ')' : $policy->cambodia_re_premium_ceded}}
                            </td>
                            <td>{{$policy->cambodia_re_tax_fee_amounts < 0 ? '(' . number_format(abs($policy->cambodia_re_tax_fee_amounts), 2, '.', ',') . ')' : $policy->cambodia_re_tax_fee_amounts}}
                            </td>
                            <td>{{$policy->cambodia_re_ri_commissions * 100}}</td>
                            <td>{{$policy->cambodia_re_ri_commission_amount < 0 ? '(' . number_format(abs($policy->cambodia_re_ri_commission_amount), 2, '.', ',') . ')' : $policy->cambodia_re_ri_commission_amount}}
                            </td>
                            <td>{{$policy->cambodia_re_net_due < 0 ? '(' . number_format(abs($policy->cambodia_re_net_due), 2, '.', ',') . ')' : $policy->cambodia_re_net_due}}
                            </td>
                            <!-- // QUOTA SHARE CESSION -->
                            <td>{{$policy->quota_share_share * 100}}</td>
                            <td>{{$policy->quota_share_sum_insured_ceded < 0 ? '(' . number_format(abs($policy->quota_share_sum_insured_ceded), 2, '.', ',') . ')' : $policy->quota_share_sum_insured_ceded}}
                            </td>
                            <td>{{$policy->quota_share_total_premium_ceded < 0 ? '(' . number_format(abs($policy->quota_share_total_premium_ceded), 2, '.', ',') . ')' : $policy->quota_share_total_premium_ceded}}
                            </td>
                            <td>{{$policy->quota_share_tax_fee_amount < 0 ? '(' . number_format(abs($policy->quota_share_tax_fee_amount), 2, '.', ',') . ')' : $policy->quota_share_tax_fee_amount}}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{$policy->quota_share_ri_commission * 100}}</td>
                            <td>{{$policy->quota_share_ri_commission_amount < 0 ? '(' . number_format(abs($policy->quota_share_ri_commission_amount), 2, '.', ',') . ')' : $policy->quota_share_ri_commission_amount}}
                            </td>
                            <td>{{$policy->quota_share_net_due < 0 ? '(' . number_format(abs($policy->quota_share_net_due), 2, '.', ',') . ')' : $policy->quota_share_net_due}}
                            </td>
                            <!-- // PGI RETENTION -->
                            <td>{{$policy->pgi_retention_share * 100}}</td>
                            <td>{{$policy->pgi_retention_sum_insured_ceded < 0 ? '(' . number_format(abs($policy->pgi_retention_sum_insured_ceded), 2, '.', ',') . ')' : $policy->pgi_retention_sum_insured_ceded}}
                            </td>
                            <td>{{$policy->pgi_retention_premium_ceded < 0 ? '(' . number_format(abs($policy->pgi_retention_premium_ceded), 2, '.', ',') . ')' : $policy->pgi_retention_premium_ceded}}
                            </td>
                            <td>{{$policy->pgi_retention_tax_fee_amount < 0 ? '(' . number_format(abs($policy->pgi_retention_tax_fee_amount), 2, '.', ',') . ')' : $policy->pgi_retention_tax_fee_amount}}
                            </td>
                            <td>{{$policy->pgi_retention_net_due < 0 ? '(' . number_format(abs($policy->pgi_retention_net_due), 2, '.', ',') . ')' : $policy->pgi_retention_net_due}}
                            </td>
                            <!-- // OTHER TREATY CESSIONS -->
                            <td>{{$policy->reinsure_other_treaty_name}}</td>
                            <td>{{$policy->reinsure_other_share * 100}}</td>
                            <td>{{$policy->reinsure_other_sum_insured_ceded < 0 ? '(' . number_format(abs($policy->reinsure_other_sum_insured_ceded), 2, '.', ',') . ')' : $policy->reinsure_other_sum_insured_ceded}}
                            </td>
                            <td>{{$policy->reinsure_other_premium_ceded < 0 ? '(' . number_format(abs($policy->reinsure_other_premium_ceded), 2, '.', ',') . ')' : $policy->reinsure_other_premium_ceded}}
                            </td>
                            <td>{{$policy->reinsure_other_tax_fee_amt < 0 ? '(' . number_format(abs($policy->reinsure_other_tax_fee_amt), 2, '.', ',') . ')' : $policy->reinsure_other_tax_fee_amt}}
                            </td>
                            <td>{{$policy->reinsure_other_ri_commission * 100}}</td>
                            <td>{{$policy->reinsure_other_ri_commission_amount < 0 ? '(' . number_format(abs($policy->reinsure_other_ri_commission_amount), 2, '.', ',') . ')' : $policy->reinsure_other_ri_commission_amount}}
                            </td>
                            <td>{{$policy->reinsure_other_net_due < 0 ? '(' . number_format(abs($policy->reinsure_other_net_due), 2, '.', ',') . ')' : $policy->reinsure_other_net_due}}
                            </td>
                            <!-- // OUTWARD FACULTATIVE REINSURANCE -->
                            <td>{{$policy->facul_treaty_name}}</td>
                            <td>{{$policy->facul_share * 100}}</td>
                            <td>{{$policy->facul_sum_insured_ceded < 0 ? '(' . number_format(abs($policy->facul_sum_insured_ceded), 2, '.', ',') . ')' : $policy->facul_sum_insured_ceded}}
                            </td>
                            <td>{{$policy->facul_premium_ceded < 0 ? '(' . number_format(abs($policy->facul_premium_ceded), 2, '.', ',') . ')' : $policy->facul_premium_ceded}}
                            </td>
                            <td>{{$policy->facul_tax_fee_amounts < 0 ? '(' . number_format(abs($policy->facul_tax_fee_amounts), 2, '.', ',') . ')' : $policy->facul_tax_fee_amounts}}
                            </td>
                            <td>{{$policy->facul_ri_commissions * 100}}</td>
                            <td>{{$policy->facul_ri_commission_amount < 0 ? '(' . number_format(abs($policy->facul_ri_commission_amount), 2, '.', ',') . ')' : $policy->facul_ri_commission_amount}}
                            </td>
                            <td>{{$policy->facul_net_due < 0 ? '(' . number_format(abs($policy->facul_net_due), 2, '.', ',') . ')' : $policy->facul_net_due}}
                            </td>
                            <!-- OUTWARD CO-INSURANCE -->
                            <td>{{$policy->outward_treaty_name}}</td>
                            <td>{{$policy->outward_share * 100}}</td>
                            <td>{{$policy->outward_sum_insured_ceded < 0 ? '(' . number_format(abs($policy->outward_sum_insured_ceded), 2, '.', ',') . ')' : $policy->outward_sum_insured_ceded}}
                            </td>
                            <td>{{$policy->outward_premium_ceded < 0 ? '(' . number_format(abs($policy->outward_premium_ceded), 2, '.', ',') . ')' : $policy->outward_premium_ceded}}
                            </td>
                            <td>{{$policy->outward_ri_commissions * 100}}</td>
                            <td>{{$policy->outward_ri_commission_amount < 0 ? '(' . number_format(abs($policy->outward_ri_commission_amount), 2, '.', ',') . ')' : $policy->outward_ri_commission_amount}}
                            </td>
                            <td>{{$policy->outward_net_due < 0 ? '(' . number_format(abs($policy->outward_net_due), 2, '.', ',') . ')' : $policy->outward_net_due}}
                            </td>

                            <td>{{$policy->reinsurance_premium_ceded < 0 ? '(' . number_format(abs($policy->reinsurance_premium_ceded), 2, '.', ',') . ')' : $policy->reinsurance_premium_ceded}}
                            </td>
                            <td>{{$policy->ri_capacity}}</td>
                            <td>{{$policy->type_of_policy}}</td>
                            <td>{{$policy->status}}</td>
                            <td>{{$policy->uw_year_standard}}</td>
                            <td>{{$policy->uw_year_quota_share}}</td>
                            <td>{{date('d-M-Y',strtotime($policy->issued_date))}}</td>
                            <td>{{$policy->accounting_month}}</td>
                            <td>{{$policy->invoice_no}}</td>
                            <td>{{$policy->issued_by}}</td>
                            <td>{{$policy->approved_by}}</td>
                            <td>{{$policy->unearned_premium_reserved}}</td>
                            <td>{{$policy->deferred_commission_and_brokerage}}</td>
                            <td>{!! $policy->remark !!}</td>
                        </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>