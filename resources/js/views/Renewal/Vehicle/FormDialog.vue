<template>
    <Dialog header="Vehicle" ref="dl" class="p-fluid w-9/12 custom-dialog z-10" position="top" :visible="isVisible"
        :modal="true" :closable="false" :draggable="false" @show="loadDynamicData">
        <template #header>
            <span class="p-dialog-title flex-1">Vehicle</span>
            <button @click="hideDialog" aria-label="close" type="button" tabindex="-1"
                class="p-dialog-header-icon p-dialog-header-close p-link" style="margin-right: 0">
                <span class="p-dialog-header-close-icon pi pi-times"></span>
            </button>
        </template>
        <div>
            <div class="grid grid-cols-1 justify-items-center" v-if="preLoading">
                <LoadingIndicator aria-label="Loading..." />
            </div>
            <div v-else class="grid grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-x-10 gap-y-4">
                <div class="">
                    <label for="" class="mb-1 block font-medium">Make *</label>
                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.make" :options="makes"
                        placeholder="Select make" :filter="true" optionLabel="label" showClear
                        @change="listModelsByMake($event.value)" optionValue="value" :disabled="!isEditable">
                    </Dropdown>
                    <span class="text-red text-xs text-red-700 make" v-if="errors['make']">{{ errors['make'][0]
                        }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Model *</label>
                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.model" :options="models"
                        placeholder="Select model" :filter="true" optionLabel="label" showClear optionValue="value"
                        :disabled="!isEditable">
                    </Dropdown>
                    <span class="text-red text-xs text-red-700 model" v-if="errors['model']">{{ errors['model'][0]
                        }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Value of Vehicle *</label>
                    <InputNumber v-model="formValues.vehicle_value" :min="0" :maxFractionDigits="2"
                        placeholder="Value of vehicle" :disabled="!isEditable" />
                    <span class="text-red text-xs text-red-700 vehicle_value" v-if="errors['vehicle_value']">{{
                        errors['vehicle_value'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Cubic Capacity </label>
                    <InputText v-model="formValues.cubic" placeholder="Cubic Capacity" :disabled="!isEditable" />
                    <span class="text-red text-xs text-red-700 cubic" v-if="errors['cubic']">{{
                        errors['cubic'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Plate No.</label>
                    <InputText v-model="formValues.plate_no" placeholder="Plate No."
                        :disabled="!isGeneralEndorsementEditable" />
                    <span class="text-red text-xs text-red-700 plate_no" v-if="errors['plate_no']">{{
                        errors['plate_no'][0] }}</span>
                </div>

                <div>
                    <label for="" class="mb-1 block font-medium">Chassis No.</label>
                    <InputText v-model="formValues.chassis_no" placeholder="Chassis No."
                        :disabled="!isGeneralEndorsementEditable" />
                    <span class="text-red text-xs text-red-700 chassis_no" v-if="errors['chassis_no']">{{
                        errors['chassis_no'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Engine No.</label>
                    <InputText v-model="formValues.engine_no" placeholder="Engine No."
                        :disabled="!isGeneralEndorsementEditable" />
                    <span class="text-red text-xs text-red-700 engine_no" v-if="errors['engine_no']">{{
                        errors['engine_no'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Year of Manufacture *</label>
                    <InputNumber v-model="formValues.manufacturing_year" :useGrouping="false" :min="0"
                        :maxFractionDigits="0" placeholder="Year of Manufacture"
                        :disabled="!isGeneralEndorsementEditable" />
                    <span class="text-red text-xs text-red-700 manufacturing_year"
                        v-if="errors['manufacturing_year']">{{
                            errors['manufacturing_year'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Surcharge %</label>
                    <InputNumber v-model="formValues.surcharge" :min="0" :max="100" :maxFractionDigits="2"
                        placeholder="Surcharge" :disabled="!isEditable" />
                    <span class="text-red text-xs text-red-700 surcharge" v-if="errors['surcharge']">{{
                        errors['surcharge'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">Discount %</label>
                    <InputNumber v-model="formValues.discount" :min="0" :max="100" :maxFractionDigits="2"
                        placeholder="Discount" :disabled="!isEditable" />
                    <span class="text-red text-xs text-red-700 discount" v-if="errors['discount']">{{
                        errors['discount'][0] }}</span>
                </div>
                <div>
                    <label for="" class="mb-1 block font-medium">NCD</label>
                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.ncd" :options="ncd"
                        placeholder="Select no claim discount" :filter="true" optionLabel="label" showClear
                        optionValue="value" :disabled="!isEditable">
                    </Dropdown>
                    <span class="text-red text-xs text-red-700 ncd" v-if="errors['ncd']">{{
                        errors['ncd'][0] }}</span>
                </div>
                <div v-if="hasPassengerTonnage">
                    <label for="" class="mb-1 block font-medium">Passenger / Tonnage *</label>
                    <InputNumber v-model="formValues.passenger_tonnage" :useGrouping="false" :min="0"
                        :maxFractionDigits="0" placeholder="Passenger / Tonnage" :disabled="!isEditable" />
                    <span class="text-red text-xs text-red-700 passenger_tonnage" v-if="errors['passenger_tonnage']">{{
                        errors['passenger_tonnage'][0] }}</span>
                </div>
                <div v-if="defaultVehicleUsage != null">
                    <label for="" class="mb-1 block font-medium">Vehicle Usage *</label>
                    <Dropdown class="w-full p-inputtext-sm" v-model="formValues.vehicle_usage"
                        :options="vehicleUsageOptions" placeholder="Select vehicle usage" :filter="true"
                        optionLabel="label" showClear optionValue="value" :disabled="!isEditable">
                    </Dropdown>
                    <span class="text-red text-xs text-red-700 vehicle_usage" v-if="errors['vehicle_usage']">{{
                        errors['vehicle_usage'][0] }}</span>
                </div>
                <div class="clear-fix col-span-2" v-if="defaultVehicleUsage != null"></div>
                <div v-if="isEndorsement">
                    <label for="" class="mb-1 block font-medium">Endorsement Effective Date</label>
                    <Calendar v-model="formValues.endorsement_e_date" dateFormat="dd-M-yy" :minDate="today"
                        :disabled="!isEditable" />
                    <span class="text-red text-xs text-red-700 endorsement_e_date"
                        v-if="errors['endorsement_e_date']">{{
                            errors['endorsement_e_date'][0] }}</span>
                </div>
                <div class="grid grid-cols-2">
                    <div class="col-span-2">
                        <label for="" class="mb-5 block font-medium">Cover Package</label>
                        <div v-for="coverPackage of coverPackageOptions" :key="coverPackage.value"
                            class="flex align-items-center mb-5 ml-2">
                            <RadioButton v-model="formValues.cover_pkg_id" @update:modelValue="changeCovers"
                                variant="filled" :disabled="coverPackage.disabled" :inputId="coverPackage.value"
                                name="cover_pkg_id" :value="coverPackage.value" />
                            <label :for="coverPackage.value" class="ml-1">{{ coverPackage.label }}</label>
                        </div>
                        <span class="text-red text-xs text-red-700 cover_pkg_id" v-if="errors['cover_pkg_id']">{{
                            errors['cover_pkg_id'][0] }}</span>
                    </div>
                    <div class="col-span-2" v-if="mandatoryCovers">
                        <label for="" class="mb-5 block font-medium">Optional Covers</label>
                        <div v-for="optCover of optionalCovers" :key="optCover.value"
                            :class="{ 'flex align-items-center mb-5 ml-2': true, 'hover:cursor-not-allowed': optCover.disabled }">
                            <Checkbox v-model="formValues.optional_covers" variant="outlined"
                                :disabled="optCover.disabled" :inputId="'opt_' + optCover.value" name="optional_covers"
                                :value="optCover.value" />
                            <label :for="'opt_' + optCover.value"
                                :class="{ 'ml-1': true, 'text-slate-300': optCover.disabled }">{{ optCover.label
                                }}</label>
                        </div>
                        <span class="text-red text-xs text-red-700 optional_covers" v-if="errors['optional_covers']">{{
                            errors['optional_covers'][0] }}</span>
                    </div>
                </div>
                <div>
                    <label for="" class="mb-2 block font-medium">Remark</label>
                    <Textarea v-model="formValues.remark" placeholder="Remark" :disabled="!isEditable" rows="5" />
                    <span class="text-red text-xs text-red-700 remark" v-if="errors['remark']">{{
                        errors['remark'][0] }}</span>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex w-full">
                <div class="mr-auto">
                    <p v-if="subPremium" class="text-lg font-bold">Sub Premium: {{ subPremium }}</p>
                </div>
                <Button label="Cancel" type="button"
                    severity="default" class="p-button-secondary mr-1" @click="hideDialog" />
                <Button v-if="!isEndorsement" type="button" label="Save and View Sub Premium" :loading="savingI"
                    :class="{ 'opacity-50 cursor-not-allowed': isDisabledSubmitButton, 'mr-1': true }" severity="info"
                    :disabled="isDisabledSubmitButton" @click="saveAndViewSubPremium" />
                <Button v-if="canSaveAndClose" type="button" label="Save and Close" :loading="savingII"
                    :class="{ 'opacity-50 cursor-not-allowed': isDisabledSubmitButton }" severity="info"
                    :disabled="isDisabledSubmitButton" autofocus @click="saveAndClose" />
            </div>
        </template>
    </Dialog>
</template>

<script>

import LoadingIndicator from "@/components/LoadingIndicator.vue";
import AutoDetailService from "../../../services/auto_detail.service";
import axios from "axios";

export default {
    components:{
        LoadingIndicator
    },
    props: {
        isVisible: Boolean,
        submitted: Boolean,
        productCode: String,
        id: Number,
        defaultSurcharge: [String, Number],
        defaultDiscount: [String, Number],
        defaultNCD: [String, Number],
        masterDataId: [Number, String],
        isQuotation: Boolean,
        isPolicy: Boolean,
        isEndorsement: Boolean,
        documentNo: String,
        endorsementType: String,
        quotationDocumentNo: String,
    },

    data() {
        return {
            detailId: null,
            formValues: {
                'product_code': this.productCode
            },
            makes: [],
            models: [],
            ncd: [],
            vehicleUsageOptions: [],
            coverPackageOptions: [],
            optionalCovers: [],
            mandatoryCovers: null,
            productSpecification: '',
            subPremium: '',
            defaultVehicleUsage: null,
            isDisabledSubmitButton: false,
            errors: [],
            policyId: this.$route.params.id,
            preLoading: true,
            savingI: false,
            savingII: false,
            currency: 0.00
        }
    },

    computed: {
        hasPassengerTonnage() {
            return this.productSpecification === 'TONNAGE' || this.productSpecification === 'PASSENGER'
        },
        today() {
            return new Date()
        },
        isGeneralEndorsement() {
            return this.endorsementType === 'GENERAL'
        },
        isVehicleEndorsement() {
            return this.endorsementType === 'VEHICLE'
        },
        isCancellationEndorsement() {
            return this.endorsementType === 'CANCELLATION'
        },

        isNewVehicle() {
            return this.id == null || (this.formValues.endorsement_state === 'ADDITION' && this.formValues.endorsement_stage === this.documentNo)
        },

        isEditable() {
            // if not an endorsement
            if (!this.isEndorsement) return true

            // if endorsement type is VEHICLE and is new vehicle
            return this.isVehicleEndorsement && this.isNewVehicle
        },

        isGeneralEndorsementEditable() {
            return this.isGeneralEndorsement || this.isEditable
        },

        canSaveAndClose() {
            // if not an endorsement
            if (!this.isEndorsement) return true
            if (this.isVehicleEndorsement) return this.isNewVehicle

            return this.isGeneralEndorsement || !this.isCancellationEndorsement
        }
    },

    methods: {
        hideDialog() {
            this.$emit('hideDialog')
            this.subPremium = null
            this.isDisabledSubmitButton = false
            this.errors = {}
            this.formValues = {}
        },
        listMakesByProduct() {
            axios.get('/auto-service/list-makes-by-product/' + this.productCode).then(response => {
                this.makes = response.data
            })
        },
        listModelsByMake(value) {
            axios.get('/auto-service/list-models-by-product-and-make/' + this.productCode + '/' + value).then(response => {
                this.models = response.data
            })
        },
        listNoClaimDiscounts() {
            axios.get('/no-claim-discounts-service/list-ncd/' + this.productCode).then(response => {
                this.ncd = response.data
            })
        },

        getProductSpecification() {
            axios.get(`/auto-service/get-product-specification/${this.productCode}`).then(response => {
                this.productSpecification = response.data
            })
        },
        listVehicleUsages() {
            axios.get(`/auto-service/list-vehicle-usage-by-product-code/${this.productCode}`).then(response => {
                this.vehicleUsageOptions = response.data
                this.defaultVehicleUsage = response.data[0]?.value
            })
        },
        listCoverPackages() {
            axios.get('/auto-service/list-cover-packages/' + this.productCode).then(response => {
                this.coverPackageOptions = response.data

                if (!this.isEditable) {
                    this.coverPackageOptions.map(item => item.disabled = true)
                }
            })
        },
        listProductMandatoryCovers() {
            axios.get('/auto-service/list-product-mandatory-covers/' + this.productCode).then(response => {
                this.mandatoryCovers = response.data
            })
        },
        listProductCovers() {
            axios.get('/auto-service/list-product-covers/' + this.productCode).then(response => {
                this.optionalCovers = response.data
                if (!this.isEditable) {
                    this.optionalCovers.map(item => item.disabled = true)
                }
            })
        },
        getData() {
            if (this.id) {
                if (this.isCancellationEndorsement || this.isVehicleEndorsement)
                    axios.get(`/auto-details/show-endorsement-deleted-vehicles/${this.id}`).then(response => { this.formValues = response.data; this.listModelsByMake(this.formValues.make) })
                else
                    axios.get(`/auto-details/${this.id}`).then(response => { this.formValues = response.data; this.listModelsByMake(this.formValues.make) })
            }
        },
        assignDetailId() {
            this.detailId = this.id
        },
        handleSubmit() {
            if (this.isVehicleEndorsement) {
                if (this.id)
                    return axios.put(`/auto-details/update-endorsement-vehicle/${this.masterDataId}/${this.id}`, this.formValues)
                else
                    return axios.post(`/auto-details/save-endorsement-new-vehicle/${this.masterDataId}`, this.formValues)
            } else {
                const method = this.detailId ? "PUT" : "POST"
                return AutoDetailService.save(
                    {
                        ...this.formValues,
                        master_data_id: this.masterDataId,
                        ...(method === "PUT" && { id: this.detailId })
                    },
                    method
                )
            }
        },
        scrollToError() {
            setTimeout(() => {
                var eleClass = ''
                Object.keys(this.errors).forEach((key, index) => {
                    if (index === 0) {
                        eleClass = key;
                    }
                })
                $('.p-dialog-content').animate({ scrollTop: $(`span.text-error}`).offset().top }, 500);
            }, 500)
        },
        saveAndClose() {
            this.isDisabledSubmitButton = true
            this.savingII = true
            if (this.isGeneralEndorsement) {
                AutoDetailService.updateGeneralEndorsement(
                    {
                        ...this.formValues,
                        master_data_id: this.masterDataId,
                        id: this.id
                    }
                ).then(response => {
                    if (response.data.success) {
                        notify(response.data.message, 'success')
                        this.hideDialog()
                    }
                })
                .catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                        this.scrollToError()
                    } else {
                        notify('Error', 'error')
                    }
                })
                .finally(() => { this.isDisabledSubmitButton = false; this.savingII = false })
            } else {
                this.handleSubmit().then(response => {
                    if (response.data.success) {
                        this.detailId = response.data.vehicle_id
                        notify(response.data.message, 'success')
                        this.hideDialog()
                        if (this.isEndorsement) {
                            this.$emit('startCalculatingPremium')
                            this.calculateEndorsementPremium()
                        } else {
                            this.checkValidation().then((responseValidation) => {
                                if (responseValidation.data[0].is_checked == 'Y') {
                                    this.handleDeductible()
                                    this.$emit('startCalculatingPremium')
                                    this.handleSubPremium()
                                } else {
                                    notify(responseValidation.data[0].message, 'error')
                                }
                            })
                        }
                    }
                })
                .catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors
                        this.scrollToError()
                    } else {
                        notify('Error', 'error')
                    }
                })
                .finally(() => {
                    this.isDisabledSubmitButton = false
                    this.savingII = false
                })
            }
        },
        saveAndViewSubPremium() {
            this.isDisabledSubmitButton = true
            this.savingI = true
            this.handleSubmit().then(response => {
                this.detailId = response.data.vehicle_id
                this.errors = []
                if (response.data.success) {
                    // Clear error
                    this.errors = {}
                    notify(response.data.message, 'success')
                    this.$emit('startCalculatingPremium')
                    this.handleSubPremium(true)
                    this.handleDeductible()
                }
            })
            .catch(err => {
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors
                    this.scrollToError()
                } else {
                    notify('Something went wrong', 'warn')
                }
            })
            .finally(() => {
                this.isDisabledSubmitButton = false
                this.savingI = false
            })
        },

        handleDeductible() {
            const formData = {
                data_id: this.masterDataId,
                product_code: this.productCode,
                request_type: this.isQuotation ? 'QUOTATION' : 'POLICY',
            }

            axios.post('/autos/functions/generate-overall-deductible', formData)
                .then(response => {
                    if (response.data[0].code === 'SUC') {
                        this.$emit('toastDeductibleMessage')
                    }
                })
                .catch(error => console.log(error))
        },

        handleSubPremium(isPreviewed = false) {
            const formData = {
                detail_id: this.detailId,
                data_id: this.masterDataId,
                product_code: this.productCode,
                request_type: this.isQuotation ? 'QUOTATION' : 'POLICY',
            }
            axios.post('/autos/functions/generate-single-premium', formData)
                .then(response => {
                    if (response.data[0].stat === 'SUC') {
                        if (isPreviewed)
                            this.subPremium = response.data[0].vehicle_premium
                        this.$emit('updateTotalPremium', response.data[0].total_premium)
                        // Only after the total premium is generated, then it is possible to call a function to generate quotation number
                        if (!this.quotationDocumentNo && this.isQuotation)
                            this.$emit('handleQuotationNumGeneration')
                    }
                })
                .then(() => {
                    if (this.isPolicy) {
                        this.$emit('generateCommissionData')
                        this.$emit('generateReinsurance')
                    }
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.$emit('vehicleListUpdated')
                    this.$emit('finishCalculatingPremium')
                })
        },

        checkValidation() {
            const formData = {
                id: null,
                data_id: this.masterDataId,
                detail_id: this.detailId,
                product_line: 'AUTO',
                product_code: this.productCode,
                request_type: this.isQuotation ? 'QUOTATION' : 'POLICY',
                group_type: this.quotationDocumentNo ? 'check_regen_premium' : 'gen_quote',
                p_type: ''
            }

            return axios.get('/autos/functions/check-validation', { params: formData })
        },

        changeCovers(value) {
            axios.get(`/auto-service/list-remain-covers/${value}/${this.productCode}`).then(response => {
                this.optionalCovers = response.data

                if (!this.isEditable) {
                    this.optionalCovers.map(item => item.disabled = true)
                }
            }).finally(() => {})
        },

        calculateEndorsementPremium() {
            axios.get(`/api/endorsements/get-premium/${this.policyId}/${this.documentNo}`).then(response => {
                this.$emit('updateTotalPremium', response.data)
                this.$emit('generateCommissionData')
                this.$emit('generateReinsurance')
                this.$emit('vehicleListUpdated')
                this.$emit('finishCalculatingPremium')
            })
        },

        disableOtherCoverPackages(coverPackageArr) {
            if (coverPackageArr.length === 1) {
                this.coverPackageOptions.filter(item => item.value != coverPackageArr[0])
                    .map(item => item.disabled = true)
            } else if (coverPackageArr.length === 0) {
                this.coverPackageOptions.map(item => item.disabled = false)
            }
        },

        loadVehicleEnum(keys) {
            return axios.post('/auto-service/vehicle-enums', {
                product_code: this.productCode,
                keys: keys
            })
        },
        loadCoverages() {
            this.loadVehicleEnum(['optional_coverages', 'cover_packages']).then((response) => {
                let data = response.data
                this.optionalCovers = data.optional_coverages
                this.coverPackageOptions = data.cover_packages

                if (!this.isEditable) {
                    this.optionalCovers.map(item => item.disabled = true)
                }
                if (!this.isEditable) {
                    this.coverPackageOptions.map(item => item.disabled = true)
                }
            }).finally(() => { this.preLoading = false })
        },
        loadVehicleAttr() {
            this.loadVehicleEnum(['makes', 'ncd', 'product_specs', 'vehicle_usages', 'mandatory_covers']).then((response) => {
                let data = response.data
                this.makes = data.makes
                this.ncd = data.ncd
                this.productSpecification = data.product_specs
                this.vehicleUsageOptions = data.vehicle_usages
                this.defaultVehicleUsage = this.vehicleUsageOptions[0]?.value
                this.mandatoryCovers = data.mandatory_covers
            }).then(() => {
                this.assignDefault()
            })
        },
        assignDefault() {
            this.formValues.vehicle_usage = this.defaultVehicleUsage
            this.formValues.discount = this.defaultDiscount
            this.formValues.ncd = this.defaultNCD
            this.formValues.surcharge = this.defaultSurcharge
            this.formValues.optional_covers = this.mandatoryCovers
        },
        loadDynamicData() {
            this.loadCoverages()
            this.getData()
            this.assignDetailId()
        }
    },

    mounted() {
        this.loadVehicleAttr()
    }
}
</script>
