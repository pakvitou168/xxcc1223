<template>
    <div v-if="modelValue" class="schema-benefit-item " >
        <div class="flex">
            <div class="w-full mt-4 mb-6 pl-3">
                <div class="font-bold text-lg" v-html="schemaName"></div>
            </div>
        </div>
        <div class="grid grid-cols-5 gap-x-2 gap-y-2 ml-6" v-if="updatedValue.schema_type == 'STANDARD'">
            <div class="col-span-2 grid grid-cols-2 gap-x-2 gap-y-2" v-if="updatedValue.max_number_of_day">
                <div>
                  <label for="from_date" class="block mb-1 font-bold">Admission Date</label>
                  <Calendar  placeholder="dd-M-yy" v-model="updatedValue.admission_date"  @change="changeDate" dateFormat="dd-M-yy" />
                  <small v-if="addmissionDateErr" class="p-error block mt-1">
                    {{addmissionDateErr.addmissionDateErr[0]}}
                  </small>
                </div>
                <div class="">
                  <label class="block mb-1 font-bold">{{'Days (max= ' + updatedValue.max_number_of_day + ' days ) '}}</label>
                  <InputNumber
                      class="w-full"
                      name="total_actual_incurred_expense"
                      v-model="updatedValue.number_of_day"
                      placeholder="Amount"
                      @keyup="changeDay"
                      @change="changeDay"
                  />
                  <small v-if="daysErr" class="p-error block mt-1">
                    {{daysErr[0]}}
                  </small>
                </div>
                <div>
                  <label for="from_date" class="block mb-1 font-bold">Admission Date</label>
                  <Calendar  placeholder="dd-M-yy" v-model="updatedValue.discharge_date"  @change="changeDate" dateFormat="dd-M-yy" />
                  <small v-if="dischargeDateErr" class="p-error block mt-1">
                    {{dischargeDateErr[0]}}
                  </small>
                </div>
            </div>
            <div class="col-span-2" v-else>
                &nb&nbsp;
            </div>
            <div class="col-span-3 grid grid-cols-3 gap-x-2 gap-y-2">
                <div>
                  <label class="block mb-1 font-bold">Limit (USD)</label>
                  <InputNumber
                      class="w-full"
                      name="total_actual_incurred_expense"
                      v-model="updatedValue.limit_amount"
                      placeholder="Limit (USD)"
                      disabled
                  />
                </div>
                <div>
                  <label class="block mb-1 font-bold">Actual Incurred Expense</label>
                  <InputNumber
                      class="w-full"
                      placeholder="Actual Incurred Expense"
                      v-model="updatedValue.actual_incurred_expense"
                      name="actual_incurred_expense"
                      @keyup="changeActualExpense"
                  />
                </div>
                <div>
                  <label class="block mb-1 font-bold">Maximum Payable</label>
                  <InputNumber
                      class="w-full maximum_payable"
                      placeholder="Maximum Payable"
                      v-model="updatedValue.maximum_payable"
                      name="maximum_payable"
                      disabled
                  />
                </div>
                <div class="col-span-3 grid grid-cols-3 gap-x-2 gap-y-2">
                    <div class="col-span-2">&nbsp;</div>
                    <div>
                      <label class="block mb-1 font-bold">Non-Payable Expense</label>
                      <InputNumber
                          class="w-full none_payable_expense"
                          placeholder="Non-Payable Expense"
                          v-model="updatedValue.non_payable_expense"
                          name="none_payable_expense"
                          disabled
                      />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-x-5 gap-y-2 ml-6" v-else>
            <div>
              <label class="block mb-1 font-bold">Limit (USD)</label>
              <InputNumber
                  class="w-full"
                  placeholder="Limit (USD)"
                  v-model="updatedValue.limit_amount"
                  disabled

              />
              <small v-if="daysErr" class="p-error block mt-1">
                {{daysErr[0]}}
              </small>
            </div>
            <div>
              <label class="block mb-1 font-bold">Remaining (USD)</label>
              <InputNumber
                  class="w-full"
                  placeholder="Remaining (USD)"
                  v-model="updatedValue.remaining"
                  disabled
              />
            </div>
            <div>
              <label class="block mb-1 font-bold">Actual Incurred Expense</label>
              <InputNumber
                  class="w-full actual_expense"
                  placeholder="Actual Incurred Expense"
                  v-model="updatedValue.actual_incurred_expense"
                  disabled
                  @keyup="changeActualExpense"
              />
            </div>
            <div>
              <label class="block mb-1 font-bold">Non-Payable Expense</label>
              <InputNumber
                  class="w-full maximum_payable"
                  placeholder="Non-Payable Expense"
                  v-model="updatedValue.maximum_payable"
                  disabled
              />
            </div>
            <div>
              <label class="block mb-1 font-bold">Maximum Payable</label>
              <InputNumber
                  class="w-full none_payable_expense"
                  placeholder="Maximum Payable"
                  v-model="updatedValue.non_payable_expense"
                  disabled
              />
            </div>
        </div>
        <div class="p-2"></div>
        <hr>
    </div>
