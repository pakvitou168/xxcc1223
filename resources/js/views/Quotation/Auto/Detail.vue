<template>
    <div>
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
            <h2 class="text-lg font-medium mr-auto">
                Auto Quotation Detail
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">

                <button v-if="canProceedToPolicy" class="btn btn-primary shadow-md mr-2" title="Proceed to Policy"
                    @click="proceedToPolicy">
                    <span class="h-6 leading-6">Proceed to Policy</span>
                </button>

                <button v-if="canApproveCond" class="btn btn-primary shadow-md mr-2" title="Approve Quote"
                    @click="openApproveDialog">
                    <span class="h-6 leading-6">Quote Approval</span>
                </button>.

                <button v-if="canAcceptCond" class="btn btn-primary shadow-md mr-2" title="Accept Quote"
                    @click="openAcceptDialog">
                    <span class="h-6 leading-6">Quote Acceptance</span>
                </button>

                <button v-if="canClone" class="btn btn-warning shadow-md mr-2" title="Generate New Version"
                    @click="cloneQuote">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                </button>
                <button class="btn btn-success shadow-md mr-2" title="Export Vehicles"
                    v-if="loadedVehicle && formValues.vehicles.length > 1" @click="exportVehicles">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </button>
                <div class="dropdown">
                    <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print Quote"
                        id="print-button">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                    </button>
                    <div class="dropdown-menu w-60">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLink" target="_blank">Letterhead (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLink" target="_blank">No Letterhead (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLink" target="_blank">Signature with No letterhead
                                (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLinkKh" target="_blank">Letterhead (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLinkKh" target="_blank">No Letterhead (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLinkKh" target="_blank">Signature with No
                                letterhead (KH)</a>
                        </div>
                    </div>
                </div>
                <button v-if="canDeleteCond" class="btn btn-danger mr-2 intro-x" title="Delete"
                    v-on:click="handleDelete(id)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
                <router-link v-if="canUpdateCond" :to="{ name: 'QuotationAutoEdit', params: { id: id } }">
                    <button class="btn btn-primary intro-x" title="Edit">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </button>
                </router-link>
            </div>
        </div>
        <Toast />
        <div class="intro-y box overflow-hidden mt-5">
            <div class="text-center">
                <div class="pt-10">
                    <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
                        <span>{{ product_insurance_name }}</span>
                    </div>
                    <div class="mt-2 text-xl text-center">INSURANCE QUOTATION</div>
                </div>
                <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
                    <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
                        <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">Quotation No: <span>{{
                            document_no }}</span></div>
                        <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">Business Code: {{
                            formValues.business_code }}</div>
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-5">
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">THE INSURED NAME:</div>
                    </div>
                    <div class="w-2/3">
                        <!-- <div class="text-md font-bold mb-3">ABC Company</div> -->
                        <div class="text-md font-bold mb-3"><span>{{ formValues.insured_name }}</span></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">CORRESPONDENCE ADDRESS:</div>
                    </div>
                    <div class="w-2/3">
                        <!-- <div class="text-md font-medium mb-3">No. 27DEF, Preah Monivong Blvd, Sangkat Srah Chork, Khan Daun Penh, Phnom Penh, Kingdom of Cambodia.</div> -->
                        <div class="text-md font-medium mb-3"><span>{{ customer_address }}</span></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">BUSINESS / OCCUPATION:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ formValues.customer_classification }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">PERIOD OF INSURANCE:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3"> {{ effectivePeriod }} - {{ periodOfInsurance }}</div>
                    </div>
                </div>
                <div class="flex" v-if="loadedVehicle">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">COVERAGE:</div>
                    </div>
                    <div class="w-2/3">
                        <div v-for="item in coverage" :key="item.code">
                            <div class="text-sm font-bold mb-1">{{ item.name }} ({{ item.code }})</div>
                            <div class="text-sm mb-2" v-html="item.html_detail"></div>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">POLICY WORDING:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-sm">Subject to {{ product_insurance_name }} Policy Wording
                            ({{ formValues.policy_wording_version }})</div>
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-16">
                <div class="flex" v-if="loadedVehicle && formValues.vehicles.length > 1">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">INSURED VEHICLE:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-sm mb-3 underline cursor-pointer btn-attach" @click="exportVehicles">{{
                            formValues.vehicles.length + ' units as per list attached' }}</div>
                    </div>
                </div>
                <div class="flex" v-if="loadedVehicle && formValues.vehicles.length > 1">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">TOTAL SUM INSURED:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-sm font-bold mb-3"> {{ formValues.sum_insured   }}</div>
                    </div>
                </div>
                <div class="flex" v-if="loadedVehicle && formValues.vehicles.length > 1">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">TOTAL PREMIUM:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-sm font-bold mb-3"> {{ formValues.total_premium   }}</div>
                    </div>
                </div>
                <div class="table-responsive" v-if="loadedVehicle && formValues.vehicles.length == 1">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="border-b-2 whitespace-nowrap">Make and Model</th>
                                <th class="border-b-2 whitespace-nowrap">Plate No.</th>
                                <th class="border-b-2 whitespace-nowrap">Chassis No.</th>
                                <th class="border-b-2 whitespace-nowrap">Engine No.</th>
                                <th class="border-b-2 whitespace-nowrap">Year of Manufacture</th>
                                <th v-if="isCommercialVehicle" class="border-b-2 whitespace-nowrap">Seats/Tonnage</th>
                                <th v-if="!isCommercialVehicle" class="border-b-2 whitespace-nowrap">Cubic Capacity/Engine Power</th>
                                <th class="border-b-2 whitespace-nowrap">Sum Insured (USD)</th>
                                <th class="border-b-2 whitespace-nowrap">Premium (USD)</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="vehicle in formValues.vehicles" :key="vehicle.id">
                                <td class="border-b">{{ vehicle.make_name }} {{ vehicle.model_name }}</td>
                                <td class="border-b">{{ vehicle.plate_no }}</td>
                                <td class="border-b">{{ vehicle.chassis_no }}</td>
                                <td class="border-b">{{ vehicle.engine_no }}</td>
                                <td class="border-b">{{ vehicle.manufacturing_year }}</td>
                                <td v-if="isCommercialVehicle" class="border-b">{{ vehicle.passenger_tonnage }}</td>
                                <td v-if="!isCommercialVehicle" class="border-b">{{ vehicle.cubic }}</td>
                                <td class="border-b">{{ formatCurrency(vehicle.vehicle_value)   }}</td>
                                <td class="border-b">{{ formatCurrency(vehicle.premium)   }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-5">
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">LIMITATION AS TO USE:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ limitToUse }}</div>
                    </div>
                </div>
                <div v-if="loadedVehicle">
                    <div v-if='formValues.vehicles[0]?.discount && formValues.vehicles.length == 1' class="flex">
                        <div class="w-1/3">
                            <div class="text-md font-bold mb-3">DISCOUNT:</div>
                        </div>
                        <div class="w-2/3">
                            <div class="text-md font-medium mb-3">{{ discount }}</div>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="w-1/3">
                            <div class="text-md font-bold mb-3">NO CLAIM DISCOUNT:</div>
                        </div>
                        <div class="w-2/3" v-if="formValues.vehicles.length == 1">
                            <div class="text-md font-medium mb-3">{{ ncd }}</div>
                        </div>
                        <div class="w-2/3" v-else-if="formValues.vehicles.length > 1">
                            <div class="text-sm font-bold mb-2">As per list attached</div>
                        </div>
                    </div>
                </div>

                <div class="flex" v-if="loadedVehicle">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">DEDUCTIBLE:</div>
                    </div>
                    <div class="w-2/3" v-if="formValues.vehicles.length == 1">
                        <div class="text-sm font-bold mb-2">It is applicable to each and every claim for:</div>
                        <div class="text-sm mb-2" v-for="item in deductibles" :key="item.comp_code">
                            {{ item.cover ? item.cover.deductible_label : '' }}: {{ item.value }}
                        </div>
                    </div>
                    <div class="w-2/3" v-else-if="formValues.vehicles.length > 1">
                        <div class="text-sm font-bold mb-2">As per list attached</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ENDORSEMENTS/CLAUSES:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3" v-for="endorsement in formValues.endorsement_clause"
                            :key="endorsement.id">
                            {{ endorsement.clause }}
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">GENERAL EXCLUSIONS:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3" v-for="exclusion in formValues.general_exclusive"
                            :key="exclusion.id">
                            {{ exclusion.clause }}
                        </div>
                    </div>
                </div>
                <div v-if="formValues.warranty" class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">WARRANTY:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3" v-html="formValues.warranty"></div>
                    </div>
                </div>
                <div v-if="formValues.memorandum" class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">MEMORANDUM:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3" v-html="formValues.memorandum"></div>
                    </div>
                </div>
                <div v-if="formValues.subjectivity" class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">SUBJECTIVITY:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3" v-html="formValues.subjectivity"></div>
                    </div>
                </div>
                <div v-if="formValues.remark" class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">REMARK:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3" v-html="formValues.remark"></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">JURISDICTION:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">Kingdom of Cambodia</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">QUOTATION VALIDITY:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">30 days from the issuance date</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ISSUED ON:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ issuedDate }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ISSUED BY:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ formValues.issued_by }}</div>
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-5">
                <div class="flex">
                    <div class="w-auto">
                        <div class="text-md font-bold mb-3 uppercase">Phillip General Insurance (Cambodia) Plc.</div>
                        <div class="my-2" v-bind:class="{ 'relative': signature }" style="min-height: 150px;">
                            <img v-if="signature" class="img-over" :src="'/' + signature.file_url"
                                style="max-height: 150px" alt="">
                            <img v-if="signature" class="img-under" src="/images/stamp/phillip_insurance.png"
                                style="max-height: 150px" alt=""
                        </div>

                        <hr class="my-3">

                        <div class="text-md mb-3 font-medium">Authorised Signature</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3"></div>
                    <div class="w-2/3">
                        <div class="text-md mb-3 font-bold pt-1"
                            style="text-decoration-line: underline;text-decoration-style: double;">ACCEPTANCE BY CLIENT:
                        </div>
                        <div class="text-md font-medium mb-3">
                            We examine and understand the above terms and premium payment. We hereby accept and agree to
                            the terms to issue the Policy with an effective on
                            ...................................................
                        </div>
                    </div>
                </div>
                <br>
                <div class="flex mt-12">
                    <div class="w-1/3">
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium pt-1 border-t border-gray-200">Authorised Signature (Company
                            Stamp if Applicable)</div>
                    </div>
                </div>
            </div>
        </div>
        <ReasonDialog :isVisible="approveDialog" header="Quote Approval" :errors="errors" :loading="loading"
            :options="[{ label: 'Approve', value: 'APV' }, { label: 'Reject', value: 'REJ' }]" value="APV"
            @hideDialog="hideDialog" @confirm="approve" />

        <ReasonDialog :isVisible="acceptDialog" header="Quote Acceptance" :submitted="submitted" :loading="loading"
            :options="[{ label: 'Accept', value: 'ACP' }, { label: 'Reject', value: 'REJ' }]" value="ACP"
            @hideDialog="hideDialog" @confirm="accept" />
    </div>
