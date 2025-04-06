<template>
    <div>
        <Toast position="center" />
        <div class="intro-y flex flex-col sm:flex-row items-center">
            <div>
                <span v-if="isEndorsement && !isGeneralEndorsement" class="text-lg font-bold">Total Premium:
                    <ProgressSpinner v-if="isCalculatingPremium" class="mt-2" style="width:30px;height:30px" strokeWidth="8" animationDuration=".5s"/>
                    <span v-else>{{ newPremium || newPremium == 0 ? formatCurrency(newPremium) : formatCurrency(totalPremium) }}</span>
                </span>
                <template v-else-if="isGeneralEndorsement"></template>
                <span v-else class="text-lg font-bold">Total Premium:
                    <ProgressSpinner v-if="isCalculatingPremium" class="mt-2" style="width:30px;height:30px" strokeWidth="8" animationDuration=".5s"/>
                    <span v-else>{{ newPremium || newPremium == 0 ? formatCurrency(newPremium) : formatCurrency(totalPremium) }}</span>
                </span>
            </div>
            <div class="w-full sm:w-auto flex ml-auto">
                <button v-if="canDeleteVehicle" class="btn btn-danger shadow-md" @click="handleDelete">Delete</button>
                <button v-if="canAddVehicle" class="btn btn-primary shadow-md ml-2" @click="openFormDialog">Add vehicle</button>
            </div>
        </div>
        <div class="mt-5">
            <div class="overflow-x-auto scrollbar-hidden -mt-5">
                <DataTable @ref="tabulator = $event" :options="options"/>
            </div>
        </div>
        <portal to="destination">
            <FormDialog
                v-if="canOpenDialog"
                :id="id"
                :isVisible="isShownDialog"
                :submitted="submitted"
                :productCode="productCode"
                :masterDataId="masterDataId"
                :defaultSurcharge="defaultSurcharge"
                :defaultDiscount="defaultDiscount"
                :defaultNCD="defaultNCD"
                :isQuotation="isQuotation"
                :isPolicy="isPolicy"
                :isEndorsement="isEndorsement"
                :endorsementType="endorsementType"
                :documentNo="documentNo"
                :quotationDocumentNo="quotationDocumentNo"
                @generateCommissionData="generateCommissionData"
                @generateReinsurance="generateReinsuranceShare"
                @hideDialog="hideDialog"
                @updateTotalPremium="updateTotalPremium"
                @toastDeductibleMessage="toastDeductibleMessage"
                @vehicleListUpdated="$emit('vehicleListUpdated')"
                @startCalculatingPremium="startCalculatingPremium"
                @finishCalculatingPremium="finishCalculatingPremium"
                @handleQuotationNumGeneration="handleQuotationNumGeneration"
            />
            <DeleteVehicleDialog
                v-if="defaultEndorsementEffectiveDate"
                :selectedRowsIds="selectedRowsIds"
                :isVisible="isShownDeleteVehicleDialog"
                :submitted="submitted"
                :defaultEndorsementEffectiveDate="defaultEndorsementEffectiveDate"
                :documentNo="documentNo"
                @hideDialog="hideDeleteVehicleEndorsementDialog"
                @updateTotalPremium="updateTotalPremium"
                @generateDeductibles="handleDeductible"
                @generateCommissionData="generateCommissionData"
                @generateReinsurance="generateReinsuranceShare"
                @vehicleListUpdated="$emit('vehicleListUpdated')"
                @startCalculatingPremium="startCalculatingPremium"
                @finishCalculatingPremium="finishCalculatingPremium"
            />
        </portal>
    </div>
</template>

