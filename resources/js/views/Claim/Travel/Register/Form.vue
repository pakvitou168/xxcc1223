<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Register
      </h2>
    </div>
    <div class="intro-y box grid gap-y-2 mt-5 p-5">
      <form @submit.prevent="handleSubmit">
        <div class="grid lg:grid-cols-4 gap-x-5 gap-y-2 pb-2">
          <div class="">
            <label class="block mb-1 font-bold" for="">Policy No. *</label>
            <AutoComplete
              placeholder="Policy No."
              v-model="formValues.policy"
              field="name"
              forceSelection
              :suggestions="lovs.filteredPolicies"
              class="w-full"
              :dropdown="true"
              @change="changePolicy"
              @complete="filterPolicies($event)"
              @item-select="preLoadInsuredPersons"
            >
              <template #item="slotProps">
                <div class="country-item">
                  <div>{{ slotProps.item.name }}</div>
                </div>
              </template>
            </AutoComplete>
            <ul v-if="errors.document_no" class="">
              <li class="">{{ errors.document_no[0] }}</li>
            </ul>
          </div>
          <div class="">
            <label class="block mb-1 font-bold" for="">Insured Person *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              name="data_detail_id"
              v-model="formValues.data_detail_id"
              optionLabel="name"
              optionValue="code"
              placeholder="Insured person"
              :filter="true"
              :showClear="true"
              @filter="filterInsuredPersons"
              :options="lovs.insuredPersons"
              dataKey="code"
            >
              <template #option="slotProps">
                <div class="flex align-items-center">
                  <div>
                    {{ slotProps.option.name }} . {{ slotProps.option.gender }} .
                    {{ slotProps.option.dob }}
                  </div>
                </div>
              </template>
            </Dropdown>
            <ul v-if="errors.data_detail_id" class="">
              <li class="">
                {{ errors.data_detail_id[0] }}
              </li>
            </ul>
          </div>
          <div class="">
            <label class="block mb-1 font-bold" for="">Cause of Loss</label>
            <Dropdown
              v-model="formValues.cause_of_loss"
              :options="lovs.causeOfLoss"
              class="w-full p-inputtext-sm"
              optionLabel="name"
              optionValue="code"
              optionDisabled="disabled"
              @change="selectCauseofLoss"
              placeholder="Cause of loss"
              :filter="true"
              :showClear="true"
            >
              <template #option="slotProps">
                <div class="flex align-items-center">
                  <div>
                    {{ slotProps.option.name }}
                  </div>
                </div>
              </template>
            </Dropdown>
          </div>
          <div class="">
            <label class="block mb-1 font-bold" for="">Date of loss *</label>
            <Calendar
              placeholder="dd-M-yy"
              dateFormat="dd-M-yy"
              label="Date of loss *"
              name="date_of_loss"
              validation="required"
              step="any"
              v-model="formValues.date_of_loss"
            />
            <ul v-if="errors.date_of_loss" class="">
              <li class="">{{ errors.date_of_loss[0] }}</li>
            </ul>
          </div>

          <div class="">
            <label class="block mb-1 font-bold" for="">Date of notification *</label>
            <Calendar
              placeholder="dd-M-yy"
              dateFormat="dd-M-yy"
              label="Date of notification *"
              v-model="formValues.notification_date"
              name="notification_date"
              step="any"
            />
            <ul v-if="errors.notification_date" class="">
              <li class="">{{ errors.notification_date[0] }}</li>
            </ul>
          </div>
          <div class="">
            <label class="block mb-1 font-bold" for="">Reserve amount *</label>
            <InputNumber
              label="Reserve amount *"
              placeholder="Reserve amount"
              name="reserve_amount"
              validation="required"
              v-model="formValues.reserve_amount"
              step="any"
              class="w-full"
            />
            <ul v-if="errors.reserve_amount" class="">
              <li class="">{{ errors.reserve_amount[0] }}</li>
            </ul>
          </div>
          <div class="">
            <label class="block mb-1 font-bold" for="">Location of loss *</label>
            <InputText
              label="Location of loss *"
              placeholder="Location of loss"
              name="location_of_loss"
              v-model="formValues.location_of_loss"
            />
            <ul v-if="errors.location_of_loss" class="">
              <li class="">{{ errors.location_of_loss[0] }}</li>
            </ul>
          </div>
          <div class="">
            <label for="" class="font-bold mb-1 block">Loss description *</label>
            <Textarea
              placeholder="Loss description"
              class="w-full"
              rows="5"
              cols="30"
              v-model="formValues.loss_description"
            />
            <ul v-if="errors.loss_description" class="">
              <li class="">{{ errors.loss_description[0] }}</li>
            </ul>
          </div>
        </div>
        <div class="w-full border-t pt-2 border-gray-300">
          <div>
            <label for="" class="font-bold block">Deductible *</label>
          </div>
          <div
            v-for="(deductible, index) in lovs.deductibles"
            :key="index"
            class="pt-5 pb-2 border-gray-300 first:pt-0"
          >
            <DeductibleItem
              v-model="formValues.deductible[index]"
              :info="deductible"
              :index="index"
            />
          </div>
        </div>
        <div class="w-full border-t pt-2 border-gray-300">
          <div>
            <label for="" class="font-bold block">Cause of loss *</label>
          </div>
          <div class="pt-5 pb-2">
            <div
              v-for="(item, index) in lovs.causeOfLoss"
              :key="index"
              class="py-2 first:pt-0"
            >
              <label for="" class="font-bold block">{{ item.name }}</label>
              <div class="grid lg:grid-cols-4">
                <div class="col-span-3">
                  <span>{{ item.cause }}</span>
                </div>
                <div>
                  <InputNumber
                    label="Reserve amount *"
                    placeholder="Reserve amount"
                    name="reserve_amount"
                    validation="required"
                    v-model="formValues.reserve_amount"
                    step="any"
                    class="w-full"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-right mt-5">
          <router-link
            :to="{ name: 'TravelClaimRegisterIndex' }"
            class="btn btn-outline-secondary w-24 mr-1"
          >
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
import ClaimRegisterService from "@/services/claim/travel/claim_register.service";
import InsuredDetail from "@/views/Claim/Travel/Register/Components/InsuredDetail.vue";
import DeductibleItem from "@/views/Claim/Travel/Register/Components/DeductibleItem.vue";
import deductibleData from "@/services/travel/policy/deductibleData.service";

