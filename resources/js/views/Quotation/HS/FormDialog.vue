<template>
  <template>
    <div>
      <Toast />
      <Dialog class="w-9/12 " position="top" :visible="isVisible" :modal="true" :closable="false" :header="header"
              @show="assignDefaultValues">
        <!-- File and Joint Status Row -->
        <div class="grid grid-cols-2 gap-x-10 mb-4">
          <div class="col-span-1">
            <label for="" class="form-label">File *</label>
            <FileUploader v-model="formValues.file" @change="uploadFile" :loading="requireSpinning" accept=".xlsx">
            </FileUploader>
            <div class="vehicle-upload-msg break-words"></div>
            <small v-if="errors.file" class="p-error block">{{ errors.file[0] }}</small>
          </div>
          <div class="col-span-1">
            <label class="form-label" for="">Joint Status *</label>
            <Dropdown v-model="formValues.joint_status" class="w-full" optionLabel="label"
                      optionValue="value" :options="formLovs.jointStatuses" @change="changeJointStatus" />
            <span class="p-error block" v-if="errors.joint_status">
              {{ errors.joint_status[0] }}
            </span>
          </div>
        </div>

        <!-- Joint Details Section (Conditional) -->
        <div class="col-span-2 mb-4" v-if="formValues.joint_status === 'J'">
          <JointDetail v-model="formValues.joint_details" :customerTypeOpts="formLovs.customerTypes"
            :jointLevelOpts="formLovs.jointLevels" :permissionOpts="formLovs.permissions"
            @change="detectInsuredName" />
        </div>
        <!-- Insured Name Row -->
        <div class="grid grid-cols-2 gap-x-10 mb-4">
          <div>
            <label class="form-label" for="insured-name">Insured Name *</label>
            <AutoComplete
                id="insured-name"
                v-model="formValues.insured_name"
                :suggestions="formLovs.insuredPersonList"
                @item-select="selectInsured"
                @complete="searchInsured"
                @dropdown-click="searchInsured"
                class="w-full"
                :virtualScrollerOptions="{ itemSize: 38 }"
                placeholder="Search insured name"
                dropdown
                :showClear="true"
                forceSelection
                optionLabel="name"
                optionValue="name"
            />
            <span class="p-error block" v-if="errors.insured_name">
              {{ errors.insured_name[0] }}
            </span>
          </div>

          <div>
            <label for="insured-name" class="form-label">Insured Name (Khmer) *</label>
            <InputText
                id="insured-name"
                v-model="insuredNameKh"
                class="w-full"
                placeholder="Insured Name (Khmer)"
            />
            <span class="p-error block" v-if="errors.insured_name_kh">
              {{ errors.insured_name_kh[0] }}
            </span>
          </div>
        </div>

        <!-- Endorsement and General Exclusion Row -->
        <div class="grid grid-cols-2 gap-x-10 mb-4">
          <div class="col-span-1">
            <label class="form-label" for="">Endorsement Clause *</label>
            <MultiSelect v-model="formValues.endorsement_clauses" class="w-full" display="chip"
                         optionLabel="label" optionValue="value" :options="formLovs.endorsementClauses" :filter="true"
                         :showClear="true" />
            <span class="p-error block" v-if="errors.endorsement_clauses">
              {{ errors.endorsement_clauses[0] }}
            </span>
          </div>

          <div class="col-span-1">
            <label class="form-label" for="">General Exclusion *</label>
            <MultiSelect v-model="formValues.general_exclusions" class="w-full" display="chip"
                         optionLabel="label" optionValue="value" :options="formLovs.generalExclusions" :filter="true"
                         :showClear="true" />
            <span class="p-error block" v-if="errors.general_exclusions">
              {{ errors.general_exclusions[0] }}
            </span>
          </div>
        </div>

        <!-- Business Channel and Name Row -->
        <div class="grid grid-cols-2 gap-x-10 mb-4">
          <div class="col-span-1">
            <label class="form-label" for="">Business Channel *</label>
            <Dropdown v-model="formValues.sale_channel" class="w-full" optionLabel="label"
                      optionValue="value" :options="formLovs.saleChannels" :filter="true" :showClear="true"
                      @change="listBusinessNames" />
            <span class="p-error block" v-if="errors.sale_channel">
              {{ errors.sale_channel[0] }}
            </span>
          </div>

          <div class="col-span-1">
            <label class="form-label" for="">Business Name *</label>
            <Dropdown v-model="formValues.business_code" class="w-full" optionLabel="label"
                      optionValue="value" :options="formLovs.businessNames" :filter="true" :showClear="true"
                      @change="changeBusinessChannel" />
            <span class="p-error block" v-if="errors.business_code">
              {{ errors.business_code[0] }}
            </span>
          </div>
        </div>

        <!-- Commission Rate and Business Handler Row -->
        <div class="grid grid-cols-2 gap-x-10 mb-4">
          <div class="col-span-1">
            <label for="" class="form-label">Commission Rate %</label>
            <InputNumber v-model="formValues.commission_rate" class="w-full"
                         placeholder="Commission Rate" :min="0" :max="100" :maxFractionDigits="5" />
          </div>

          <div class="col-span-1">
            <label for="" class="form-label">Business Handler *</label>
            <Dropdown class="w-full" v-model="formValues.handler_code"
                      :options="businessHandlerOptions"
                      placeholder="Business Handler" optionLabel="label"
                      optionValue="value" :filter="true">
            </Dropdown>
            <span class="p-error block" v-if="errors.handler_code">
              {{ errors.handler_code[0] }}
            </span>
          </div>
        </div>

        <!-- Geographical Limit and Policy Wording Version Row -->
        <div class="grid grid-cols-2 gap-x-10 mb-4">
          <div class="col-span-1">
            <label for="" class="form-label">Geographical Limit *</label>
            <Dropdown class="w-full" v-model="formValues.geographical_limit"
                      :options="formLovs.geographicalLimits"
                      placeholder="Geographical Limit" optionLabel="label"
                      optionValue="value" :filter="true">
            </Dropdown>

            <span class="p-error block" v-if="errors.geographical_limit">
              {{ errors.geographical_limit[0] }}
            </span>
          </div>

          <div class="col-span-1">
            <label for="" class="form-label">Policy Wording Version *</label>
            <Dropdown class="w-full" v-model="formValues.policy_wording_version"
                      :options="policyWordingVersionOptions"
                      placeholder="Select policy wording version" optionLabel="label"
                      optionValue="value" :filter="true">
            </Dropdown>
            <span class="p-error block" v-if="errors.policy_wording_version">
              {{ errors.policy_wording_version[0] }}
            </span>
          </div>
        </div>
        <!--      abc-->
        <div class="clear-fix"></div>

        <div class="grid grid-cols-12 gap-x-10 gap-y-4">
          <!-- Insured Person Note -->
          <div class="col-span-12">
            <label for="insured_person_note" class="form-label">Insured Person Note</label>
            <CKEditor
                v-model="formValues.insured_person_note"
                placeholder="Insured Person Note"
            />
          </div>

          <!-- Insured Person Note (Khmer) -->
          <div class="col-span-12">
            <label for="insured_person_note_kh" class="form-label">Insured Person Note (Khmer)</label>
            <CKEditor
                v-model="formValues.insured_person_note_kh"
                placeholder="Insured Person Note (Khmer)"
            />
          </div>

          <!-- Warranty -->
          <div class="col-span-12">
            <label for="warranty" class="form-label">Warranty</label>
            <CKEditor
                v-model="formValues.warranty"
                placeholder="Warranty"
            />
          </div>

          <!-- Warranty (Khmer) -->
          <div class="col-span-12">
            <label for="warranty_kh" class="form-label">Warranty (Khmer)</label>
            <CKEditor
                v-model="formValues.warranty_kh"
                placeholder="Warranty (Khmer)"
            />
          </div>

          <!-- Memorandum -->
          <div class="col-span-12">
            <label for="memorandum" class="form-label">Memorandum</label>
            <CKEditor
                v-model="formValues.memorandum"
                placeholder="Memorandum"
            />
          </div>

          <!-- Memorandum (Khmer) -->
          <div class="col-span-12">
            <label for="memorandum_kh" class="form-label">Memorandum (Khmer)</label>
            <CKEditor
                v-model="formValues.memorandum_kh"
                placeholder="Memorandum (Khmer)"
            />
          </div>

          <!-- Subjectivity -->
          <div class="col-span-12">
            <label for="subjectivity" class="form-label">Subjectivity</label>
            <CKEditor
                v-model="formValues.subjectivity"
                placeholder="Subjectivity"
            />
          </div>

          <!-- Subjectivity (Khmer) -->
          <div class="col-span-12">
            <label for="subjectivity_kh" class="form-label">Subjectivity (Khmer)</label>
            <CKEditor
                v-model="formValues.subjectivity_kh"
                placeholder="Subjectivity (Khmer)"
            />
          </div>

          <!-- Remark -->
          <div class="col-span-12">
            <label for="remark" class="form-label">Remark</label>
            <CKEditor
                v-model="formValues.remark"
                placeholder="Remark"
            />
          </div>

          <!-- Remark (Khmer) -->
          <div class="col-span-12">
            <label for="remark_kh" class="form-label">Remark (Khmer)</label>
            <CKEditor
                v-model="formValues.remark_kh"
                placeholder="Remark (Khmer)"
            />
          </div>
        </div>

        <template #footer>
          <div class="w-full flex justify-end pt-4 border-t">
            <Button
                label="Cancel"
                class="p-button-secondary mr-1"
                :disabled="isSaving"
                @click="hideDialog"
            />
            <Button
                :loading="isSaving"
                icon="pi pi-save"
                @click="handleSave"
                class="bg-blue-700 bg-opacity-90 text-white px-3"
                label="Save"
            >
            </Button>
          </div>
        </template>
      </Dialog>
    </div>
  </template>