<script>
    import FormDialog from './FormDialog.vue'
    import DeleteVehicleDialog from './DeleteVehicleDialog.vue'
    import {formatCurrency} from '@/helpers'

	export default {
        components: {
            FormDialog,
            DeleteVehicleDialog,
        },

        props:{
            masterDataId: [Number, String],
            totalPremium: [Number, String],
            isQuotation: {
                type: Boolean,
                default: true
            },
            isPolicy: {
                type: Boolean,
                default: false
            },
            isEndorsement: {
                type: Boolean,
                default: false
            },
            documentNo: {
                type: String,
                default: '',
            },
            endorsementType: String,
            requireUpdateTotalPremium: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                id: null,
                productCode: '',
                defaultEndorsementEffectiveDate: null,
                data: [],
                policyId: this.$route.params.id ?? null,
                selectedRowsIds: [],
                tabulator: null,
                options: {
                    persistenceID: 'vehicles-table',
                    ajaxURL: '/auto-details',
                    ajaxParams: {
                        master_data_id: this.masterDataId,
                        isEndorsement: this.isEndorsement ? this.isEndorsement : null
                    },
                    columns:[
                        ...(!this.isEndorsement && [{
                            formatter:"rowSelection",
                            titleFormatter:"rowSelection",
                            headerHozAlign: "center",
                            hozAlign:"center",
                            headerSort: false,
                            width: 20,
                        }] || []),
                        {
                            title:"No.",
                            field:"no",
                            headerSort: false,
                            width: 55,
                            hozAlign: "center",
                            headerHozAlign: "center",
                        },
                        {
                            title:"Make",
                            field:"make",
                            headerSort: false,
                            mutator: (_, row) => {
                                return row.make_model?.make.make
                            },
                        },
                        {
                            title:"Model",
                            field:"model",
                            headerSort: false,
                            mutator: (_, row) => {
                                return row.make_model?.model
                            },
                        },
                        {
                            title:"Sum Insured",
                            field:"vehicle_value",
                        },
                        {
                            title:"Plate No.",
                            field:"plate_no",
                        },
                        {
                            title:"Year of Manufacture",
                            field:"manufacturing_year",
                        },
                        {
                            title:"Vehicle Usage",
                            field:"vehicle_usage",
                            headerSort: false,
                        },
                        {
                            title:"Covers",
                            field:"selected_cover_pkg",
                            tooltip: true,
                        },
                        {
                            title:"Status",
                            field:'status',
                            headerSort: false,
                            width: 100,
                            visible: this.isEndorsement,
                            hozAlign: "center",
                            headerHozAlign: "center",
                            formatter: "html",
                            mutator: (_, row) => {
                                if (row.status === 'DEL') {
                                    if (row.endorsement_state === 'CANCEL')
                                        return `
                                            <span class="text-xs px-1 rounded-full bg-theme-6 text-white mr-1 px-2 py-1">Cancelled</span>
                                        `

                                    if (row.endorsement_stage === null)
                                        return `<span class="text-xs px-1 rounded-full bg-theme-6 text-white mr-1 px-2 py-1">
                                            Deleted
                                        </span>`

                                    return `
                                        <span class="text-xs px-1 rounded-full bg-theme-6 text-white mr-1 px-2 py-1">
                                            Deleted
                                        </span>
                                    `
                                }
                                else if (row.endorsement_state === 'ADDITION') {
                                    if (row.endorsement_stage === this.documentNo)
                                        return `
                                            <span class="text-xs px-1 rounded-full bg-theme-9 text-white mr-1 px-2 py-1">
                                                New
                                            </span>
                                        `
                                    return `
                                        <span class="text-xs px-1 rounded-full bg-theme-1 text-white mr-1 px-2 py-1">
                                            Added
                                        </span>
                                    `
                                }
                                return ''
                            },
                        },
                        {
                            title:"Endorsement Stage",
                            field:"endorsement_stage",
                            headerSort: false,
                            width: 180,
                            visible: this.isEndorsement,
                            hozAlign: "center",
                            headerHozAlign: "center",
                        },
                    ],
                    rowDblClick: this.handleRowClick,
                    rowSelectionChanged: data => this.selectedRowsIds = data.map(item => item.id),

                    // Can select multiple rows except in endorsement
                    rowClick: (_, row) => {
                        if (this.isVehicleEndorsement) {
                            const activeRows = row._row.parent.activeRows
                            row.toggleSelect()

                            // Deselect other rows
                            activeRows.filter(item => item.data.id !== row._row.data.id)
                                .forEach(item => {
                                    item.component.deselect()
                                })
                        } else if (!this.isEndorsement) {
                            row.toggleSelect()
                        }
                    },
                },
                isShownDialog: false,
                submitted: false,
                isShownUploadDialog: false,
                isShownDeleteVehicleDialog: false,
                defaultSurcharge: null,
                defaultDiscount: null,
                defaultNCD: null,
                newPremium: null,
                isCalculatingPremium: false,
                quotationDocumentNo: null,
            }
        },

        computed: {
            canOpenDialog(){
                return this.productCode;
            },
            canOpenUploadDialog(){
                return this.productCode && this.isQuotation;
            },
            isGeneralEndorsement() {
                return this.endorsementType === 'GENERAL'
            },
            isVehicleEndorsement() {
                return this.endorsementType === 'VEHICLE'
            },
            canAddVehicle() {
                if (!this.isEndorsement) return true

                return this.isVehicleEndorsement
            },
            canDeleteVehicle() {
                // Quote, and Policy: When has selected row(s)
                if (!this.isEndorsement) return this.selectedRowsIds.length > 0

                // Endorsement: Only in add / remove vehicle endorsement with 1 selected row
                if (this.isVehicleEndorsement) return this.selectedRowsIds.length === 1

                return false
            }
        },

        watch: {
            requireUpdateTotalPremium() {
                if(this.requireUpdateTotalPremium){
                    this.startCalculatingPremium()
                    this.handlePremium()
                    this.handleDeductible()
                }
            },
        },

        methods: {
            formatCurrency,
            openFormDialog() {
                this.isShownDialog = true
                this.submitted = false
            },

            hideDialog() {
                this.isShownDialog = false
                this.submitted = false

                this.id = null
                this.tabulator.replaceData()
            },

            openFormUploadDialog() {
                this.isShownUploadDialog = true
                this.submitted = false
            },

            hideUploadDialog() {
                this.isShownUploadDialog = false
                this.submitted = false

                this.id = null
                this.tabulator.replaceData()
            },

            openDeleteVehicleEndorsementDialog() {
                this.isShownDeleteVehicleDialog = true
                this.submitted = false
            },

            hideDeleteVehicleEndorsementDialog() {
                this.isShownDeleteVehicleDialog = false
                this.submitted = false

                this.tabulator.replaceData()
            },

            getProductCode() {
                if (this.masterDataId){
                    axios.get(`/auto-service/get-product-code/${this.masterDataId}`).then(response => this.productCode = response.data).finally(()=>{
                        this.getDefaultSurcharge()
                        this.getDefaultDiscount()
                        this.getDefaultNCD()
                    })
                }
            },

            getDefaultEndorsementEffectiveDate() {
                if (this.masterDataId){
                    axios.get(`/auto-service/get-default-endorsement-e-date/${this.masterDataId}`).then(response => this.defaultEndorsementEffectiveDate = response.data)
                }
            },

            getDefaultSurcharge(){
                axios.get(`/auto-service/get-default-surcharge/${this.productCode}`).then(response => {
                    this.defaultSurcharge = response.data
                })
            },

            getDefaultDiscount(){
                axios.get(`/auto-service/get-default-discount/${this.productCode}`).then(response => {
                    this.defaultDiscount = response.data
                })
            },

            getDefaultNCD(){
                axios.get(`/auto-service/get-default-ncd/${this.productCode}`).then(response => {
                    this.defaultNCD = response.data
                })
            },

            handleDeleteVehicleUpload(){
                axios.delete('/auto-service/delete-auto-temp-from-detail',
                    {
                        data: { 'master_data_id': this.masterDataId }
                    }
                ).catch((error)=>{
                    console.log(error);
                })
            },

            updateTotalPremium(premium){
                this.newPremium = premium
            },

            handleRowClick(_, row) {
                this.id = row._row.data.id
                this.openFormDialog()
            },

            handleDelete() {
                if (this.isVehicleEndorsement)
                    this.openDeleteVehicleEndorsementDialog()
                else
                    this.openDeleteVehicleQuotationOrPolicyDialog()
            },

            openDeleteVehicleQuotationOrPolicyDialog(){
                this.$confirm.require({
                    message: 'Do you want to delete these records?',
                    header: 'Delete',
                    icon: 'pi pi-info-circle',
                    acceptClass: "p-button-danger",
                    blockScroll: false,
                    accept: () => {
                        var formData = {
                            policy_id: this.isPolicy ? this.policyId : null,
                            data_id: this.masterDataId,
                            detail_id_list: this.selectedRowsIds,
                            request_type: 'RENEWED_POLICY',
                        }
                        this.startCalculatingPremium()

                        axios.post('/autos/functions/delete-vehicles-manually', formData).then(async response => {
                            if (response.data.success) {
                                await this.handlePremium()
                                this.tabulator.replaceData()
                            } else {
                                this.toastMessage(response.data[0].message ?? 'Error', 'Error')
                            }
                        })
                        .then(() => {
                            this.handleDeductible()
                        })
                        .catch((e) => {
                            console.error(e)
                            this.toastMessage('Error', 'Error')
                        })
                        .finally(()=>{
                            this.$emit('vehicleListUpdated')
                            this.finishCalculatingPremium()
                        })
                    },
                })
            },

            handleQuotationNumGeneration() {
                var formData = {
                    data_id: this.masterDataId,
                    product_code: this.productCode,
                    request_type: 'RENEWED_POLICY',
                    product_line: 'AUTO',
                    is_checked: 'Y'
                }
                axios.post('/autos/functions/generate-quotation-no', formData).then((response) => {
                    if(response.data.success){
                        this.quotationDocumentNo = response.data.quotation_no
                        this.toastMessage(response.data.message, 'Success')
                    }
                    else
                        this.toastMessage(response.data.message, 'Error')
                }).catch(err => console.log(err))
            },

            getQuotationDocumentNo() {
                if (this.masterDataId)
                    axios.get(`/auto-service/quotation/get-document-no/${this.masterDataId}`).then(response => this.quotationDocumentNo = response.data)
            },

            handleDeductible() {
                var formData = {
                    data_id: this.masterDataId,
                    product_code: this.productCode,
                    request_type: 'POLICY',
                }

                axios.post('/autos/functions/generate-overall-deductible', formData)
                    .then(response => {
                        if (response.data[0].code === 'SUC') {
                            this.toastDeductibleMessage()
                        }
                    })
                    .catch(error => console.log(error))
            },

            handlePremium() {
                var formData = {
                    data_id: this.masterDataId,
                    product_code: this.productCode,
                    request_type: 'RENEWED_POLICY',
                }
                axios.post('/autos/functions/generate-overall-premium', formData)
                .then(response => {
                    if (response.data[0].stat === 'SUC'){
                        this.newPremium = response.data[0].total_premium
                    }
                }).finally(()=>{
                    if(this.isPolicy){
                        this.generateCommissionData()
                        this.generateReinsuranceShare()
                    }
                    this.$emit('vehicleListUpdated')
                    this.$emit('updateIsInsurancePeriodChange', false)
                    this.$emit('updateRequireTotalPremiumState', false)
                    this.finishCalculatingPremium()
                })
            },

            checkValidation(){
                var formData = {
                    id: null,
                    data_id: this.masterDataId,
                    detail_id: null,
                    product_line: 'AUTO',
                    product_code: this.productCode,
                    request_type: 'RENEWED_POLICY',
                    group_type: 'check_regen_premium',
                    p_type: ''
                }

                return axios.get('/autos/functions/check-validation', {params : formData})
            },

            generateCommissionData() {
                if(this.isPolicy || this.isEndorsement)
                    axios.get(`/policy-service/generate-commission-data/${this.policyId}`)
                        .catch(err => console.log(err))
            },

            generateReinsuranceShare() {
                if(this.isPolicy)
                    axios.get(`/policy-service/generate-reinsurance-share/${this.policyId}`)
                        .then(response => {
                            if (response.data.success)
                                this.generateReinsuranceData()
                        }
                    ).catch(err => console.log(err))
                else if (this.isEndorsement)
                    axios.get(`/endorsement-service/generate-reinsurance-share/${this.policyId}`)
                        .then(response => {
                            if (response.data.success)
                                this.generateReinsuranceData()
                        }
                    ).catch(err => console.log(err))
            },

            generateReinsuranceData() {
                if(this.isPolicy)
                    axios.get(`/policy-service/generate-reinsurance-data/${this.policyId}`).catch(err => console.log(err))
                else if(this.isEndorsement)
                    axios.get(`/endorsement-service/generate-reinsurance-data/${this.policyId}`).catch(err => console.log(err))
            },

            startCalculatingPremium(){
                this.isCalculatingPremium = true
            },

            finishCalculatingPremium(){
                this.isCalculatingPremium = false
            },

            toastDeductibleMessage() {
                this.$toast.add({
                    severity: 'success',
                    detail: 'Deductibles has been generated to default',
                    life: 4000
                });
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
            this.getProductCode()
            this.getDefaultEndorsementEffectiveDate()
            if(this.isQuotation)
                this.getQuotationDocumentNo()
        }
    }
</script>

<style scoped>
    .tabulator-row.tabulator-selected {
        background: #EFF6FF;
    }
</style>
