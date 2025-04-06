<template>
  <div>
    <LoadingIndicator v-if="isLoading" />
    <div v-else class="col-span-12 p-4">
      <FormulateForm @submit="handleSubmit" v-model="formValues" >
        <div class="grid grid-cols-2 gap-x-6">
          <FormulateInput
              type="textarea"
              name="insured_name"
              label="Insured Name *"
              validationName="Insured Name"
              validation="required"
              placeholder="Insured Name"
              rows="5"
              :disabled="!editable"
          />
          <FormulateInput
              type="textarea"
              name="insured_name_kh"
              label="Insured Name (Khmer) *"
              validationName="Insured Name (Khmer)"
              validation="required"
              placeholder="Insured Name (Khmer)"
              rows="5"
              :disabled="!editable"
          />
          <FormulateInput
              type="select"
              name="geographical_limit"
              validationName="Geographical Limit"
              label="Geographical Limit *"
              validation="required"
              placeholder="Geographical Limit"
              :options="formLovs.geographicalLimits"
              :error="
                errors.geographical_limit ? errors.geographical_limit[0] : null
              "
              :disabled="!editable"
          />
          <div class="formulate-input">
            <label class="formulate-input-label" for=""
            >Endorsement Clause *</label
            >
            <MultiSelect
                v-model="formValues.endorsement_clauses"
                class="w-full p-inputtext-sm"
                display="chip"
                optionLabel="label"
                optionValue="value"
                :options="formLovs.endorsementClauses"
                :filter="false"
                :showClear="true"
                :disabled="!editable"
            />
            <ul
                v-if="errors.endorsement_clauses"
                class="formulate-input-errors"
            >
              <li class="formulate-input-error">
                {{ errors.endorsement_clauses[0] }}
              </li>
            </ul>
          </div>
          <div class="formulate-input">
            <label class="formulate-input-label" for=""
            >General Exclusion *</label
            >
            <MultiSelect
                v-model="formValues.general_exclusions"
                class="w-full p-inputtext-sm"
                display="chip"
                optionLabel="label"
                optionValue="value"
                :options="formLovs.generalExclusions"
                :filter="true"
                :showClear="true"
                :disabled="!editable"
            />
            <ul v-if="errors.general_exclusions" class="formulate-input-errors">
              <li class="formulate-input-error">
                {{ errors.general_exclusions[0] }}
              </li>
            </ul>
          </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
          <FormulateInput
              type="select"
              name="sale_channel"
              validationName="Business Channel"
              label="Business Channel *"
              validation="required"
              placeholder="Business Channel"
              :options="formLovs.saleChannelOptions"
              @input="changeBusinessCategory"
              :disabled="!editable"
          />

          <FormulateInput
              type="select"
              name="business_code"
              validationName="Business Name"
              label="Business Name *"
              validation="required"
              placeholder="Business Name"
              :options="formLovs.businessChannelOptions"
              @change="changeBusinessChannel"
              :disabled="!editable"
          />
        </div>
        <div class="grid grid-cols-2 gap-x-10">
          <FormulateInput
              type="number"
              min="0"
              name="commission_rate"
              label="Commission Rate %"
              validation="min:0"
              placeholder="Commission Rate"
              step="any"
              :disabled="!editable"
          />

          <FormulateInput
              type="select"
              name="handler_code"
              validationName="Business Handler"
              label="Business Handler *"
              validation="required"
              placeholder="Business Handler"
              :options="formLovs.businessHandlerOptions"
              :disabled="!editable"
          />
        </div>
        <div class="grid grid-cols-2 gap-x-10">
          <editor
              v-model="formValues.warranty"
              label="Warranty"
              placeholder="Warranty"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.warranty_kh"
              label="Warranty (Khmer)"
              placeholder="Warranty (Khmer)"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.memorandum"
              label="Memorandum"
              placeholder="Memorandum"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.memorandum_kh"
              label="Memorandum (Khmer)"
              placeholder="Memorandum (Khmer)"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.subjectivity"
              label="Subjectivity"
              placeholder="Subjectivity"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.subjectivity_kh"
              label="Subjectivity (Khmer)"
              placeholder="Subjectivity (Khmer)"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.remark"
              label="Remark"
              placeholder="Remark"
              :disabled="!editable"
          />
          <editor
              v-model="formValues.remark_kh"
              label="Remark (Khmer)"
              placeholder="Remark (Khmer)"
              :disabled="!editable"
          />
        </div>
        <div class="grid grid-cols-1">
          <div class="text-right my-4">
            <button
                type="button"
                class="btn btn-secondary w-24"
                @click="$router.push({ name: 'HSEndorsementIndex' })"
            >
              <span>Cancel</span>
            </button>
            <button
                v-if="editable"
                type="submit"
                :disabled="saving"
                class="btn btn-primary w-24"
            >
              <span v-if="!saving">Save</span>
              <span v-else>Saving...</span>
            </button>
            <button
                v-else
                class="btn btn-primary w-24"
                type="button"
                @click="nextTab"
            >
              <span>Next</span>
            </button>
          </div>
        </div>
      </FormulateForm>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import LoadingIndicator from "@/components/LoadingIndicator.vue";

