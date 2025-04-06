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
        <div class="grid lg:grid-cols-4 gap-x-5 gap-y-2">
          <div class="">
            <label class=" block mb-1 font-bold" for="">Policy No. *</label>
            <AutoComplete placeholder="Policy No." v-model="formValues.policy" field="name" forceSelection :suggestions="lovs.filteredPolicies"
                          class="w-full" :dropdown="true"
                          @complete="filterPolicies($event)" @item-select="preLoadInsuredPersons">
              <template #item="slotProps">
                <div class="country-item">
                  <div>{{ slotProps.item.name }}</div>
                </div>
              </template>
            </AutoComplete>
            <span v-if="errors.policy_id" class="p-error block">
              {{ errors.policy_id[0] }}
            </span>
          </div>
          <div class="">
            <label class=" block mb-1 font-bold" for="">Insured Person *</label>
            <Dropdown class="w-full p-inputtext-sm" name="data_detail_id" v-model="formValues.data_detail_id"
                      optionLabel="name" optionValue="code" placeholder="Insured person" :filter="true"
                      :showClear="true"
                      @change="changeInsuredPerson" @filter="filterInsuredPersons" :options="lovs.insuredPersons"
                      dataKey="code">
              <template #option="slotProps">
                <div class="flex align-items-center">
                  <div>
                    {{ slotProps.option.name }} .
                    {{ slotProps.option.gender }} .
                    {{ slotProps.option.dob }}
                  </div>
                </div>
              </template>
            </Dropdown>
            <span v-if="errors.data_detail_id " class="p-error block">
              {{ errors.data_detail_id[0] }}
            </span>
          </div>
          <div class="">
            <label class=" block mb-1 font-bold" for="">Cause of Loss</label>
            <Dropdown v-model="formValues.cause_of_loss" :options="lovs.causeOfLoss" class="w-full p-inputtext-sm"
                      optionLabel="name" optionValue="code" optionDisabled="disabled" @change="selectCauseofLoss"
                      placeholder="Cause of loss" :filter="true" :showClear="true">
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
            <label class=" block mb-1 font-bold" for="">Cause of loss dis. *</label>
            <InputText
                label="Cause of loss dis. *"
                placeholder="Cause of loss dis"
                name="cause_of_loss_disability"
                v-model="formValues.cause_of_loss_disability"
                validation="required"

            />
            <span v-if="errors.cause_of_loss_disability " class="p-error block">
              {{ errors.cause_of_loss_disability[0] }}
            </span>
          </div>
          <div class="">
            <label class=" block mb-1 font-bold" for="">Date of loss *</label>
            <Calendar  placeholder="" dateFormat="dd-M-yy" label="Date of loss *" name="date_of_loss" validation="required"
                      step="any"

                      v-model="formValues.date_of_loss"
            />
            <span v-if="errors.date_of_loss " class="p-error block">
              {{ errors.date_of_loss[0] }}
            </span>
          </div>

          <div class="">
            <label class=" block mb-1 font-bold" for="">Date of notification *</label>
            <Calendar  placeholder="" dateFormat="dd-M-yy"
                      label="Date of notification *"

                      v-model="formValues.notification_date"

                      name="notification_date"
                      step="any"/>
            <span v-if="errors.notification_date " class="p-error block">
              {{ errors.notification_date[0] }}
            </span>
          </div>
          <div class="">
            <label class=" block mb-1 font-bold">Clinic *</label>
            <div class="flex">
              <Dropdown v-model="formValues.clinic_id" class="w-full p-inputtext-sm" placeholder="Clinic"
                        optionLabel="name" optionValue="code" :filter="true" :showClear="true" :options="lovs.clinics"/>
              <button class="btn btn-primary leading-6 ml-1" type="button" @click="openClinicDialog">
                <span class="pi pi-plus"></span>
              </button>
            </div>
            <span v-if="errors.clinic_id" class="p-error block">
              {{ errors.clinic_id[0] }}
            </span>
          </div>

          <div class="">
            <label class=" block mb-1 font-bold" for="">Reserve amount *</label>
            <InputNumber
                label="Reserve amount *"
                placeholder="Reserve amount"
                name="reserve_amount"
                validation="required"
                v-model="formValues.reserve_amount"
                step="any"/>
            <span v-if="errors.reserve_amount" class="p-error block">
              {{ errors.reserve_amount[0] }}
            </span>
          </div>
          <div class="">
            <label class=" block mb-1 font-bold" for="">Location of loss *</label>
            <InputText label="Location of loss *"
                       placeholder="Location of loss"
                       name="location_of_loss"
                       v-model="formValues.location_of_loss"
            />
            <span v-if="errors.location_of_loss" class="p-error block">
              {{ errors.location_of_loss[0] }}
            </span>
          </div>
          <div class="">
            <label for="" class="font-bold mb-1 block">Loss description *</label>
            <Textarea placeholder="Loss description"
                      class="w-full" rows="5" cols="30" v-model="formValues.loss_description" />
            <span v-if="errors.loss_description" class="p-error block">
              {{ errors.loss_description[0] }}
            </span>
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
    <ClinicDialog header="Add Clinic" :isVisible="showClinicDialog" @hideDialog="hideClinicDialog"
                  @setSelectedClinic="setSelectedClinic"/>
  </div>