</template>

<script>
import AutoComplete from 'primevue/autocomplete';
import JoinDetailFields from './Partials/JoinDetailFields.vue'
import JointDetail from './Partials/JointDetail.vue';
import quotationService from '@/services/hs/quotation.service'
import RichTextEditor from "@/views/Quotation/HS/Components/RichTextEditor.vue";
import CKEditor from '../../../components/Form/CKEditor.vue';
import FileUploader from "@/components/Form/FileUploader.vue";
export default {
  props: {
    header: String,
    isVisible: Boolean,
  },
  components: {
    JoinDetailFields,
    RichTextEditor,
    CKEditor,
    AutoComplete,
    FileUploader,
    JointDetail
  },
  data() {
    return {
      filteredInsuredPersons: [],
      initFormValues: {
        file: null,
        joint_status: 'S',
        joint_details: [
          {
            customer_type: null,
            customer_no: null,
            joint_level: 'PRIMARY',
            permission: 'FULL'
          }
        ],
        endorsement_clauses: [],
        general_exclusions: [],
        geographical_limit: null,
        sale_channel: null,
        business_code: null,
        warranty: '',
        memorandum: '',
        subjectivity: '',
        remark: '',
        insured_person_note: '',
        insured_person_note_kh: '',
        commission_rate: null,
        handler_code: null
      },
      insuredNameKh: '',
      formValues: {},
      policyWordingVersionOptions: {},
      formLovs: {
        jointStatuses: [],
        saleChannels: [],
        businessNames: [],
        customerTypes: [],
        jointLevels: [],
        permissions: [],
        defaultEndorsementClauses: [],
        endorsementClauses: [],
        defaultGeneralExclusions: [],
        generalExclusions: [],
        geographicalLimits: [],
        insuredPersonList: []
      },
      businessHandlerOptions: {},
      errors: [],
      isSaving: false,
    }
  },
  watch: {
    'formValues.insured_name_kh'(newValue) {
      console.log(newValue)
    }
  },
  computed: {
    requireSpinning() {
      return this.isUploadInProgress
    }
  },

  methods: {
    detectInsuredName (value) {
    if (this.formValues.joint_status === 'J') {
        const nameEn = this.formValues.joint_details.filter(item => item.name_en).map(item => item.name_en)
        const nameKh = this.formValues.joint_details.filter(item => item.name_kh).map(item => item.name_kh)
        this.formValues.insured_name = nameEn.join(', ')
        this.formValues.insured_name_kh = nameKh.join(', ')
    }
},
    handleMessage(msg, type, position = 'bottom-right') {
      notify(msg,type === 'success' ? 'success' : 'error')
    },
    hideDialog() {
      this.formValues = JSON.parse(JSON.stringify(this.initFormValues))
      this.errors = []

      this.$emit('hideDialog')
    },
    changeValue(e) {
    },
    inputValue(e) {
      this.formValues.insured_name_kh = e
    },
    getLovs() {
      quotationService.getLovs().then(res => {
        this.formLovs.saleChannels = res.data.sale_channels
        this.formLovs.jointStatuses = res.data.joint_statuses
        this.formLovs.customerTypes = res.data.customer_types
        this.formLovs.jointLevels = res.data.joint_levels
        this.formLovs.permissions = res.data.permissions
        this.formLovs.defaultEndorsementClauses = res.data.default_endorsement_clauses
        this.formLovs.endorsementClauses = res.data.endorsement_clauses
        this.formLovs.defaultGeneralExclusions = res.data.default_general_exclusions
        this.formLovs.generalExclusions = res.data.general_exclusions
        this.formLovs.geographicalLimits = res.data.geographical_limits
      })
    },
    listBusinessNames({value}) {
      quotationService.getBusinessChannelsLov(value).then(res => {
        this.formLovs.businessNames = res.data
      })
    },
    async handleSave() {
      if (this.isSaving) {
        return;
      }

      try {
        this.isSaving = true;
        this.errors = {};

        const response = await quotationService.create(this.getPreparedFormData());

        if (response?.data) {

          notify('Quote is created successfully', 'success')
          this.resetForm();
          this.$emit('hideDialog', true);
        } else {
          throw new Error('Invalid response from server');
        }

      } catch (error) {
        if (error.response) {
          switch (error.response.status) {
            case 422:
              this.errors = error.response.data.errors || {};
              notify('Please fill in all required fields', 'error','bottom-right');
              break;
            case 403:
              notify('Maker can not approve their own records.', 'error','bottom-right');
              break;
            case 413:
              notify('File size too large', 'error','bottom-right');
              break;
            default:
              notify('Error', 'error','bottom-right');
          }
        }

        console.error('Save error:', error);
      } finally {
        this.isSaving = false;
      }
    },

    resetForm() {
      // Deep clone the initial form values to avoid reference issues
      this.formValues = JSON.parse(JSON.stringify(this.initFormValues));
      this.errors = {};
      this.insuredNameKh = '';

      // Reset file upload if needed
      if (this.$refs.fileUpload) {
        this.$refs.fileUpload.clear();
      }
    },
    async uploadFile(file) {

      if (!file ) {
        return false;
      }

      this.formValues.file = {
        fileList: [file]
      };

      await quotationService.getProductCodeByUploadExcel(this.getPreparedFormData())
          .then(response => {
            console.log('Upload successful');

            let data = response.data
            this.formLovs.insuredPersonList = data.insured_persons
            this.filteredInsuredPersons = this.formLovs.insuredPersonList
            this.formValues.insured_name = data.selected_insured.insured_name
            this.formValues.insured_name_kh = data.selected_insured.insured_name_kh
            this.insuredNameKh = this.formValues.insured_name_kh
            this.listPolicyWordingVersions(data.product_code);
          })
          .catch((error) => {
            console.log(error);
            notify('Error', 'error','bottom-right');
          })
    },
    getPreparedFormData() {
      let formData = new FormData()
      Object.keys(this.formValues).forEach(item => {
        if (item === 'joint_details') {
          this.formValues[item].forEach((i, index) => {
            Object.keys(i).forEach(ii => {

              formData.append(`joint_details[${index}][${ii}]`, i[ii] ?? '')
            })
          })
        } else if ((item === 'endorsement_clauses' || item === 'general_exclusions') && this.formValues[item].length > 0) {
          this.formValues[item].forEach(i => {
            formData.append(`${item}[]`, i)
          })
        } else if (item === 'file') {
          formData.append(item, this.formValues[item]?.fileList[0] ?? null)
        } else
          formData.append(item, this.formValues[item] ?? '')
      })
      formData.delete('insured_name_kh');
      formData.append('insured_name_kh', this.insuredNameKh);
      console.log(formData);
      return formData
    },
    assignDefaultValues() {
      this.formValues.endorsement_clauses = this.formLovs.defaultEndorsementClauses
      this.formValues.general_exclusions = this.formLovs.defaultGeneralExclusions
    },
    listPolicyWordingVersions(productCode) {
      axios
          .get(`/auto-service/list-policy-wording-version-by-product-code/${productCode}`)
          .then((response) => {
            this.policyWordingVersionOptions = response.data;
          });
    },
    removeFile() {
      this.formValues.policy_wording_version = null;
      this.formValues.insured_name = null;
      this.formValues.insured_name_kh = null;
      this.formLovs.insuredPersonList = [];
      this.policyWordingVersionOptions = {};
    },
    changeBusinessChannel(e) {
      axios.get('/hs/quotations/find-business-channel/' + e.value).then(response => {
        if (response.data) {
          this.formValues.commission_rate = response.data.commission_rate
          this.formValues.handler_code = response.data.handler_code
        }
      })
    },
    listBusinessHandlers() {
      axios.get('/business-channels-service/list-business-handlers').then(response => {
        this.businessHandlerOptions = response.data
      })
    },
    // searchInsured() {
    //   this.formLovs.insuredPersonList = [...this.formLovs.insuredPersonList]
    // },
    // selectInsured(event) {
    //   console.log(event)
    // }

    searchInsured(event) {
      // const query = event.query?.toLowerCase();
      // this.filteredInsuredPersons = this.formLovs.insuredPersonList.filter(
      //     person => person?.name.toLowerCase().includes(query)
      // );
    },
    selectInsured(event) {
      // Update both English and Khmer names when selecting
      this.formValues.insured_name = event.value.name;
      this.insuredNameKh = event.value.nameKh || '';
    },

    changeJointStatus({ value }) {
      // Create default joint detail structure
      const defaultJointDetail = {
        customer_type: null,
        customer_no: null,
        joint_level: 'PRIMARY',
        permission: 'FULL'
      };

      try {
        // Ensure formValues exists
        if (!this.formValues) {
          this.formValues = {};
        }

        // Initialize joint_details if it doesn't exist
        if (!this.formValues.joint_details) {
          this.formValues.joint_details = [];
        }

        if (value === 'S') {
          // For Single status, reset to one default joint detail
          this.formValues.joint_details = [{ ...defaultJointDetail }];
        } else if (value === 'J') {
          // For Joint status
          if (this.formValues.joint_details.length === 0) {
            // If no joint details exist, add one default entry
            this.formValues.joint_details = [{ ...defaultJointDetail }];
          } else {
            // Ensure all existing entries have required fields
            this.formValues.joint_details = this.formValues.joint_details.map(detail => ({
              ...defaultJointDetail,
              ...detail
            }));
          }
        }

        // Update joint status
        this.formValues.joint_status = value;

      } catch (error) {
        notify('Error', 'error','bottom-right');
        console.error('Error in changeJointStatus:', error);
        // Fallback to safe default state
        this.formValues.joint_details = [{ ...defaultJointDetail }];
        this.formValues.joint_status = 'S';
      }
    },

    addJointDetail() {
      const defaultJointDetail = {
        customer_type: null,
        customer_no: null,
        joint_level: 'PRIMARY',
        permission: 'FULL'
      };

      // Ensure joint_details exists before pushing
      if (!Array.isArray(this.formValues.joint_details)) {
        this.formValues.joint_details = [];
      }

      this.formValues.joint_details.push({ ...defaultJointDetail });
    },

    removeJointDetail(index) {
      if (index > 0 && Array.isArray(this.formValues.joint_details)) {
        this.formValues.joint_details.splice(index, 1);
      }
    },

    // Method to validate joint details structure
    validateJointDetails() {
      const defaultJointDetail = {
        customer_type: null,
        customer_no: null,
        joint_level: 'PRIMARY',
        permission: 'FULL'
      };

      if (!Array.isArray(this.formValues.joint_details)) {
        this.formValues.joint_details = [{ ...defaultJointDetail }];
        return;
      }

      // Ensure at least one joint detail exists
      if (this.formValues.joint_details.length === 0) {
        this.formValues.joint_details = [{ ...defaultJointDetail }];
      }
    },

  },
  mounted() {
    this.formValues = JSON.parse(JSON.stringify(this.initFormValues))
    this.validateJointDetails();
    this.getLovs()
    this.listBusinessHandlers()
  },
}
</script>

<style scoped>
.space-y-4 > * + * {
  margin-top: 1rem;
}
</style>

<style>
/* Add these styles to your component or global CSS */
.p-error {
  color: #ef4444 !important; /* PrimeVue error red */
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.p-input.p-invalid,
.p-inputtext.p-invalid {
  border-color: #ef4444 !important;
}

/* Style for error text under inputs */
.s {
  list-style: none;
  padding: 0;
  margin-top: 0.25rem;
}

/* Additional styling for invalid state */
.p-invalid:enabled:focus {
  outline: 0 none;
  outline-offset: 0;
  box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px rgba(239, 68, 68, 0.4);
  border-color: #ef4444;
}

/* Style for required field indicator */
.required-field::after {
  content: " *";
  color: #ef4444;
}
</style>