<template>
    <div>
      <LoadingIndicator v-if="isLoading" />
      <div v-else class=" container mx-auto p-4">
        <form @submit.prevent="handleSubmit">
          <div class="grid grid-cols-2 gap-y-2 gap-x-6">
            <div>
              <label for="insured_name" class="block mb-1 font-bold">Insured Name *</label>
              <Textarea rows="5" class="w-full" :disabled="!editable" v-model="formValues.insured_name"
                  placeholder="Insured Name"
              />
              <div class="h-6">
                <small v-if="errors.insured_name" class="p-error block mt-1">{{ errors.insured_name[0] }}</small>
              </div>
            </div>
            <div>
              <label for="insured_name_kh" class="block mb-1 font-bold">Insured Name (Khmer) *</label>
              <Textarea rows="5" class="w-full" :disabled="!editable" v-model="formValues.insured_name_kh"
                  placeholder="Insured Name (Khmer)"
              />
              <div class="h-6">
                <small v-if="errors.insured_name_kh" class="p-error block mt-1">{{ errors.insured_name_kh[0] }}</small>
              </div>
            </div>
            <div>
              <label for="" class="font-bold block mb-1">Geographical Limit *</label>
              <Dropdown :disabled="!editable" class="w-full p-inputtext" v-model="formValues.geographical_limit"
                        :options="formLovs.geographicalLimits"
                        placeholder="Geographical Limit" optionLabel="label"
                        optionValue="value" :filter="true">
              </Dropdown>
            </div>
            <div>
              <label class="-label" for="endorsement_clauses"
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
            </div>
            <div>
              <label class="font-bold block mb-1" for="">General Exclusion *</label>
              <MultiSelect :disabled="!editable" v-model="formValues.general_exclusions" class="w-full p-inputtext" display="chip"
                           optionLabel="label" optionValue="value" :options="formLovs.generalExclusions" :filter="true"
                           :showClear="true" />
            </div>
            <div>
              
            </div>
            <div>
              <label class="font-bold block mb-1" for="">Business Channel *</label>
              <Dropdown :disabled="!editable" v-model="formValues.sale_channel" class="w-full p-inputtext" optionLabel="label"
                        optionValue="value" :options="formLovs.saleChannelOptions" :filter="true" :showClear="true"
                        />
            </div>
            <div>
              <label class="font-bold block mb-1" for="">Business Name *</label>
              <Dropdown :disabled="!editable" v-model="formValues.business_code" class="w-full p-inputtext" optionLabel="label"
                        optionValue="value" :options="formLovs.businessChannelOptions" :filter="true" :showClear="true"
                      />
              <ul v-if="errors.business_code" class="formulate-input-errors">
                <li class="formulate-input-error">{{ errors.business_code[0] }}</li>
              </ul>
            </div>
            <div>
              <label for="" class="font-bold block mb-1">Commission Rate %</label>
              <InputNumber :disabled="!editable" v-model="formValues.commission_rate" class="w-full"
                           placeholder="Commission Rate" :min="0" :max="100" :maxFractionDigits="5" />
            </div>
            <div >
              <label for="" class="font-bold block mb-1">Business Handler *</label>
              <Dropdown class="w-full p-inputtext" v-model="formValues.handler_code"
                        :options="formLovs.businessHandlerOptions"
                        placeholder="Business Handler" optionLabel="label"
                        optionValue="value" :filter="true">
              </Dropdown>
            </div>

<!--              <div>-->
<!--                <label for="insured_person_note" class="block mb-1 font-bold">Insured Person Note</label>-->
<!--                <CKEditor :disabled="!editable"-->
<!--                    v-model="formValues.insured_person_note"-->
<!--                    placeholder="Insured Person Note"-->
<!--                />-->
<!--              </div>-->

            <!-- Warranty -->
            <div>
              <label for="warranty" class="block mb-1 font-bold">Warranty</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.warranty"
                  placeholder="Warranty"
              />
            </div>

            <!-- Warranty (Khmer) -->
            <div>
              <label for="warranty_kh" class="block mb-1 font-bold">Warranty (Khmer)</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.warranty_kh"
                  placeholder="Warranty (Khmer)"
              />
            </div>

            <!-- Memorandum -->
            <div>
              <label for="memorandum" class="block mb-1 font-bold">Memorandum</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.memorandum"
                  placeholder="Memorandum"
              />
            </div>

            <!-- Memorandum (Khmer) -->
            <div>
              <label for="memorandum_kh" class="block mb-1 font-bold">Memorandum (Khmer)</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.memorandum_kh"
                  placeholder="Memorandum (Khmer)"
              />
            </div>

            <!-- Subjectivity -->
            <div>
              <label for="subjectivity" class="block mb-1 font-bold">Subjectivity</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.subjectivity"
                  placeholder="Subjectivity"
              />
            </div>

            <!-- Subjectivity (Khmer) -->
            <div>
              <label for="subjectivity_kh" class="block mb-1 font-bold">Subjectivity (Khmer)</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.subjectivity_kh"
                  placeholder="Subjectivity (Khmer)"
              />
            </div>

            <!-- Remark -->
            <div>
              <label for="remark" class="block mb-1 font-bold">Remark</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.remark"
                  placeholder="Remark"
              />
            </div>

            <!-- Remark (Khmer) -->
            <div>
              <label for="remark_kh" class="block mb-1 font-bold">Remark (Khmer)</label>
              <CKEditor :disabled="!editable"
                  v-model="formValues.remark_kh"
                  placeholder="Remark (Khmer)"
              />
            </div>
          </div>
          <div class="grid grid-cols-1">
            <div class="text-right my-4">
              <button
                  type="button"
                  class="btn btn-secondary w-24"
                  @click="$router.push({ name: 'TravelEndorsementIndex' })"
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
        </form>
      </div>
    </div>
  </template>
  <script>
  import axios from "axios";
  import LoadingIndicator from "@/components/LoadingIndicator.vue";
  import CKEditor from "@/components/Form/CKEditor.vue";
  
  export default {
    props: {
      id: [Number, String],
      dataId: [Number, String],
      endorsement: Object,
    },
    components: {
      CKEditor,
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
        // console.log('start getDetail:', 123);
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

            // console.log('state 1 getDetail:', this.formValues);
          })
          .then(() => {})
            .catch((error) => {
              console.error("error getDetails:", error)
            })
            .finally(() => {
              this.isLoading = false;
            });

        // console.log('end getDetail:', this.formValues);
      },
      getLovs() {
        this.isLoading = true
        axios.get("/hs/quotations/get-lovs").then((res) => {
          this.formLovs.geographicalLimits = res.data.geographical_limits;
          this.formLovs.endorsementClauses = res.data.endorsement_clauses;
          this.formLovs.generalExclusions = res.data.general_exclusions;
          this.formLovs.saleChannelOptions = res.data.sale_channels;

          console.log('logs form lov:', this.formLovs)
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
        this.$notify(
          {
            group: position,
            title: type,
            text: msg,
          },
          4000
        );
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