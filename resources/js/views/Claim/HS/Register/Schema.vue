<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span>Schema</span>
        Registration
      </h2>
    </div>
    <div class="intro-y box grid gap-y-2 mt-5 p-5">
      <form @submit.prevent="handleSubmit">
        <div class="grid lg:grid-cols-4 gap-x-5 gap-y-2">
          <div>
            <label class="font-bold mb-1 block">Claim No. *</label>
            <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.claim_no"
                placeholder="Claim No."
                disabled
            />
            <span v-if="errors.claim_no" class="text-error">{{ errors.claim_no[0] }}</span>
          </div>
          <div>
            <label class="font-bold mb-1 block">Claimant's name *</label>
            <InputText
                class="w-full p-inputtext-sm"
                v-model="formValues.claimant_name"
                placeholder="Claimant's name"
                :disabled="'true'"
            />
            <span v-if="errors.claimant_name" class="text-error">{{ errors.claimant_name[0] }}</span>
          </div>
          <div>
            <label for="from_date" class="block mb-1 font-bold">Date of disability *</label>
            <Calendar  placeholder="" v-model="formValues.date_of_disability" dateFormat="dd-M-yy"/>
          </div>
          <div>
            <label for="from_date" class="block mb-1 font-bold">Date of complete document *</label>
            <Calendar  placeholder="" v-model="formValues.date_of_completed_doc" dateFormat="dd-M-yy"/>
            <span v-if="errors.date_of_completed_doc" class="text-error">{{
                errors.date_of_completed_doc[0]
              }}</span>
          </div>
        </div>
        <div class="pt-6">
          <h1 class="text-xl font-bold mb-2.5">Benefits of : {{ formValues.schema_plan_name }}</h1>
          <SchemaBenefit v-model="formValues.schema_data" :errors="errors" @changeExpense="changeExpense"/>
        </div>
        <div class="grid grid-cols-5 gap-x-2 py-2.5 pl-6">
          <div class="col-span-3">
            <h3 class="text-right uppercase font-bold pt-2">Total Actual Incurred Expense (USD)</h3>
          </div>
          <div class="col-span-2">
            <InputNumber
                name="total_actual_incurred_expense"
                disabled="true"
                v-model="formValues.total_actual_incurred_expense"
                placeholder="Amount"
                class="w-full"
            />
            <span v-if="errors.date_of_completed_doc" class="text-error">
              {{errors.total_actual_incurred_expense[0]}}
            </span>
          </div>
        </div>
        <hr>
        <div class="grid grid-cols-5 gap-x-2 py-2.5 pl-6">
          <div class="col-span-3">
            <h3 class="text-right uppercase font-bold pt-2">Total Maximum Payable (USD)</h3>
          </div>
          <div class="col-span-2">
            <InputNumber
                name="total_maximum_payable"
                disabled="true"
                v-model="formValues.total_maximum_payable"
                class="w-full"
            />
            <span v-if="errors.total_maximum_payable" class="text-error">
              {{errors.total_maximum_payable[0]}}
            </span>
          </div>
        </div>
        <hr>
        <div class="grid grid-cols-5 gap-x-2 py-2.5 pl-6">
          <div class="col-span-3">
            <h3 class="text-right uppercase font-bold pt-2">Total Non-Payable Expense (USD)</h3>
          </div>
          <div class="col-span-2">
            <InputNumber
                name="total_non_payable_expense"
                disabled="true"
                v-model="formValues.total_non_payable_expense"
                class="w-full"
            />
            <span v-if="errors.total_non_payable_expense" class="text-error">
              {{errors.total_non_payable_expense[0]}}
            </span>
          </div>
        </div>
        <hr>
        <div class="grid grid-cols-5 gap-x-2 py-2.5 pl-6">
          <div class="col-span-3">
            <h3 class="text-right uppercase font-bold pt-2">PREVIOUS PAYMENT (USD)</h3>
          </div>
          <div class="col-span-2">
            <InputNumber
                name="previous_payment"
                disabled="true"
                v-model="formValues.previous_payment"
                class="w-full"
            />
            <span v-if="errors.previous_payment" class="text-error">
              {{errors.previous_payment[0]}}
            </span>
          </div>
        </div>
        <hr>
        <div class="grid grid-cols-5 gap-x-2 py-2.5 pl-6">
          <div class="col-span-3">
            <h3 class="text-right uppercase font-bold pt-2">TOTAL AMOUNT DUE (USD)</h3>
          </div>
          <div class="col-span-2">
            <InputNumber
                name="total_amount_due"
                disabled="true"
                v-model="formValues.total_amount_due"
                class="w-full"
            />
            <span v-if="errors.total_amount_due" class="text-error">
              {{errors.total_amount_due[0]}}
            </span>
          </div>
        </div>
        <hr>
        <div class="grid grid-cols-5 gap-x-2 mt-4">
          <div class="col-span-2 pl-6">
            <h3 class="mb-2 font-bold">Claim Histories : {{ claimHistories.length ? '' : 'N/A' }}</h3>
            <ol v-if="claimHistories.length" class="list-decimal pl-4">
              <li v-for="(claim, index) in claimHistories" :key="'claim' + index">
                {{ claim.claim_status }} USD {{ claim.total_claim }} {{ claim.approved_at }} <span
                  v-if="claim.remaining">(Remaining
                                    {{ claim.remaining }})</span>
              </li>
            </ol>
          </div>
          <div>
            <h3 class="text-right uppercase font-bold">DUE TO</h3>
          </div>
          <div class="col-span-2">
            <div>
              <label for="" class="form-label"></label>
              <Textarea v-model="formValues.due_to" rows="5" class="w-full" />
              <span v-if="errors.due_to" class="text-error">
                {{errors.due_to[0]}}
              </span>
            </div>
          </div>
        </div>
        <div class="text-right mt-5">
          <router-link :to="{ name: 'ClaimHSRegisterIndex' }" class="btn btn-outline-secondary w-24 mr-1">
            Cancel
          </router-link>
          <button type="submit" :disabled="isLoading" class="btn btn-primary w-24">
            {{ isLoading ? "Saving ..." : "Save" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ClaimRegisterService from "@/services/claim/hs/claim_register.service";
import SchemaBenefit from "./Components/SchemaBenefit.vue";

export default {
  components: {
    SchemaBenefit
  },
  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      id: this.$route.params.id ?? null,
      formValues: {
        claim_no: '',
        insured_name: '',
        date_of_disability: '',
        date_of_completed_doc: '',
        schema_data: []
      },
      claimHistories: [],
      errors: {},
      isLoading: false,
      showDialog: false,
      showClinicDialog: false,
      submitted: false,
      totalActualExpense: null,
      totalPayable: null
    };
  },
  methods: {
    changeExpense() {
      this.formValues.total_actual_incurred_expense = this.formValues.schema_data?.reduce(function (a, b) {
        return Number(a) + Number(b.actual_incurred_expense ?? 0);
      }, 0);
      this.formValues.total_maximum_payable = this.formValues.schema_data?.reduce(function (a, b) {
        return Number(a) + Number(b.maximum_payable ?? 0);
      }, 0);
      this.formValues.total_non_payable_expense = this.formValues.total_actual_incurred_expense - this.formValues.total_maximum_payable;
    },
    getData() {
      ClaimRegisterService.getSchema(this.id).then((res) => {
        // console.log('res:', res)
        this.formValues = res.data.claim
        this.formValues.schema_data = res.data.schema_data
        this.claimHistories = res.data.claim_histories

        // console.log('✅ Parent API response:', res.data);
        // Debug what we're passing to child
       /* console.log('📤 Parent sending to child:', {
          schema_data: this.formValues.schema_data,
          type: typeof this.formValues.schema_data,
          isArray: Array.isArray(this.formValues.schema_data)
        });*/

      }).catch((err) => {
        console.log(err)
      })
      .finally(() => {
        // console.log(' this.formValues:',  this.formValues.claim_no)

      });
    },
    handleSubmit() {
      this.isLoading = true;
      ClaimRegisterService.saveSchema(this.id, JSON.parse(JSON.stringify(this.formValues)))
          .then((res) => {
            notify(res.data?.message || this.SUCCESS_MESSAGE, "success","bottom-right");

            this.$router.push({name: "ClaimHSRegisterIndex"});
          })
          .catch((err) => {
            if (err?.response?.status === 422) {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error","bottom-right");
              console.log(err.response)
              this.errors = err.response.data.errors;
            } else if (err?.response?.status === 403) {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error","bottom-right");
              this.$router.push({
                name: "ClaimHSRegisterDetail",
                params: {id: this.id},
              });
            } else {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error","bottom-right");

            }
          })
          .finally(() => (this.isLoading = false));
    },
  },
  mounted() {
    this.getData();
  },
};
</script>
<style>
.p-dialog-mask.p-component-overlay {
  width: 100% !important;
}
</style>