export default {
  props: {
    id: [Number, String],
    dataId: [Number, String],
    endorsement: Object,
  },
  components: {
    LoadingIndicator,
  },
  data() {
    return {
      isLoading: false,
      initForm: {
        insured_name: "",
        insured_name_kh: "",
        geographical_limit: "",
        endorsement_clauses: [],
        general_exclusions: [],
        warranty: "",
        warranty_kh: "",
        memorandum: "",
        memorandum_kh: "",
        subjectivity: "",
        subjectivity_kh: "",
        remark: "",
        remark_kh: "",
      },
      formValues: {},
      formLovs: {
        geographicalLimits: [],
        endorsementClauses: [],
        generalExclusions: [],
        saleChannelOptions: [],
        businessHandlerOptions: [],
        businessChannelOptions: [],
      },

      errors: {},
      saving: false,
    };
  },
  computed: {
    editable() {
      return (
          this.endorsement.endorsement_type === "GENERAL" &&
          this.endorsement.status === "PND"
      );
    },
  },
  methods: {
    getDetail() {
      console.log('start getDetail:', 123);
      this.isLoading = true;
      axios
          .get("/hs/endorsements/get-detail/" + this.id)
          .then((res) => {
            let data = res.data;
            this.formValues = data.hs;
            Object.keys(this.formValues).forEach(
                (k) =>
                    (this.formValues[k] =
                        this.formValues[k] === null ? "" : this.formValues[k])
            );


          })
          .then(() => {})
          .catch((error) => {
              console.error("error getDetails:", error)
          })
          .finally(() => {
            this.isLoading = false;
          });

      console.log('end getDetail:', this.formValues);
    },
    getLovs() {
      this.isLoading = true
      axios.get("/hs/quotations/get-lovs").then((res) => {
        this.formLovs.geographicalLimits = res.data.geographical_limits;
        this.formLovs.endorsementClauses = res.data.endorsement_clauses;
        this.formLovs.generalExclusions = res.data.general_exclusions;
        this.formLovs.saleChannelOptions = res.data.sale_channels;
      }).finally(() => {
        this.getDetail();
        this.listBusinessHandlers();
        this.isLoading = false
      });
    },
    listSaleChannels() {
      axios
          .get("/business-channels-service/list-sale-channels")
          .then((response) => {
            this.formLovs.saleChannelOptions = response.data;
          });
    },
    listBusinessHandlers() {
      axios
          .get("/business-channels-service/list-business-handlers")
          .then((response) => {
            this.formLovs.businessHandlerOptions = response.data;
          });
    },
    changeBusinessCategory(e) {
      axios
          .get("/auto-service/list-business-channels-by-category/" + e)
          .then((response) => {
            this.formLovs.businessChannelOptions = response.data;
          });
    },
    changeBusinessChannel(e) {
      axios
          .get("/auto-service/find-business-channel/" + e.target.value)
          .then((response) => {
            this.formValues.commission_rate = response.data.commission_rate;
            this.formValues.handler_code = response.data.handler_code;
          });
    },


    nextTab() {
      document.querySelector('.nav-tabs a[href="#config"]').click();
    },
    handleSubmit() {
      alert(123)
      if (this.id) {
        this.saving = true;
        axios
            .patch("/hs/endorsements/" + this.id, this.formValues)
            .then((res) => {
              let data = res.data;
              this.saving = false;
              this.toastMessage(data.message, "Success");
            })
            .then(() => {
              this.getDetail();
              this.$emit('updateGeneralInfo');
            })
            .then(() => {
              document.querySelector('.nav-tabs a[href="#config"]').click();
            })
            .catch((error) => {
              let err_res = error.response.data;
              this.errors = err_res.errors;
              this.toastMessage(err_res.message, "Error");
            })
            .finally(() => {
              this.saving = false;
            });
      }
    },
    toastMessage(msg, type, position = "bottom") {
      notify(msg, type,'bottom-right');
    },
  },
  mounted() {
    this.formValues = JSON.parse(JSON.stringify(this.initForm));
    if (this.id) {
      this.getLovs();
    }
  },
};
</script>
<style lang="scss">
.formulate-input .p-multiselect {
  height: 2.7rem;
}
.formulate-input .p-multiselect-label {
  padding-top: 0.45rem !important;
}
</style>