</template>

<script>
import {formatCurrency} from '@/helpers'
import moment from 'moment'
import UserPermissions from '@/mixins/UserPermissions'
import ReasonDialog from './ReasonDialog.vue'

export default {
    mixins: [UserPermissions],

    components: {
        ReasonDialog
    },

    data() {
        return {
            id: this.$route.params.id ?? null,
            functionCode: 'AUTO',
            formValues: {},
            coverage: [],
            deductibles: [],
            loadedVehicle: false,
            signature: null,
            approveDialog: false,
            acceptDialog: false,
            submitted: false,
            hasPendingPolicy: false,
            loading: false,
            errors: [],
            notifyRef: null
        }
    },
    computed: {
        document_no() {
            return this.formValues.quotation ? this.formValues.quotation.document_no : null
        },
        customer_insured() {
            return this.formValues.customer ? this.formValues.customer.name_en : null
        },
        customer_address() {
            return this.formValues.customer ? this.handleCustomerAddress(this.formValues.customer.address_en, this.formValues.customer.village_en, this.formValues.addressData, this.formValues.country) : null
        },
        product_insurance_name() {
            return this.formValues.product ? this.formValues.product.name : null
        },
        issuedDate() {
            var date = this.formValues.updated_at ?? this.formValues.created_at
            return moment(date).format('DD/MM/YYYY')
        },
        printWithLetterHeadLink() {
            return `/auto-service/${this.id}/download-quotation/en?letterhead=1`
        },
        printWithoutLetterHeadLink() {
            return `/auto-service/${this.id}/download-quotation/en?letterhead=0`
        },
        printWithoutLetterHeadAndStampLink() {
            return `/auto-service/${this.id}/download-quotation/en?letterhead=0&noStamp=1`
        },
        printWithLetterHeadLinkKh() {
            return `/auto-service/${this.id}/download-quotation/km?letterhead=1`
        },
        printWithoutLetterHeadLinkKh() {
            return `/auto-service/${this.id}/download-quotation/km?letterhead=0`
        },
        printWithoutLetterHeadAndStampLinkKh() {
            return `/auto-service/${this.id}/download-quotation/km?letterhead=0&noStamp=1`
        },
        ncd() {
            return `${this.formValues.vehicles[0].ncd ?? 0} %`
        },
        discount() {
            return `${this.formValues.vehicles[0]?.discount} %`
        },
        canApproveCond() {
            // If don't have permission to approve
            if (!this.canApproveByCode('QUOTATION')) return false

            if (!this.formValues.quotation) return false

            // If not yet approved
            return this.formValues.quotation.approved_status === 'PND' ? true : false;
        },
        canDeleteCond() {
            // If already approved
            if (this.formValues.quotation && this.formValues.quotation.approved_status === 'APV') return false;
            return this.canDelete
        },
        canAcceptCond() {
            // If don't have permission to accept
            if (!this.canAcceptByCode('QUOTATION')) return false

            if (!this.formValues.quotation) return false

            // If not approved
            if (this.formValues.quotation.approved_status !== 'APV') return false

            // If not yet accepted
            return this.formValues.quotation.accepted_status === 'PND' ? true : false;
        },
        canUpdateCond() {
            // If already approved
            if (this.formValues.quotation && this.formValues.quotation.approved_status !== 'PND') return false

            return this.canUpdate
        },
        canClone() {
            return this.hasPendingPolicy
        },
        isCommercialVehicle() {
            return this.hasPassengerOrTonnage
        },
        hasPassengerOrTonnage() {
            return this.formValues.has_passenger_tonnage
        },

        canProceedToPolicy() {
            if (!this.formValues.quotation) return false
            // If already proceed to policy
            if (this.formValues.quotation.policy) return false

            // If accepted
            return (this.formValues.quotation.accepted_status === 'ACP') ? true : false
        },
        periodOfInsurance() {
            if (this.formValues.effective_date_from && this.formValues.effective_date_to) {
                return `From ${moment(this.formValues.effective_date_from).format('DD/MM/YYYY')} To ${moment(this.formValues.effective_date_to).format('DD/MM/YYYY')} (Both days inclusive)`
            }
            return ''
        },
        effectivePeriod() {
            return `${this.formValues.effective_day} days`
        },
        limitToUse() {
            return this.formValues.product?.limitation_to_use_en
        },
    },
    methods: {
        formatCurrency,
        resolveAuto() {
            if (this.id) {
                axios.get('/autos/show-detail/' + this.id).then(response => {
                    if (response) {
                        this.formValues = response.data.auto
                        this.coverage = response.data.coverage
                        this.deductibles = response.data.deductibles
                        this.loadedVehicle = true
                        this.signature = response.data.signature
                    }
                })
            }
        },
        cloneQuote() {
            this.$confirm.require({
                message: 'Do you want to generate new version?',
                header: 'Generate New Version',
                icon: 'pi pi-info-circle',
                acceptLabel: 'Generate',
                rejectLabel: 'Cancel',
                blockScroll: false,
                acceptClass: 'p-button-info px-2 py-2 border border-blue-500 text-white bg-blue-500',
                rejectClass: "px-2 py-2 border border-red-500 text-red-500",
                accept: () => {
                    axios.post(`/autos/${this.id}/clone`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success')
                            // Generate overall premium of the newly cloned quotation first before proceeding to policy
                            var formData = {
                                data_id: response.data.newAutoId,
                                product_code: this.formValues.product_code,
                                request_type: 'QUOTATION',
                            }
                            axios.post('/autos/functions/generate-overall-premium', formData).then(() => {
                                this.$router.push({ name: 'QuotationAutoIndex' });
                            })
                        }
                    }).catch(err => {
                        notify('Error', 'error')
                        console.log(err)
                    })
                },
            });
        },
        canGenerateNewVersion() {
            if (this.id) {
                axios.get(`/autos/${this.id}/can-generate-new-version`).then(response => {
                    this.hasPendingPolicy = response.data
                })
            }
        },
        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/autos/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success');
                            this.$router.push({ name: 'QuotationAutoIndex' })
                        }
                    }).catch(err => {
                        notify('Error', 'error')
                    })
                },
            });
        },

        handleCustomerAddress(customAddress, village, address, country) {
            if (address)
                if (country)
                    return `${customAddress ? customAddress + ', ' : ''}
                            ${village ? village + ', ' : ''}
                            ${address.commune ? address.commune + ', ' : ''}
                            ${address.district ? address.district + ', ' : ''}
                            ${address.province ? address.province : ''}
                            ${address.province == 'Phnom Penh' ? ' Capital, ' : ' Province, '}
                            ${country}`
                else
                    return `${customAddress ? customAddress + ', ' : ''}
                            ${village ? village + ', ' : ''}
                            ${address.commune ? address.commune + ', ' : ''}
                            ${address.district ? address.district + ', ' : ''}
                            ${address.province ? address.province : ''}
                            ${address.province == 'Phnom Penh' ? ' Capital' : ' Province'}`
            else if (country)
                return `${customAddress ? customAddress + ', ' : ''}
                        ${village ? village + ', ' : ''}
                        ${country}`
            else if (village)
                return `${customAddress ? customAddress + ', ' : ''}
                        ${village}`
            else
                return `${customAddress}`
        },

        exportVehicles() {
            location.href = '/auto-service/' + this.id + '/export-vehicles/' + this.document_no
        },
        openApproveDialog() {
            this.approveDialog = true
            this.submitted = false
        },
        openAcceptDialog() {
            this.acceptDialog = true
            this.submitted = false
        },
        approve(form) {
            this.submitted = true
            if (form.status && form.reason) {
                this.loading = true
                axios.post(`/autos/approve/${this.id}`, {
                    approved_status: form.status,
                    approved_reason: form.reason,
                }).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success')
                        this.$router.push({ name: 'QuotationAutoIndex' });
                    }
                }).catch((err) => {
                    notify(err?.response?.data?.message, 'error', 'bottom-right')
                }).finally(() => this.loading = false)
            }
        },
        accept(form) {
            this.submitted = true

            if (form.status && form.reason) {
                axios.post(`/autos/accept/${this.id}`, {
                    accepted_status: form.status,
                    accepted_reason: form.reason,
                }).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success')
                        this.$router.push({ name: 'QuotationAutoIndex' });
                    }
                }).catch(err => notify(err?.response?.data?.message, 'error'))
            }
        },
        hideDialog() {
            this.approveDialog = false
            this.acceptDialog = false
            this.submitted = false
        },
        toastMessage(msg, type, position = 'top-right') {
            notify(msg, type, position)
        },
        ucword(str) {
            return str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
                return letter.toUpperCase();
            });
        },
        proceedToPolicy() {
            this.$confirm.require({
                message: 'Do you want to proceed to policy?',
                header: 'Proceed to Policy',
                icon: 'pi pi-info-circle',
                acceptLabel: 'Proceed',
                rejectLabel: 'Cancel',
                blockScroll: false,
                acceptClass: 'p-button-info',
                rejectClass: 'p-button-danger',
                accept: () => {
                    axios.post(`/autos/proceed-to-policy/${this.id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'Success');
                            this.$router.push({ name: 'PolicyIndex' })
                        }
                    }).catch(err => {
                        notify('Error', 'Error')
                    })
                },
            });
        },
    },
    mounted() {
        this.resolveAuto();
        this.canGenerateNewVersion();
    }
}
</script>

<style scoped>
.table th {
    white-space: normal;
}

.btn-attach:hover {
    color: rgb(28, 63, 170);
}

.table td,
.table th {
    padding: 0.5rem 0.75rem;
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
