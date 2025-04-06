<template>
    <div>
        <div class="flex mr-1 mb-3">
            <span>
                <svg class="w-6 h-6 text-green-500 rotate icon-color" fill="currentColor"
                    :class="`commission-icon-${index}`" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </span>
            <span class="text-base cursor-pointer w-full" @click="toggle(index)">Vehicle #{{ index + 1 }}</span>
        </div>
        <div class="collapse" :class="`show-commission-${index}`">
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Business Channel</span>
                <p class="w-3/4">{{ commissionData.business_category }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Business Name</span>
                <p class="w-3/4">{{ businessName }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Gross Written Premium</span>
                <p class="w-3/4">{{ commissionData.gross_written_premium   }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Tax & Fee (%)</span>
                <p v-if='!canEditCommissionData' class="w-3/4">{{ commissionData.premium_tax_fee_rate }}</p>
                <span v-else class="w-1/12">
                    <InputNumber v-model="premium_tax_fee_rate" :min="0" :max="100" placeholder="Tax & Fee (%)"
                        class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2" />
                        <span v-if="errors['premium_tax_fee_rate']" class="text-xs text-red-500 text-nowrap">{{errors['premium_tax_fee_rate'][0]}}</span>
                </span>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Tax & Fee Amount</span>
                <p class="w-3/4">{{ commissionData.premium_tax_fee   }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Net Written Premium</span>
                <p class="w-3/4">{{ commissionData.net_written_premium   }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <div class="w-1/4 font-bold">Commission Rate (%)</div>
                <p v-if='!canEditCommissionData' class="w-3/4">{{ commissionData.commission_rate }}</p>
                <span v-else class="w-1/12">
                    <InputNumber v-model="commission_rate" :min="0" :max="100" placeholder="Commission Rate (%)"
                        class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2" />
                        <span v-if="errors['commission_rate']" class="text-xs text-red-500 text-nowrap">{{errors['commission_rate'][0]}}</span>
                </span>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Commission Amount</span>
                <p class="w-3/4">{{ commissionData.commission_amount   }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">WHT (%)</span>
                <p v-if='!canEditCommissionData' class="w-3/4">{{ commissionData.witholding_tax_rate }}</p>
                <span v-else class="w-1/12">
                    <InputNumber v-model="witholding_tax_rate" :min="0" :max="100" placeholder="WHT (%)" class="w-full"
                        :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2" />
                        <span v-if="errors['witholding_tax_rate']" class="text-xs text-red-500 text-nowrap">{{errors['witholding_tax_rate'][0]}}</span>
                </span>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">WHT Amount</span>
                <p class="w-3/4">{{ commissionData.witholding_tax   }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="w-1/4 font-bold">Commission Due Amount</span>
                <p class="w-3/4">{{ commissionData.commission_due_amount   }}</p>
            </div>
            <div v-if="canEditCommissionData" class="text-right">
                <router-link :to="{ name: 'PolicyIndex' }" class="btn btn-outline-secondary w-24 mr-1"
                    tag="button">Cancel</router-link>
                <button type="button" @click="handleSubmit" class="btn btn-primary w-24"><i v-if="saving"
                        class="pi pi-spinner pi-spin mr-1"></i> Save</button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        id: [Number, String],
        index: Number,
        canSave: {
            type: Boolean,
            default: false
        },
        businessName: String,
        commission: Object,
        dataId: [Number, String],
        length: {
            type: Number,
            default: 0
        }
    },

    data() {
        return {
            commissionData: this.commission,
            premium_tax_fee_rate: this.commission.premium_tax_fee_rate,
            witholding_tax_rate: this.commission.witholding_tax_rate,
            commission_rate: this.commission.commission_rate,
            saving: false,
            errors:[]
        }
    },

    computed: {
        canEditCommissionData() {
            return this.canSave;
        },
    },

    methods: {
        handleSubmit() {
            this.saving = true
            axios.post(`/policy-service/update-commission-data/${this.id}`, {
                premium_tax_fee_rate: this.premium_tax_fee_rate,
                commission_rate: this.commission_rate,
                witholding_tax_rate: this.witholding_tax_rate,
                detail_id: this.commissionData.detail_id
            })
                .then(response => {
                    if (response.data.success) {
                        this.getCommissionDataByVehicle();


                        notify(response.data.message, 'success')

                        // Update issue_date
                        this.handleIssueDate()

                        // Go to the reinsurance tab
                        if (this.length === (this.index + 1)) {
                            // reset the form values by getting the new input from users
                            this.$emit('triggerCommissionInputChange')
                            document.querySelector('.nav-tabs a[href="#reinsurance"]').click()
                        }
                        else this.toggle(this.index + 1)
                    }
                }).catch((e) => {
                    if(e.status === 422){
                        this.errors = e.response.data.errors
                    }else notify('Something went wrong', 'error')
                    
                }).finally(() => this.saving = false)
        },

        getCommissionDataByVehicle() {
            if (this.id)
                axios.get(`/policy-service/get-commission-data-by-vehicle/${this.commissionData.detail_id}`)
                    .then((response) => {
                        this.commissionData = response.data
                        this.premium_tax_fee_rate = this.commissionData.premium_tax_fee_rate
                        this.commission_rate = this.commissionData.commission_rate
                        this.witholding_tax_rate = this.commissionData.witholding_tax_rate
                    })
        },

        handleIssueDate() {
            axios.put('/autos/update-issue-date/' + this.dataId).catch((e) => {
                console.log(e)
            })
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        },

        toggle(index) {
            this.$emit('toggle', index)
        },
    },

    mounted() {
        if (this.index == 0)
            this.$emit('collapse', this.index)
    },
}
</script>

<style scoped>
.input-label {
    display: block;
    line-height: 1.5;
    font-weight: bold;
}

.collapse {
    display: none;
}

.rotate {
    transform: rotate(180deg);
}

.icon-color {
    color: red;
}
</style>
