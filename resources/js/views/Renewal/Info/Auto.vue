<template>
    <div>
        <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-5">
            <div>
                <label class="form-label">Product Type *</label>
                <Dropdown class="w-full p-inputtext-sm" v-model="formValues.product_code" :options="productOptions"
                    optionLabel="label" optionValue="value" placeholder="Product" :disabled="hasSubmitted">
                    <template #option="slotProps">
                        <p class="text-sm font-semibold">
                            {{ slotProps.option.label }}
                        </p>
                        <span class="text-xs">{{
                            slotProps.option.desc
                        }}</span>
                    </template>
                </Dropdown>
            </div>
            <div class="clear-fix"></div>
            <div>
                <label for="" class="form-label">Calc. Option *</label>
                <Dropdown placeholder="Select an option" :options="calculateOptions" v-model="formValues.calc_option"
                    optionLabel="label" optionValue="value" class="w-full" :disabled="isEndorsement" />
            </div>
            <div v-if="formValues.calc_option == 'SPECIAL'">
                <label for="" class="form-label">Negotiation Rate (%) *</label>
                <InputNumber :min="0" :max="100" v-model="formValues.negotiation_rate" placeholder="Negotiation Rate"
                    label="" :maxFractionDigits="2" class="w-full" :disabled="isEndorsement" />
            </div>
            <div>
                <label for="" class="form-label">Customer Type *</label>
                <Dropdown v-model="formValues.customer_type" placeholder="Select an option" class="w-full"
                    optionValue="value" optionLabel="label" :options="customerTypes"
                    @change="renderListCustomers($event.value)" disabled />
            </div>
            <div>
                <label for="" class="form-label">Customer Name *</label>
                <Dropdown v-model="formValues.customer_no" placeholder="Select an option" class="w-full"
                    optionValue="value" optionLabel="label" :options="customers"
                    @change="changeCustomerName($event.value)" disabled />
            </div>
            <div>
                <label for="" class="form-label">Joint Status *</label>
                <Dropdown v-model="formValues.joint_status" placeholder="Select an option" optionValue="value"
                    optionLabel="label" class="w-full" :options="joinStatusOptions" :disabled="!canSaveGeneral" />
            </div>
            <div class="col-span-2" v-if="formValues.joint_status == 'J'">
                <JointDetailsFields :customerTypeOpts="customerTypes" :canSaveGeneral="canSaveGeneral"
                    v-model="formValues.joint_details" :jointLevelOpts="jointDetailsConfig[0] ?? []"
                    :permissionOpts="jointDetailsConfig[1] ?? []" />
            </div>
            <div>
                <label for="" class="form-label">Insured Name *</label>
                <Textarea type="textarea" placeholder="Insured name" class="w-full" rows="5"
                    v-model="formValues.insured_name" :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Insured Name (Khmer)</label>
                <Textarea v-model="formValues.insured_name_kh" placeholder="Insured name in Khmer" rows="5"
                    class="w-full" :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Period Type *</label>
                <Dropdown class="w-full" v-model="formValues.insurance_period_type" optionLabel="label"
                    optionValue="value" placeholder="Select an option" :options="periodTypes"
                    :disabled="isEndorsement" />
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="" class="form-label">Inception Date *</label>
                    <Calendar v-model="formValues.effective_date_from" :maxDate="formValues.effective_date_to"
                        :disabled="isEndorsement" @input="setValueForExpiryDate" dateFormat="dd-M-yy" class="w-full"
                        @update:modelValue="updateIsInsurancePeriodChange(true)" />
                </div>
                <div>
                    <label for="" class="form-label">Expiry Date *</label>
                    <Calendar v-model="formValues.effective_date_to" :minDate="formValues.effective_date_from"
                        :disabled="isEndorsement" class="w-full" dateFormat="dd-M-yy"
                        @update:modelValue="updateIsInsurancePeriodChange(true)" />
                </div>
            </div>

            <div>
                <label for="" class="form-label">Endorsement Clause *</label>
                <MultiSelect v-model="formValues.endorsement_clause" placeholder="Select options" display="chip"
                    optionLabel="label" optionValue="value" class="w-full" :options="endorsementClauses"
                    :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">General Exclusion *</label>
                <MultiSelect v-model="formValues.general_exclusive" placeholder="Select options" display="chip"
                    optionLabel="label" optionValue="value" class="w-full" :options="generalExclusions"
                    :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Business Channel *</label>
                <Dropdown v-model="formValues.sale_channel" placeholder="Select an option" class="w-full"
                    optionLabel="label" optionValue="value" :options="saleChannelOptions"
                    @change="changeBusinessCategory($event.value)" :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Business Channel *</label>
                <Dropdown v-model="formValues.business_code" placeholder="Select an option" class="w-full"
                    optionLabel="label" optionValue="value" :options="businessChannelOptions"
                    @change="changeBusinessChannel($event.value)" filter :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Policy Wording Version *</label>
                <Dropdown v-model="formValues.policy_wording_version" placeholder="Select an option" class="w-full"
                    optionLabel="label" optionValue="value" :options="policyWordingVersionOptions"
                    :disabled="!canSaveGeneral" />
            </div>
            <div class="clear-fix"></div>
            <div>
                <label for="" class="form-label">Warranty</label>
                <CKEditor v-model="formValues.warranty" placeholder="Warranty" :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Memorandum</label>
                <CKEditor v-model="formValues.memorandum" placeholder="Memorandum" :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Subjectivity</label>
                <CKEditor v-model="formValues.subjectivity" placeholder="Subjectivity" :disabled="!canSaveGeneral" />
            </div>
            <div>
                <label for="" class="form-label">Remark</label>
                <CKEditor v-model="formValues.remark" placeholder="Remark" :disabled="!canSaveGeneral" />
            </div>
            <div v-if="generalEndorsement || vehicleEndorsement">
                <label for="" class="form-label">Endorsement Description</label>
                <Textarea v-model="formValues.endorsement_description" placeholder="Endorsement description" rows="4" />
            </div>
        </div>
        <div class="text-right mt-5">
            <router-link :to="{ name: cancelRoute }" class="btn btn-outline-secondary w-24 mr-1"
                tag="button">Cancel</router-link>
            <button type="button" @click="handleSubmit" :disabled="isSubmitting" class="btn btn-primary w-24">Save</button>
        </div>
    </div>
