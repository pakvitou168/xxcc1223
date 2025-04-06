<template>
  <div>
    <div class="intro-y flex items-center mt-5">
      <h2 class="text-lg font-medium mr-auto">Edit Renewal</h2>
      <!-- <button class="btn btn-primary shadow-md mr-2 leading-6" :disabled="isSubmitting"
          @click="handleSubmit">Submit<span v-if="isSubmitting">...</span></button> -->
    </div>
    <div class="p-5">
      <form @submit.prevent="handleSubmit">
        <div>
          <div class="grid lg:grid-cols-4 gap-x-5 gap-y-2" v-if="isShownInfoTab">
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
          <div>
            <span v-if="!lovs.deductibles.length">Please choose a policy.</span>
            <div
              v-for="(deductible, index) in lovs.deductibles"
              :key="index"
              class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0"
            >
              <DeductibleItem
                v-model="formValues.deductible[index]"
                :info="deductible"
                :index="index"
              />
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
          </div>
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
    DeductibleItem,
    InsuredDetail,
  },
  data() {
    return {
      activeTab: 0,
      id: this.$route.params.id ?? null,
      isSubmitting: false,
      totalPremium: null,
      tabs: [
        {
          title: "Claim Information",
          target: "#info",
          classes: "py-3 sm:mr-8 cursor-pointer active",
        },
        {
          title: "Deductibles",
          target: "#deductible",
          classes: "py-3 cursor-pointer sm:mr-8",
        },
      ],
      isShownInfoTab: true,
      isShownDeductibleTab: false,

      deductibleTabKey: 0,
      requireDeductibleTabRendering: false,
      requireUpdateTotalPremium: false,
      formValues: {
        document_no: null,
        data_id: null,
        premium: null,
        claim_no: null,
        remark: null,
        deductible: null,
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

  watch: {
    activeTab(newVal, oldVal) {
      if (newVal === 1) {
        this.isShownInfoTab = true;
      } else if (newVal === 2) {
        this.isShownDeductibleTab = true;
      }
    },
  },
  methods: {
    changeTab(event, tab) {
      if (tab.target === "#info") {
        this.isShownInfoTab = true;
        this.isShownDeductibleTab = false;
      } else if (tab.target === "#deductible") {
        this.isShownDeductibleTab = true;
        this.isShownInfoTab = false;
        if (this.requireDeductibleTabRendering) {
          this.deductibleTabKey += 1;
          // After re-rendering the deductible tab set requireDeductibleTabRendering to false
          this.setRequireDeductibleTabRenderingStatus(false);
        }
      }
    },
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
        console.log(this.formValues);
      });
    },
    changePolicy(event) {
      let policy = event.value;
      this.getCauseOfLoss(policy);
    },
    selectCauseofLoss(event) {},
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