</template>

<script>
import ClaimRegisterService from "@/services/claim/hs/claim_register.service";
import InsuredDetail from "@/views/Claim/HS/Register/Components/InsuredDetail.vue";
import ClinicDialog from "./Components/ClinicDialog.vue";

export default {
  components: {
    InsuredDetail,
    ClinicDialog
  },
  data() {
    return {
      ERROR_MESSAGE:"Something went wrong!",
      SUCCESS_MESSAGE:"Success!",
      id: this.$route.params.id ?? null,
      formValues: {
        document_no: null,
        data_id: null,
        premium: null,
        claim_no: null,
        remark: null,
      },
      lovs: {
        policies: [],
        insuredPersons: [],
        filteredPolicies: [],
        causeOfLoss: [],
        clinics: []
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
            ...(method === "PATCH" && {id: this.id}),
          },
          method
      )
          .then((res) => {
            notify(res.data?.message || this.SUCCESS_MESSAGE, "success","bottom-right");

            this.$router.push({name: "ClaimHSRegisterIndex"});
          })
          .catch((err) => {
            console.log(err.response?.data?.message);
            if (err.response?.status === 422) {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error","bottom-right");

              this.errors = err.response.data.errors;
            } else {
              notify(err.response?.data?.message || this.ERROR_MESSAGE, "error","bottom-right");
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
          this.lovs.filteredPolicies = res.data
        })
      }, 250);
    },

    getData() {
      if (this.id) {
        ClaimRegisterService.getData(this.id)
            .then((res) => {
              this.formValues = res.data;
              this.getCauseOfLoss(this.formValues.data_detail_id);
            })
            .catch((err) => {
              notify(err.response?.data?.message??'Error', 'error','bottom-right');
            })
            .finally(() => {
              this.preLoadInsuredPersons();
            });
      }
    },

    getInsuredPersons(search) {
      if (this.formValues.policy_id) {
        ClaimRegisterService.getInsuredPersons(
            this.formValues.policy_id,
            search
        ).then((res) => {
          this.lovs.insuredPersons = res.data;
        });
      }
    },
    preLoadInsuredPersons() {
      this.formValues.policy_id = this.formValues.policy.code
      this.getInsuredPersons();
    },
    filterInsuredPersons: _.debounce(function (event) {
      this.getInsuredPersons(event.value);
    }, 500),
    getCauseOfLoss(insuredId) {
      ClaimRegisterService.getCauseOfLoss(this.formValues.policy_id, insuredId).then((res) => {
        this.lovs.causeOfLoss = res.data
      })
    },
    changeInsuredPerson(event) {
      let insuredId = event.value;
      this.getCauseOfLoss(insuredId)
    },
    selectCauseofLoss(event) {
      const causeOfLoss = event.value
      if (causeOfLoss) {
        const item = this.lovs.causeOfLoss.filter((item) => item.code == causeOfLoss)[0]
        this.formValues.schema_detail_code = item.schema_detail_code;
        this.formValues.schema_plan = item.schema_plan;
        this.formValues.schema_type = item.schema_type;
      } else {
        this.formValues.schema_detail_code = '';
        this.formValues.schema_plan = '';
        this.formValues.schema_type = '';
      }
    },
    openClinicDialog() {
      this.showClinicDialog = true
    },
    hideClinicDialog() {
      this.showClinicDialog = false
    },
    setSelectedClinic(clinic) {
      this.lovs.clinics.unshift(clinic)
      this.formValues.clinic_id = clinic.code
    }
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