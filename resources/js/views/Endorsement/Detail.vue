<template>
    <div>
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
            <h2 class="text-lg font-medium mr-auto">
                Endorsement Detail
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <button v-if="canApproveCond" class="btn btn-primary shadow-md mr-2" title="Approve Policy"
                    @click="openApproveDialog">
                    <span class="h-6 leading-6">Endorsement Approval</span>
                </button>
                <button v-if="canGenerateEndorsement" class="btn btn-primary shadow-md mr-2"
                    title="Generate Endorsement" @click="openEndorsementDialog">
                    <span class="h-6 leading-6">Generate Endorsement</span>
                </button>
                <div v-if="canExportVehicles || canExportVehicleListForAll" class="dropdown">
                    <button class="dropdown-toggle btn btn-success shadow-md mr-2" title="Export Excel">
                        <DocumentTextIcon />
                    </button>
                    <div class="dropdown-menu w-56">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a v-if="canExportVehicles"
                                class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                @click="exportVehicles" target="_blank">Endorsements</a>
                            <a v-if="canExportVehicleListForAll"
                                class="flex items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                @click="exportAllVehicles" target="_blank">
                                Update Lists for All
                            </a>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                    </button>
                    <div class="dropdown-menu w-56">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a v-if="canPrintInvoice && isApproved"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printInvoiceUrlWithSignature" target="_blank">Invoice (Signature)</a>
                            <a v-if="canPrintInvoice"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printInvoiceUrlWithoutSignature" target="_blank">Invoice (No Signature)</a>
                            <a v-if="canPrintCreditNote && isApproved"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printCreditNoteUrlWithSignature" target="_blank">Credit Note (Signature)</a>
                            <a v-if="canPrintCreditNote"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printCreditNoteUrlWithoutSignature" target="_blank">Credit Note (No
                                Signature)</a>
                            <a v-if="canPrintCertificate"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printCertificateUrl" target="_blank">Certificate</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printLinkLetterHead" target="_blank">Endorsement (Letterhead)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printLinkNoLetterHead" target="_blank">Endorsement (No Letterhead)</a>
                        </div>
                    </div>
                </div>
                <button v-if="canDeleteCond" class="btn btn-danger mr-2 intro-x" title="Delete"
                    @click="handleDelete(id)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
                <router-link v-if="canUpdateCond" :to="{ name: 'EndorsementEdit', params: { id: id } }">
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
        <div v-if="isAutoProduct">
            <AutoDetail :id="id" :endorsementId="id" :documentNo="documentNo" :endorsementStatus="formValues.status"
                @get-total-premium='getTotalPremium' @get-vehicles-length='getVehiclesLength' />
        </div>

        <ApproveDialog :isVisible="approveDialog" header="Endorsement Approval" :submitted="submitted"
            :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]" value="APV"
            @hideDialog="hideDialog" @confirm="approve" :saving="savingApproval" />

        <EndorsementDialog :isVisible="showEndorsementDialog" header="Generate Endorsement" :submitted="submitted" :saving="savingEndorsement"
            @hideDialog="hideDialog" @confirm="generateEndorsement" />
    </div>
</template>

<script>

import AutoDetail from './FormTabs/Info/Auto/Detail.vue'
import UserPermissions from '../../mixins/UserPermissions'
import ApproveDialog from '../Policy/ApproveDialog.vue'
import EndorsementDialog from '../Policy/EndorsementDialog.vue'
import DocumentTextIcon from '@/components/Icons/DocumentTextIcon.vue'

