<template>
    <div>
        <!-- Start Header  -->
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Payment
            </h2>
        </div>
        <!-- End Header  -->
        <!--Start Form  -->
        <div class="intro-y box grid gap-y-2 mt-5 p-5">
            <form @submit.prevent="handleSubmit">
                <div class="grid lg:grid-cols-4 gap-x-5 gap-y-2">
                    <!-- @Claim No -->
                    <div>
                        <label class="block mb-1 font-bold"
                            >Claims No. *</label
                        >
                        <AutoComplete
                            forceSelection
                            v-model="formValues.claim_no"
                            placeholder="Claim No"
                            class="w-full"
                            :suggestions="filteredRegisters"
                            :dropdown="true"
                            @complete="filterRegisters($event)"
                            @item-select="listCauseOfLosses"
                            name="claim_no"
                            :disabled=" id ? true : false"
                        />
                        <span v-if="claimNoError" class="p-error block" >
                          claim no is required
                        </span>
                    </div>
                    <!-- @Payment Type -->
                    <div>
                        <label class="block mb-1 font-bold"
                            >Payment Type *</label
                        >
                        <div class="flex">
                          <Dropdown
                              v-model="formValues.payment_type"
                              class="w-full p-inputtext"
                              optionLabel="label"
                              optionValue="value"
                              :options="lovs.payment_types"
                              @change="checkPaymentType()"
                              placeholder="Payment Type"
                               />
                        </div>
                        <span v-if="paymentTypeError" class="p-error block">
                            payment type is required
                        </span>
                    </div>
                    <!-- @Payees -->
                    <div>
                        <label class="block mb-1 font-bold"
                            >Payee Name *</label
                        >
                        <div class="flex">
                            <Dropdown
                                v-model="formValues.payee_id"
                                class="w-full p-inputtext-sm"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Payee Name"
                                :filter="true"
                                :showClear="true"
                                :options="lovs.payees"
                                name="payee_id" 
                                @change="checkPayee()"
                            />
                            <button
                                type="button"
                                class="btn btn-primary leading-6 ml-1"
                                @click.prevent="openDialog()"
                            >
                                <span class="pi pi-plus"></span>
                            </button>
                        </div>
                        <span v-if="payeeError" class="p-error block">
                            payee is required
                        </span>
                    </div>
                    <!-- @Claimant's name -->
                    <div>
                      <label class="mb-1 block font-bold">Claimant's Name</label>
                      <InputText
                          class="w-full p-inputtext-sm"
                          v-model="formValues.claim.claimant_name"
                          placeholder="Claimant's Name"
                      />
                    </div>
                    <!-- @for Treatment of -->
                    <div>
                      <label class="mb-1 block font-bold">For Treatment of</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.claim.cause_of_loss"
                            placeholder="For Treatment of"
                      />
                    </div>
                    <!-- @Disability -->
                    <div>
                        <label class="block mb-1 font-bold"
                            >Disability</label
                        >
                        <InputText
                            v-model="formValues.claim.cause_of_loss_disability"
                            placeholder="Disability"
                            disabled="true"
                        />
                    </div>
                    <!-- @Amount -->
                    <div>
                        <label class="block mb-1 font-bold">Amount</label>
                        <InputNumber
                            disabled="true"
                            v-model="amount"
                            placeholder="Amount"
                            class="w-full"
                        />
                    </div>
                    <!-- @Payment Date -->
                    <div>
                        <label class="block mb-1 font-bold">Payment Date*</label>
                        <Calendar  placeholder=""
                            disabled="true"
                            v-model="formValues.payment_date"
                            label="Payment Date *"
                        />
                    </div>
                </div>
                <div class="text-right mt-5">
                    <router-link
                        :to="{ name: 'ClaimHSPaymentIndex' }"
                        class="btn btn-outline-secondary w-24 mr-1"
                    >
                        Cancel
                    </router-link>
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="btn btn-primary w-24"
                    >
                        {{ isLoading ? "Saving ..." : "Save" }}
                    </button>
                </div>
            </form>
        </div>
        <!--Start Form  -->
        <PayeeDialog
            :isVisible="showDialog"
            :payeeTypes="lovs.payee_types"
            @hideDialog="hideDialog"
            @setPayeeTypeId="setPayeeTypeId"
        />
    </div>
