<template>
    <div>
          <form @submit.prevent="handleSubmit">
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
                      <CustomInputNumber
                          v-model="premium_tax_fee_rate"
                          placeholder="Tax & Fee (%)"
                          :required="true"
                          validationName="Tax & Fee (%)"
                      />
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
                    <span class="w-1/4 font-bold">Commission Rate (%)</span>
                    <p v-if='!canEditCommissionData' class="w-3/4">{{ commissionData.commission_rate }}</p>
                    <span v-else class="w-1/12">
                        <CustomInputNumber
                            v-model="commission_rate"
                            placeholder="Commission Rate (%)"
                            :required="true"
                            validationName="Commission Rate (%)"
                        />
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
                         <CustomInputNumber
                             v-model="witholding_tax_rate"
                             placeholder="WHT (%)"
                             :required="true"
                             validationName="WHT (%)"
                         />
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
                    <router-link :to="{name: 'TravelPolicyIndex'}" class="btn btn-danger w-24 mr-2" tag="button">Cancel</router-link>
                    <button type="submit" class="btn btn-primary w-24">Save</button>
                </div>
          </form>
    </div>
</template>

<script>

import CustomInputNumber from "@/views/Policy/FormTabs/HS/CustomInputNumber.vue";

export default {
  components: {CustomInputNumber},
    props: {
        id: [Number, String],
        canSave: {
            type: Boolean,
            default: false
        },
        businessName: String,
        commission: Object,
        dataId: [Number, String],
    },

    data() {
        return {
            ERROR_MESSAGE:"Something went wrong!",
            SUCCESS_MESSAGE:"Success!",
            commissionData: this.commission,
            premium_tax_fee_rate: this.commission.premium_tax_fee_rate,
            witholding_tax_rate: this.commission.witholding_tax_rate,
            commission_rate: this.commission.commission_rate,
        }
    },

    computed: {
        canEditCommissionData(){
            return this.canSave;
        },
    },

    methods: {
        handleSubmit() {
            axios.post(`/travel/policies/policy-services/update-commission-data/${this.id}`, {
                premium_tax_fee_rate: this.premium_tax_fee_rate,
                commission_rate: this.commission_rate,
                witholding_tax_rate: this.witholding_tax_rate,
                detail_id: this.commissionData.detail_id
            })
            .then(response => {
                if (response.data.success) {
                    // reset the form values by getting the new input from users
                    notify(response.data.message || this.SUCCESS_MESSAGE, "success", "bottom-right")

                    // Update issue_date
                    this.handleIssueDate()

                    // Go to the reinsurance tab
                    document.querySelector('.nav-tabs a[href="#reinsurance"]').click()
                }
            }).catch((e) => {
                console.log(e)
                 notify(e.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
            })
        },

        handleIssueDate(){
            axios.put('/travel/policies/update-issue-date/' + this.dataId).catch((e) => {
                console.log(e)
            })
        },
    },
}
</script>

<style scoped>
    .input-label {
        display: block;
        line-height: 1.5;
        font-weight: bold;
    }
    .icon-color {
        color: red;
    }
</style>