export default {
    mixins: [UserPermissions],

    components: {
        AutoDetail,
        ApproveDialog,
        EndorsementDialog,
        DocumentTextIcon,
    },

    data() {
        return {
            id: this.$route.params.id ?? null,
            functionCode: 'ENDORSEMENT',
            formValues: {},
            hasNewVehicle: false,
            isReinsuranceCompleted: false,
            isConfigCompleted: false,
            approveDialog: false,
            submitted: false,
            totalPremium: null,
            canGenerateEndorsement: false,
            canExportVehicleListForAll: false,
            showEndorsementDialog: false,
            vehicle_length: 0,
            savingApproval:false,
            savingEndorsement:false
        }
    },

    computed: {
        isAutoProduct() {
            return this.formValues.product_line_code === 'AUTO'
        },
        autoId() {
            return this.formValues.data_id
        },
        documentNo() {
            return this.formValues.document_no
        },
        canPrintCreditNote() {
            // If total premium is less than 0
            return this.totalPremium < 0
        },
        canPrintInvoice() {
            // If total premium is greater than 0
            return this.totalPremium > 0
        },
        isApproved() {
            return this.formValues.status === 'APV'
        },
        canPrintCertificate() {
            // If policy is approved
            if (this.formValues.status !== 'APV') return false

            return this.hasNewVehicle
        },
        printLinkNoLetterHead() {
            return `/endorsement-service/${this.id}/download-endorsement?letterhead=0`
        },
        printLinkLetterHead() {
            return `/endorsement-service/${this.id}/download-endorsement?letterhead=1`
        },
        printCreditNoteUrlWithSignature() {
            return `/endorsement-service/${this.id}/download-credit-note/with-signature`
        },
        printCreditNoteUrlWithoutSignature() {
            return `/endorsement-service/${this.id}/download-credit-note`
        },
        printInvoiceUrlWithSignature() {
            return `/endorsement-service/${this.id}/download-invoice/with-signature`
        },
        printInvoiceUrlWithoutSignature() {
            return `/endorsement-service/${this.id}/download-invoice`
        },
        printCertificateUrl() {
            return `/endorsement-service/${this.id}/download-auto-certificate`
        },
        canUpdateCond() {
            // If already approved
            if (this.formValues.status !== 'PND') return false

            return this.canUpdate
        },
        canDeleteCond() {
            // If already approved
            if (this.formValues.status === 'APV') return false
            return this.canDelete
        },
        canApproveCond() {
            // If don't have permission to approve
            if (!this.canApprove) return false

            // If endorsement data is not yet completed
            if (!this.isReinsuranceCompleted || !this.isConfigCompleted) return false

            // If endorsement is not yet approved and approved_status as submit_status is Submitted
            return this.formValues.status === 'PND' && this.formValues.approved_status === 'SBM' ? true : false;
        },
        canExportVehicles() {
            return this.vehicle_length > 0 || this.formValues.auto?.endorsement_type == 'GENERAL'
        },
    },

    methods: {
        getEndorsement() {
            if (this.id) {
                axios.get(`/api/endorsements/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        },

        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle text-red-500',
                rejectClass: "p-button-secondary",
                acceptClass: "p-button-danger p-button-outlined",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/api/endorsements/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success');
                            this.$router.push({ name: 'EndorsementIndex' })
                        }
                    }).catch(err => {
                        notify(err.response?.data?.message, 'error')
                    })
                },
            });
        },

        // Check if endorsement has new vehicle to generate certificate
        checkHasNewVehicle() {
            axios.get(`/endorsement-service/${this.id}/check-has-new-vehicle`).then(response => this.hasNewVehicle = response.data)
        },

        isPolicyReinsuranceCompleted() {
            if (this.id)
                axios.get(`/endorsement-service/is-endorsement-reinsurance-completed/${this.id}`).then(response => {
                    this.isReinsuranceCompleted = response.data;
                })
        },

        isPolicyConfigurationCompleted() {
            if (this.id)
                axios.get(`/policy-service/is-policy-configuration-completed/${this.id}`).then(response => {
                    this.isConfigCompleted = response.data;
                })
        },

        openApproveDialog() {
            this.approveDialog = true
            this.submitted = false
        },

        openEndorsementDialog() {
            this.showEndorsementDialog = true
            this.submitted = false
        },

        hideDialog() {
            this.approveDialog = false
            this.showEndorsementDialog = false
            this.submitted = false
        },

        approve(form) {
            this.submitted = true
            console.log(form)
            if (form.status && form.reason) {
                this.savingApproval = true
                axios.post(`/api/endorsements/approve/${this.id}`, {
                    approved_status: form.status,
                    approved_reason: form.reason,
                }).then(response => {
                    if (response.data.success) {
                        if (form.status == 'APV') {
                            if (this.totalPremium > 0)
                                this.generateInvoice()
                            else if (this.totalPremium < 0)
                                this.generateCreditNote()
                        }
                        notify(response.data.message, 'success')
                        this.$router.push({ name: 'EndorsementIndex' })
                    }
                }).catch(err => {
                    notify(err?.response?.data.message, 'error')
                })
                .finally(() => this.savingApproval = false)
            }
        },

        getTotalPremium(total_premium) {
            this.totalPremium = total_premium
        },

        getVehiclesLength(vehicle_length) {
            this.vehicle_length = vehicle_length
        },

        checkCanGenerateEndorsement() {
            axios.get(`/api/policies/check-can-generate-auto-endorsement/${this.id}`).then(response => {
                this.canGenerateEndorsement = response.data
            })
        },

        checkCanExportVehicleListForAll() {
            axios.get(`/endorsement-service/check-can-export-vehicle-list-for-all/${this.id}`).then(response => {
                this.canExportVehicleListForAll = response.data
            })
        },

        exportVehicles() {
            location.href = '/endorsement-service/' + this.formValues.data_id + '/export-vehicles/' + this.documentNo + '/' + this.formValues.auto?.endorsement_type
        },

        exportAllVehicles() {
            location.href = `/endorsement-service/${this.id}/export-vehicle-lists-for-all`
        },

        generateEndorsement(form) {
            this.submitted = true

            if (form.auto_endorsement_type && form.endorsement_e_date) {
                this.savingEndorsement= true
                axios.post(`/api/endorsements/generate-auto-endorsement/${this.id}`, form).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success')
                        this.$router.push({ name: 'EndorsementIndex' })
                    }
                }).catch(() => notify('Error', 'error')).finally(() => this.savingEndorsement = true)
            }
        },

        generateInvoice() {
            axios.post('/api/policies/generate-auto-invoice', {
                documentNo: this.formValues.document_no,
                requestType: 'INVOICE'
            }).catch(err => {
                console.log(err);
            })
        },

        generateCreditNote() {
            axios.post('/api/endorsements/generate-auto-credit-note', {
                documentNo: this.formValues.document_no,
                requestType: 'CREDIT_NOTE'
            }).catch(err => {
                console.log(err);
            })
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },
    },

    mounted() {
        this.getEndorsement()
        this.checkHasNewVehicle()
        this.isPolicyReinsuranceCompleted()
        this.isPolicyConfigurationCompleted()
        this.checkCanGenerateEndorsement()
        this.checkCanExportVehicleListForAll()
    }
}
</script>