</template>
<script>
import moment from "moment";
import JointDetailsFields from "./JointDetailsFields.vue";
import axios from "axios";
import CKEditor from "../../../components/Form/CKEditor.vue";
export default {
    components: {
        JointDetailsFields,
        CKEditor
    },
    props: {
        id: Number,
        isEndorsement: Boolean,
        endorsementType: String,
        cancelRoute: String,
        isEndorsementForm: Boolean,
        documentNo: {
            type: String,
            default: ""
        }
    },

    data() {
        return {
            after: false,
            formValues: {
                product_code: "0001",
                // Trigger updates commission_rate and handler_code
                commission_rate: null,
                endorsement_clause: []
            },
            productOptions: [],
            // default selected product
            defaultProduct: "0001",
            calculateOptions: [],
            periodTypes: [],
            customerTypes: [],
            customers: [],
            endorsementClauses: [],
            generalExclusions: [],
            businessChannelOptions: [],
            saleChannelOptions: [],
            businessHandlerOptions: [],
            joinStatusOptions: [{
                value: 'S', label: "Single"
            },
            { value: 'J', label: "Joint" }],
            jointDetailsConfig: [],
            jointNamesStr: "",
            jointNamesStrKhmer: "",
            isResolvedInsuredName: false,
            warranty: "",
            memorandum: "",
            subjectivity: "",
            remark: "",
            defaultDayGap: 364,
            policyWordingVersionOptions: [],
            // To handle an issue when users change reinsurance period
            isInsurancePeriodChange: false,

            isSubmitting: false,
        };
    },
    computed: {
        hasSubmitted() {
            // has id means already submitted
            return this.id !== null;
        },
        generalEndorsement() {
            return this.endorsementType === "GENERAL";
        },
        vehicleEndorsement() {
            return this.endorsementType === "VEHICLE";
        },
        canSaveGeneral() {
            // if not an endorsement
            if (!this.isEndorsement) return true;
            // if endorsement type is General
            return this.generalEndorsement;
        }
    },
    methods: {
        assignFieldValues() {
            this.formValues.warranty = this.warranty;
            this.formValues.memorandum = this.memorandum;
            this.formValues.subjectivity = this.subjectivity;
            this.formValues.remark = this.remark;
        },

        handleSubmit() {
            this.isSubmitting = true
            if (this.isEndorsementForm) {
                axios
                    .put(
                        `/autos/save-endorsement-general/${this.id}`,
                        this.formValues
                    )
                    .then(response => {
                        this.isSubmitting = false
                        if (response.data.success) {
                            this.toastMessageSave(
                                response.data.message,
                                "Success"
                            );
                        }
                    })
                    .then(() => {
                        this.resolveAuto();
                    })
                    .then(() => {
                        // Go to the vehicles information tab
                        document
                            .querySelector('.nav-tabs a[data-target="#vehicle-info"]')
                            .click();
                    })
                    .catch(() => this.toastMessageSave("Error", "Error"));
            } else {
                if (this.id) {
                    axios
                        .put("/autos/" + this.id, this.formValues)
                        .then(() => {
                            this.isSubmitting = false
                            if (this.isInsurancePeriodChange)
                                this.$emit(
                                    "updateRequireTotalPremiumState",
                                    true
                                );
                            // Go to the vehicles information tab
                            document
                                .querySelector(
                                    '.nav-tabs a[data-target="#vehicle-info"]'
                                )
                                .click();
                        })
                        .then(() => {
                            this.updateIsInsurancePeriodChange(false);
                        })
                        .then(async () => {
                            // Generate comission data after save
                            await this.generateCommissionData();

                            this.$emit("updateBusinessChannel");
                        });
                } else {
                    axios
                        .post("/autos", this.formValues)
                        .then(response => {
                            this.isSubmitting = false
                            this.id = response.data.id;
                        })
                        .finally(() => {
                            // Go to the vehicles information tab
                            document
                                .querySelector(
                                    '.nav-tabs a[data-target="#vehicle-info"]'
                                )
                                .click();
                        });
                }
            }
        },

        generateCommissionData() {
            axios.get(`/autos/${this.id}/get-policy-id`).then(res => {
                const policyId = res.data;
                if (policyId) {
                    axios
                        .get(
                            `/policy-service/generate-commission-data/${policyId}`
                        )
                        .catch(err => console.log(err));
                }
            });
        },

        getServices() {
            axios
                .get("/auto-service/get-services")
                .then(response => {
                    this.productOptions = response.data.productOptions.map(
                        item => {
                            item.label = `${item.label} (${item.desc})`;
                            return item;
                        }
                    );
                    this.calculateOptions = response.data.calculateOptions;
                    this.periodTypes = response.data.periodTypes;
                    this.customerTypes = response.data.customerTypes;
                    this.endorsementClauses = response.data.endorsementClauses;
                    this.generalExclusions = response.data.generalExclusions;

                    this.jointDetailsConfig = response.data.jointDetailsConfig;

                    // Assign default clauses
                    this.formValues.endorsement_clause =
                        response.data.defaultEndorsementClauses;
                    this.formValues.general_exclusive =
                        response.data.defaultGeneralExclusions;
                })
                .finally(() => {
                    this.resolveAuto();
                    this.listSaleChannels();
                    this.listBusinessHandlers();
                });
        },
        renderListCustomers(e) {
            this.formValues.customer_no = null;
            axios
                .get("/auto-service/list-customers-by-type/" + e)
                .then(response => {
                    this.customers = response.data;
                });
        },

        listPolicyWordingVersions() {
            axios
                .get(
                    `/auto-service/list-policy-wording-version-by-product-code/${this.formValues.product_code}`
                )
                .then(response => {
                    this.policyWordingVersionOptions = response.data;
                });
        },

        listSaleChannels() {
            axios
                .get("/business-channels-service/list-sale-channels")
                .then(response => {
                    this.saleChannelOptions = response.data;
                });
        },
        listBusinessHandlers() {
            axios
                .get("/business-channels-service/list-business-handlers")
                .then(response => {
                    this.businessHandlerOptions = response.data;
                });
        },

        changeBusinessChannel(e) {
            axios
                .get("/auto-service/find-business-channel/" + e.target.value)
                .then(response => {
                    this.formValues.commission_rate =
                        response.data.commission_rate;
                    this.formValues.handler_code = response.data.handler_code;
                });
        },

        changeBusinessCategory(e) {
            axios
                .get("/auto-service/list-business-channels-by-category/" + e)
                .then(response => {
                    this.businessChannelOptions = response.data;
                });
        },

        changeCustomerName(e) {
            if (!this.isResolvedInsuredName) {
                var customersNo = [e ?? null];

                if (this.formValues.joint_details) {
                    var jointDetailNames = this.formValues.joint_details.map(
                        item => {
                            return item.customer_no ? item.customer_no : null;
                        }
                    );
                    customersNo = customersNo.concat(jointDetailNames);
                }
                this.getJointNamesStr(customersNo);
                this.getJointNamesStrKhmer(customersNo);
            } else {
                // Assign resolved insured_name when editing
                this.jointNamesStr = this.formValues.insured_name;
                this.jointNamesStrKhmer = this.formValues.insured_name_kh;
                this.isResolvedInsuredName = false;
            }
        },

        getJointNamesStr(customersNo) {
            axios
                .post("/auto-service/get-insured-names/en", {
                    customersNo: customersNo
                })
                .then(response => {
                    this.jointNamesStr = response.data;
                });
        },
        getJointNamesStrKhmer(customersNo) {
            axios
                .post("/auto-service/get-insured-names/kh", {
                    customersNo: customersNo
                })
                .then(response => {
                    this.jointNamesStrKhmer = response.data;
                });
        },

        resolveAuto() {
            if (this.id) {
                axios
                    .get("/autos/" + this.id)
                    .then(response => {
                        if (response) {
                            this.formValues = response.data;
                            this.formValues.effective_date_from = moment(response.data.effective_date_from).toDate()
                            this.formValues.effective_date_to = moment(response.data.effective_date_to).toDate()
                            this.formValues.warranty = response.data?.warranty ?? ''
                            this.formValues.subjectivity = response.data?.subjectivity ?? ''
                            this.formValues.memorandum = response.data?.memorandum ?? ''
                            this.formValues.remark = response.data?.remark ?? ''
                            this.isResolvedInsuredName = true;
                        }
                    })
                    .finally(() => {
                        this.listPolicyWordingVersions();
                        this.changeBusinessCategory(this.formValues.sale_channel)
                    });
            }
        },
        setValueForExpiryDate() {
            let effect_date_from = new Date(
                this.formValues.effective_date_from
            );
            // check if the input of year is 4 digit
            if (effect_date_from.getFullYear().toString().length == 4) {
                let isLeapYear = effect_date_from.getFullYear() % 4 === 0;
                if (isLeapYear) this.defaultDayGap = 365;
                else this.defaultDayGap = 364;
                effect_date_from.setDate(
                    effect_date_from.getDate() + this.defaultDayGap
                );
                this.formValues.effective_date_to = effect_date_from
                    .toISOString()
                    .split("T")[0];
            }
        },

        updateIsInsurancePeriodChange(isChanged) {
            this.isInsurancePeriodChange = isChanged;
        },

        toastMessage(msg, type) {
            this.$toast.add({
                severity: type,
                detail: msg,
                life: 4000
            });
        },

        toastMessageSave(msg, type, position = "bottom") {
            this.$notify(
                {
                    group: position,
                    title: type,
                    text: msg
                },
                4000
            );
        }
    },

    mounted() {
        this.getServices();
    }
};
</script>
<style scoped>
.collap {
    display: none;
}

.rotate {
    transform: rotate(180deg);
}

.iconColor {
    color: red;
}

.toast-container {
    left: 50% !important;
    top: 50% !important;
    min-width: 20vw !important;
    transform: translate(-50%, -50%) !important;
    position: -webkit-sticky !important;
    position: sticky !important;
    z-index: 1001 !important;
}

.p-disabled {
    background: #e9ecef;
    opacity: 1;
}
</style>