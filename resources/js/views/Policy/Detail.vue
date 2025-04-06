<template>
    <div>
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="isAutoProduct">Auto</span>
                Policy Detail
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <button v-if="canApproveCond" class="btn btn-primary shadow-md mr-2" title="Approve Policy"
                    @click="openApproveDialog">
                    <span class="h-6 leading-6">Policy Approval</span>
                </button>

                <button class="btn btn-primary shadow-md mr-2" title="Generate Endorsement" v-if="canGenerateEndorsement"
                    @click="openEndorsementDialog">
                    <span class="h-6 leading-6">Generate Endorsement</span>
                </button>
                <div class="dropdown">
                    <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                    </button>
                    <div class="dropdown-menu w-72">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a v-if="canPrintInvoice"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printInvoiceUrlWithSignature" target="_blank">Invoice (Signature)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printInvoiceUrlWithoutSignature" target="_blank">Invoice (No Signature)</a>
                            <a v-if="canPrintCertificate"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printCertificateUrl" target="_blank">Certificate</a>

                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLink" target="_blank">Policy Schedule (Letterhead EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLink" target="_blank">Policy Schedule (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLink" target="_blank">Policy Signature with No
                                letterhead (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLinkKh" target="_blank">Policy Schedule (Letterhead KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLinkKh" target="_blank">Policy Schedule (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLinkKh" target="_blank">Policy Signature with No
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
                <router-link v-if="canUpdateCond" :to="{ name: 'PolicyEdit', params: { id: id } }">
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
            <AutoDetail :id="autoId" :policyId="id" :policyStatus="formValues.status" :documentNo="documentNo" />
        </div>

        <ApproveDialog :isVisible="approveDialog" header="Policy Approval" :submitted="submitted"
            :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]" value="APV"
            @hideDialog="hideDialog" @confirm="approve" :saving="savingApproval" />

        <EndorsementDialog :isVisible="showEndorsementDialog" header="Generate Endorsement" :submitted="submitted"
            @hideDialog="hideDialog" @confirm="generateEndorsement" :saving="savingEndorsement"
            :errors="endorsementErr" />
    </div>
</template>

<script>

import AutoDetail from './FormTabs/Info/Auto/Detail.vue'
import UserPermissions from '../../mixins/UserPermissions'
import ApproveDialog from './ApproveDialog.vue'
import EndorsementDialog from './EndorsementDialog.vue'
import { hasPermission } from "@/services/auth.service";

export default {
    mixins: [UserPermissions],

    components: {
        AutoDetail,
        ApproveDialog,
        EndorsementDialog,
    },

    data() {
        return {
            id: this.$route.params.id ?? null,
            functionCode: 'POLICY',
            formValues: {},
            isReinsuranceCompleted: false,
            isConfigCompleted: false,
            approveDialog: false,
            submitted: false,
            showEndorsementDialog: false,
            canGenerateEndorsement: false,
            savingApproval: false,
            endorsementErr: [],
            savingEndorsement: false
        }
    },

    computed: {
        isAutoProduct() {
            return this.formValues?.product_line_code?.toString().toUpperCase() === 'AUTO'
        },
        autoId() {
            return this.formValues.data_id
        },
        documentNo() {
            return this.formValues.document_no
        },
        printInvoiceUrlWithSignature() {
            return `/policy-service/${this.id}/download-invoice/with-signature`
        },
        printInvoiceUrlWithoutSignature() {
            return `/policy-service/${this.id}/download-invoice`
        },
        printCertificateUrl() {
            return `/policy-service/${this.id}/download-auto-certificate`
        },
        printWithLetterHeadLink() {
            return `/policy-service/${this.id}/download-policy-schedule/en?letterhead=1`
        },
        printWithoutLetterHeadLink() {
            return `/policy-service/${this.id}/download-policy-schedule/en?letterhead=0`
        },
        printWithoutLetterHeadAndStampLink() {
            return `/policy-service/${this.id}/download-policy-schedule/en?letterhead=0&noStamp=1`
        },
        printWithLetterHeadLinkKh() {
            return `/policy-service/${this.id}/download-policy-schedule/km?letterhead=1`
        },
        printWithoutLetterHeadLinkKh() {
            return `/policy-service/${this.id}/download-policy-schedule/km?letterhead=0`
        },
        printWithoutLetterHeadAndStampLinkKh() {
            return `/policy-service/${this.id}/download-policy-schedule/km?letterhead=0&noStamp=1`
        },
        canPrintInvoice() {
            // If policy is approved
            return this.formValues.status === 'APV'
        },
        canPrintCertificate() {
            // If policy is approved
            return this.formValues.status === 'APV'
        },
        canApproveCond() {
            // If don't have permission to approve
            if (!hasPermission(this.functionCode, 'APPROVE')) return false

            // If policy data is not yet completed
            if (!this.isReinsuranceCompleted || !this.isConfigCompleted) return false

            // If policy is not yet approved and approved_status as submit_status is Submitted
            return this.formValues.status === 'PND' && this.formValues.approved_status === 'SBM' ? true : false;
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
    },

    methods: {
        getPolicy() {
            if (this.id) {
                axios.get(`/api/policies/${this.id}`).then(response => {
                    this.formValues = response.data;
                })
            }
        },

        isPolicyReinsuranceCompleted() {
            if (this.id)
                axios.get(`/policy-service/is-policy-reinsurance-completed/${this.id}`).then(response => {
                    this.isReinsuranceCompleted = response.data;
                })
        },

        isPolicyConfigurationCompleted() {
            if (this.id)
                axios.get(`/policy-service/is-policy-configuration-completed/${this.id}`).then(response => {
                    this.isConfigCompleted = response.data;
                })
        },

        handleDelete(id) {
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                rejectClass: "p-button-secondary",
                rejectLabel: "Cancel",
                acceptLabel: "Delete",
                blockScroll: false,
                accept: () => {
                    axios.delete(`/api/policies/${id}`).then(response => {
                        if (response.data.success) {
                            notify(response.data.message, 'success');
                            this.$router.push({ name: 'PolicyIndex' })
                        }
                    }).catch(err => {
                        notify('Something went wrong', 'error')
                    })
                },
            });
        },

        openApproveDialog() {
            this.approveDialog = true
            this.submitted = false
        },

        hideDialog() {
            this.approveDialog = false
            this.showEndorsementDialog = false
            this.submitted = false
        },

        approve(form) {
            this.submitted = true
            if (form.status && form.reason) {
                this.savingApproval = true
                axios.post(`/api/policies/approve/${this.id}`, {
                    approved_status: form.status,
                    approved_reason: form.reason,
                }).then(response => {
                    if (response.data.success) {
                        if (form.status == 'APV')
                            this.generateInvoice()
                        notify(response.data.message, 'Success')
                        this.$router.push({ name: 'PolicyIndex' })
                    }
                }).catch(err => notify(err?.response?.data?.message, 'error')).finally(() => this.savingApproval = false)
            }
        },

        generateEndorsement(form) {
            this.submitted = true
            this.endorsementErr = []
            if (form.auto_endorsement_type && form.endorsement_e_date) {
                this.savingEndorsement = true
                axios.post(`/api/policies/generate-auto-endorsement/${this.id}`, form).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'Success')
                        this.$router.push({ name: 'EndorsementIndex' })
                    }
                }).catch((err) => {
                    if (err.status === 422) this.endorsementErr = err.reponse.data.errors
                    notify('Something went wrong', 'error')
                }).finally(() => this.savingEndorsement = false)
            }
        },

        openEndorsementDialog() {
            this.showEndorsementDialog = true
            this.submitted = false
        },

        checkCanGenerateEndorsement() {
            axios.get(`/api/policies/check-can-generate-auto-endorsement/${this.id}`).then(response => {
                console.log("can generate: ",response.data)
                this.canGenerateEndorsement = response.data
            })
        },

        generateInvoice() {
            axios.post('/api/policies/generate-auto-invoice', {
                documentNo: this.formValues.document_no,
                requestType: 'INVOICE'
            }).catch(err => {
                console.log(err);
            })
        },
    },

    mounted() {
        this.getPolicy()
        this.isPolicyReinsuranceCompleted()
        this.isPolicyConfigurationCompleted()
        this.checkCanGenerateEndorsement()
    },
}
</script>