export default {
  components: {
    InsuredDetail,
    DeductibleItem,
  },
  data() {
    return {
      id: this.$route.params.id ?? null,
      formValues: {
        document_no: null,
        data_id: null,
        premium: null,
        claim_no: null,
        remark: null,
        deductible: [],
      },
      lovs: {
        policies: [],
        insuredPersons: [],
        filteredPolicies: [],
        causeOfLoss: [],
        deductibles: [],
      },

      errors: {},
      isLoading: false,

      showDialog: false,
      showClinicDialog: false,
      submitted: false,
      genderOptions: {
        M: "M",
        F: "F",
      },
    };
  },
  computed: {
    totalPremium() {
      return this.formValues.details
        ? this.formValues.details.reduce(
            (partialSum, a) => parseFloat(partialSum) + parseFloat(a.premium),
            0
          )
        : 0;
    },
  },
  watch: {
    totalPremium: function (newValue) {
      this.formValues.total_premium = newValue;
    },
  },
  methods: {
    handleSubmit() {
      this.isLoading = true;

      const method = this.id ? "PATCH" : "POST";
      ClaimRegisterService.save(
        {
          ...this.formValues,
          ...(method === "PATCH" && { id: this.id }),
        },
        method
      )
        .then((res) => {
          this.$notify(
            {
              group: "bottom",
              title: "Success",
              text: res.data?.message,
            },
            4000
          );
          this.$router.push({ name: "ClaimHSRegisterIndex" });
        })
        .catch((err) => {
          if (err?.response?.status === 422) {
            this.$notify(
              {
                group: "bottom",
                title: "Error",
                text: "Validation Error",
              },
              4000
            );

            this.errors = err.response.data.errors;
          } else {
            this.$notify(
              {
                group: "bottom",
                title: "Error",
                text: err?.response?.data?.message,
              },
              4000
            );
          }
        })
        .finally(() => (this.isLoading = false));
    },

    getLovs(query) {
      ClaimRegisterService.getLovs(query)
        .then((res) => {
          this.lovs.policies = res.data.policies;
          this.lovs.filteredPolicies = this.lovs.policies;
          this.lovs.clinics = res.data.clinics;
        })
        .finally(() => {
          if (this.id) {
            this.getData();
          }
        });
    },

    filterPolicies(event) {
      setTimeout(() => {
        ClaimRegisterService.filterPolicy(event.query).then((res) => {
          this.lovs.filteredPolicies = res.data;
        });
      }, 250);
    },

    getData() {
      if (this.id) {
        ClaimRegisterService.getData(this.id)
          .then((res) => {
            this.formValues = res.data;
            // this.getCauseOfLoss(this.formValues.data_id);
          })
          .catch((err) => {
            this.$notify(
              {
                group: "bottom",
                title: "Error",
                text: err?.response?.data?.message,
              },
              4000
            );
          })
          .finally(() => {
            this.preLoadInsuredPersons();
          });
      }
    },

    getInsuredPersons(search) {
      if (this.formValues.policy_id) {
        ClaimRegisterService.getInsuredPersons(this.formValues.policy_id, search).then(
          (res) => {
            this.lovs.insuredPersons = res.data;
          }
        );
      }
    },
    preLoadInsuredPersons() {
      this.formValues.policy_id = this.formValues.policy.code;
      this.getInsuredPersons();
    },
    filterInsuredPersons: _.debounce(function (event) {
      this.getInsuredPersons(event.value);
    }, 500),
    getCauseOfLoss(policy) {
      ClaimRegisterService.getCauseOfLoss(policy.data_id).then((res) => {
        this.lovs.causeOfLoss = res.data;
      });
      deductibleData.get(policy.data_id).then((res) => {
        this.lovs.deductibles = res.data?.deductible_data;
        this.formValues.deductible = this.lovs.deductibles;
      });
    },
    changePolicy(event) {
      let policy = event.value;
      this.getCauseOfLoss(policy);
    },
    selectCauseofLoss(event) {
      // const causeOfLoss = event.value
      // if (causeOfLoss) {
      //   const item = this.lovs.causeOfLoss.filter((item) => item.code == causeOfLoss)[0]
      //   this.formValues.schema_detail_code = item.schema_detail_code;
      //   this.formValues.schema_plan = item.schema_plan;
      //   this.formValues.schema_type = item.schema_type;
      // } else {
      //   this.formValues.schema_detail_code = '';
      //   this.formValues.schema_plan = '';
      //   this.formValues.schema_type = '';
      // }
    },
  },
  mounted() {
    this.getLovs();
  },
};
</script>
<style>
.p-dialog-mask.p-component-overlay {
  width: 100% !important;
}
</style>