</template>
<script>
import ClaimHSPaymentService from "@/services/claim/hs/claim_payment.service";
import PayeeDialog from "./Components/PayeeDialog.vue";
export default {
    components: {
        PayeeDialog,
    },
    data() {
        return {
            ERROR_MESSAGE:"Something went wrong!",
            SUCCESS_MESSAGE:"Success!",
            id: this.$route.params.id ?? null,
            formValues: {
                detail_id: null,
                claim_no: '',
                claim: {},
                cause_of_losses: [],
                document_no: null,
                payment_date: new Date().toISOString().slice(0, 10),
                created_at: new Date().toISOString().slice(0, 10),
                payment_type: null,
                payee_id: null,
            },
            lovs: {
                registers: [],
                payees: [],
                payee_types: [],
                payment_types: [],
            },
            filteredRegisters: [],
            claimNoError: false,
            payeeError: false,
            paymentTypeError: false,
            errors: {},
            isLoading: false,
            showDialog: false,
            selectedPayeeIndex: null,
        };
    },
    computed: {
        amount: {
            get(){
                return parseFloat(this.formValues.total_actual_incurred_expense) > parseFloat(this.formValues.total_maximum_payable)
                        ? this.formValues.total_maximum_payable
                        : this.formValues.total_actual_incurred_expense;
            },
            set(value){
                this.formValues.amount = value;
            }
        },
    },
    methods: {
        checkClaimNo(){
            if(this.formValues.claim_no == null || this.formValues.claim_no == ''){
                this.claimNoError = true
            }else{
                this.claimNoError = false
            }
        },
        checkPaymentType(){
            if(this.formValues.payment_type == null || this.formValues.payment_type == ''){
                this.paymentTypeError = true
            }else{
                this.paymentTypeError = false
            }
        },
        checkPayee(){
            if(this.formValues.payee_id == null || this.formValues.payee_id == ''){
                this.payeeError = true
            }else{
                this.payeeError = false
            }
        },
        checkValidate(){
            this.checkPaymentType()
            this.checkPayee()
            this.checkClaimNo()
        },
        handleSubmit() {
            this.checkValidate()
            if(this.payeeError || this.claimNoError || this.paymentTypeError ) return

            this.isLoading = true;

            const method = this.id ? "PUT" : "POST";
            
            ClaimHSPaymentService.save(
                {
                    ...this.formValues,
                    ...(method === "PUT" && { id: this.id }),
                },
                method
            )
                .then((res) => {
                    notify(res.data?.message || this.SUCCESS_MESSAGE, "success", "bottom-right");
                    this.$router.push({ name: "ClaimHSPaymentIndex" });
                })
                .catch((err) => {
                    if (err?.response?.status === 422) {
                        notify(err.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");

                        this.errors = err.response.data.errors;
                    } else {
                        notify(err.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
                    }
                })
                .finally(() => (this.isLoading = false));
        },
        filterRegisters(event) {
            setTimeout(() => {
                if (!event.query.trim().length) {
                    this.filteredRegisters = [...this.lovs.registers];
                } else {
                    this.filteredRegisters = this.lovs.registers.filter(
                        (item) => {
                            return item
                                .toLowerCase()
                                .startsWith(event.query.toLowerCase());
                        }
                    );
                }
            }, 250);
        },
        async listCauseOfLosses({ value }) {
            await ClaimHSPaymentService.listCauseOfLosses(value).then((res) => {
                Object.assign(this.formValues.claim, res.data.claim)
                Object.assign(this.formValues, res.data.claim_hs_detail);
                this.checkClaimNo()
            });
        },
        openDialog(index) {
            this.showDialog = true;
            this.selectedPayeeIndex = index;
        },
        hideDialog() {
            this.showDialog = false;
        },
        setPayeeTypeId(item) {
            /*this.lovs.payees.unshift(item);
            this.formValues.cause_of_losses[this.selectedPayeeIndex].payee_id = item.value;*/

            this.lovs.payees.unshift(item);
            this.formValues.payee_id = item.value;
            this.payeeError = false;
        },
        getLovs() {
            ClaimHSPaymentService.getLovs().then((res) => {
                this.lovs.registers = res.data?.registers;
                this.lovs.payees = res.data?.payees;
                this.lovs.payee_types = res.data?.payee_types;
                this.lovs.payment_types = res.data?.payment_types;
            });
        },
        getData() {
            if (this.id) {
                ClaimHSPaymentService.getData(this.id)
                    .then(async (res) => {
                        Object.assign(this.formValues, res.data.claim_transaction)
                        Object.assign(this.formValues.claim, res.data.claim)
                        Object.assign(this.formValues, res.data.claim_hs_detail);
                    })
                    .catch((err) => {
                        notify(err.data?.message || this.ERROR_MESSAGE, "error", "bottom-right");
                    });
            }
        },
    },
    mounted() {
        this.getLovs();
        this.getData();
    },
};
</script>