</template>

<script>
import { update } from 'lodash';

export default {
    props: {
        value: {
            type: Object,
            default:() => ({
              admission_date: '',
              discharge_date: '',
              number_of_day: '',
              schema_type: '',
              schema_name: ''
            }),
        },
        addmissionDateErr: Array,
        daysErr: Array,
        dischargeDateErr: Array,
        feePerDayErr: Array,
        index: Number,
        modelValue: {
            type: Object,
            default:() => ({
              admission_date: '',
              discharge_date: '',
              number_of_day: '',
              schema_type: '',
              schema_name: ''
            }),
        },
    },
    created() {
      // Debug when component is created
      console.log('🏁 Child schema benefit item created:', {
        receivedValue: this.modelValue,
        type: typeof this.modelValue,
        // isArray: Array.isArray(this.modelValue),
        // isObject: Array.isObject(this.modelValue),
      });
    },
    computed: {
        updatedValue() {
          return this.modelValue;
        },
        schemaName() {
            return (this.updatedValue.schema_type == 'STANDARD' ? (this.index + 1) + '. ' : '') + this.updatedValue.schema_name
        },

    },
    methods: {
        changeDate(event) {
            let ele = event.target;
            let eleDay = ele.closest('.grid').querySelector('.days input');
            let diffDays = '';
            if (this.updatedValue.admission_date && this.updatedValue.discharge_date) {
                let admissionDate = this.updatedValue.admission_date;
                let dischargeDate = this.updatedValue.discharge_date;
                let date1 = new Date(admissionDate);
                let date2 = new Date(dischargeDate);
                let diffTime = Math.abs(date2 - date1);
                diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                eleDay.value = diffDays
            } else {
                let days = parseInt(this.updatedValue.number_of_day);
                if (days > 0) {
                    eleDay.value = diffDays
                }
            }
            if (diffDays != this.updatedValue.number_of_day) {
                this.$emit('input', JSON.parse(JSON.stringify(Object.assign(this.updatedValue, { number_of_day: diffDays }))));
            }
            eleDay.dispatchEvent(new Event('change', { 'bubbles': true }));
        },
        changeDay(event) {
            let ele = event.target;
            let days = parseFloat(ele.value)
            let actualExpense = parseFloat(this.updatedValue.actual_incurred_expense)
            if (actualExpense && days) {
                let actualDay = days;
                let limitDay = parseFloat(this.updatedValue.max_number_of_day);
                let limitAmount = parseFloat(this.updatedValue.limit_amount)
                let maxClaimable = (limitDay >= actualDay ? actualDay : limitDay) * limitAmount;
                let claimableAmount = (maxClaimable >= actualExpense ? actualExpense : maxClaimable);
                let nonePayableExpense = actualExpense - claimableAmount;
                this.updatedValue.maximum_payable = claimableAmount;
                this.updatedValue.non_payable_expense = nonePayableExpense;
            } else {
                this.updatedValue.maximum_payable = '';
                this.updatedValue.non_payable_expense = '';
            }
            this.$emit('input', JSON.parse(JSON.stringify(this.updatedValue)));
            this.$emit('changeExpense');
        },
        changeActualExpense(event) {
            let ele = event.target;
            let actualExpense = ele.value;
            let eleMaxPayable = ele.closest('.grid').querySelector('.maximum_payable input')
            if (actualExpense) {
                let days = parseFloat(this.updatedValue.number_of_day);
                let actualDay = days;
                let limitDay = parseFloat(this.updatedValue.max_number_of_day);
                let limitAmount = parseFloat(this.updatedValue.limit_amount);
                let remainingAmount = this.updatedValue.schema_type != 'STANDARD' ? parseFloat(this.updatedValue.remaining) : 0;
                let maxClaimable = remainingAmount > 0 ? remainingAmount : (limitDay ? ((limitDay >= actualDay ? actualDay : limitDay) * limitAmount) : limitAmount);
                let claimableAmount = (maxClaimable >= actualExpense ? actualExpense : maxClaimable);
                let nonePayableExpense = actualExpense - claimableAmount;
                eleMaxPayable.value = claimableAmount
                this.updatedValue.maximum_payable = claimableAmount;
                this.updatedValue.non_payable_expense = nonePayableExpense
            } else {
                eleMaxPayable.value = ''
                this.updatedValue.maximum_payable = '';
                this.updatedValue.non_payable_expense = ''
            }
            this.$emit('changeExpense');
            this.$emit('input', JSON.parse(JSON.stringify(this.updatedValue)));
        }
    },
    data() {
        return {
            // updatedValue: this.value

        }
    },
    updated() {
        if (this.modelValue) {
            this.updatedValue = this.modelValue
        }
    }
}
</script>