<template>
  <div>
    <div class="intro-y flex items-center mt-8">
      <h2 class="text-lg font-medium mr-auto">
        <span v-if="id">Edit</span>
        <span v-else>Create</span>
        Customer
      </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
      <div>
        <div class="grid grid-cols-3 gap-x-10">
          <div class="mb-5">
            <label class="mb-1 block font-bold">Customer Type</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.customer_type"
              :options="Object.entries(customerTypes).map(([value, label]) => ({value, label}))"
              optionLabel="label"
              optionValue="value"
              placeholder="Select Customer Type"
              @change="changeForm"
            />
          </div>
        </div>

        <h3 class="w-full bg-blue-50 py-2 mb-2">
          {{ formValues.customer_type === 'IC' ? 'INDIVIDUAL CUSTOMER' : 'CORPORATE CUSTOMER' }}
        </h3>

        <div class="grid grid-cols-3 gap-x-10">

          <div class="mb-5">
            <label class="mb-1 block font-bold">
              {{ formValues.customer_type === 'IC' ? 'Individual Name in Khmer' : 'Company Name Khmer' }}
            </label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.name_kh"
              :placeholder="formValues.customer_type === 'IC' ? 'Individual Name in Khmer' : 'Company Name Khmer'"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">
              {{ formValues.customer_type === 'IC' ? 'Individual Name in LATIN *' : 'Company Name Latin *' }}
            </label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.name_en"
              :placeholder="formValues.customer_type === 'IC' ? 'Individual Name in LATIN' : 'Company Name Latin'"
            />
            <span class="text-error" v-if="!formValues.name_en && submitted">
              {{ formValues.customer_type === 'IC' ? 'Individual Name in LATIN is required.' : 'Company Name Latin is required.' }}
            </span>
          </div>

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">
              {{ formValues.customer_type === 'CL' ? 'Tin Code *' : 'Foreign TIN Number *' }}
            </label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.tin_code"
              :placeholder="formValues.customer_type === 'CL' ? 'TIN Code' : 'Foreign TIN Number'"
            />
            <span class="text-error" v-if="formValues.customer_type === 'CL' && !formValues.tin_code && submitted">
              {{ formValues.customer_type === 'CL' ? 'TIN Code is required.' : 'Foreign TIN Number is required.' }}
            </span>
          </div>

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">Incorporate Date</label>
            <Calendar 
              type="date" 
              showIcon 
              v-model="formValues.incorporate_date" 
              dateFormat="mm/dd/yy" 
              placeholder="mm/dd/yy"
            />
          </div>

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">Business/Occupation *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.cust_classification"
              :options="Object.entries(customerCorp).map(([value, label]) => ({value, label}))"
              optionLabel="label"
              optionValue="value"
              placeholder="Select Business/Occupation"
            />
            <span class="text-error" v-if="!formValues.cust_classification && submitted">Business/Occupation is required.</span>
          </div>

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">Business Registration No.</label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.business_registration_no"
              placeholder="Business Registration No."
            />
          </div>

          <div class="mb-5" v-if="formValues.customer_type === 'IC'">
            <label class="mb-1 block font-bold">Business/Occupation *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.cust_classification"
              :options="Object.entries(customerIndis).map(([value, label]) => ({value, label}))"
              optionLabel="label"
              optionValue="value"
              placeholder="Select Business/Occupation"
            />
            <span class="text-error" v-if="!formValues.cust_classification && submitted">Business/Occupation is required.</span>
          </div>
        </div>

        

        <div class="grid grid-cols-3 gap-x-10" v-if="formValues.customer_type === 'IC'">
          <div class="mb-5">
            <label class="mb-1 block font-bold">Gender *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.gender"
              :options="Object.entries(genders).map(([value, label]) => ({value, label}))"
              optionLabel="label"
              optionValue="value"
              placeholder="Select Gender"
            />
            <span class="text-error" v-if="!formValues.gender && submitted">Gender is required.</span>
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Date of Birth *</label>
            <Calendar 
              type="date" 
              showIcon 
              v-model="formValues.date_of_birth" 
              dateFormat="mm/dd/yy" 
              placeholder="mm/dd/yy"
            />
            <span class="text-error" v-if="!formValues.date_of_birth && submitted">Date of Birth is required.</span>
          </div>
        </div>

        <h3 class="w-full bg-blue-50 py-2 mb-2" v-if="formValues.customer_type === 'IC'">IDENTITY</h3>

        <div class="grid grid-cols-3 gap-x-10" v-if="formValues.customer_type === 'IC'">
          <div class="mb-5">
            <label class="mb-1 block font-bold">Identity Type *</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.identity_type"
              :options="Object.entries(identityTypes).map(([value, label]) => ({value, label}))"
              optionLabel="label"
              optionValue="value"
              placeholder="Select Identity Type"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Identity No. *</label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.identity_no"
              placeholder="Identity No."
            />
            <span class="text-error" v-if="!formValues.identity_no && submitted">Identity No. is required.</span>
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">National *</label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.national"
              placeholder="National"
            />
            <span class="text-error" v-if="!formValues.national && submitted">National is required.</span>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-x-10" v-if="formValues.customer_type === 'IC'">
          <div class="mb-5">
            <label class="mb-1 block font-bold">Nationality *</label>
            <InputText
              class="w-full p-inputtext-sm"
              v-model="formValues.nationality"
              placeholder="Nationality"
            />
            <span class="text-error" v-if="!formValues.nationality && submitted">Nationality is required.</span>
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Identity Issue Date</label>
            <Calendar 
              type="date" 
              showIcon 
              v-model="formValues.identity_iss_date" 
              dateFormat="mm/dd/yy" 
              placeholder="mm/dd/yy"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Identity Expire Date</label>
            <Calendar 
              type="date" 
              showIcon 
              v-model="formValues.identity_exp_date" 
              dateFormat="mm/dd/yy" 
              placeholder="mm/dd/yy"
            />
          </div>
        </div>

        <div class="grid grid-cols-3 gap-x-10">

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">
              {{ formValues.customer_type === "CL" ? "Country" : "Country *" }}
            </label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.country_code"
              :options="Object.entries(countryOptions).map(([key, item]) => ({key, item}))"
              optionLabel="item.label"
              optionValue="item.value"
              placeholder="Select Country"
            />
            <span class="text-error" v-if="!formValues.country_code && submitted && formValues.customer_type === 'CA'">Country is required.</span>
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">City/Province</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.province"
              :options="Object.entries(provinceOptions).map(([key, item]) => ({key, item}))"
              optionLabel="item.label"
              optionValue="item.value"
              placeholder="Select Province"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">District</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.district"
              :options="Object.entries(districtOptions.filter(obj => formValues.province === obj.province)).map(([key, item]) => ({key, item}))"
              optionLabel="item.district"
              optionValue="item.district"
              placeholder="Select District"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Commune</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.commune"
              :options="Object.entries(communeOptons.filter(obj => formValues.district === obj.district)).map(([key, item]) => ({key, item}))"
              optionLabel="item.commune"
              optionValue="item.commune"
              placeholder="Select Commune"
            />
          </div>

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">Village</label>
            <Textarea 
              v-model="formValues.village_en" 
              placeholder="Village" 
              rows="2" 
              cols="64"
            />
          </div>

          <div class="mb-5" v-if="formValues.customer_type !== 'IC'">
            <label class="mb-1 block font-bold">Address</label>
            <Textarea 
              v-model="formValues.address_en" 
              placeholder="Address" 
              rows="2" 
              cols="64"
            />
          </div>
        </div>

        <div class="grid grid-cols-3 gap-x-10" v-if="formValues.customer_type === 'IC'">
          <div class="mb-5">
            <label class="mb-1 block font-bold">Village</label>
            <Textarea 
              v-model="formValues.village_en" 
              placeholder="Village" 
              rows="2" 
              cols="64"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Address</label>
            <Textarea 
              v-model="formValues.address_en" 
              placeholder="Address" 
              rows="2" 
              cols="64"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Country</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.country_code"
              :options="Object.entries(countryOptions).map(([key, item]) => ({key, item}))"
              optionLabel="item.label"
              optionValue="item.value"
              placeholder="Select Country"
            />
          </div>
        </div>

        <h3 class="w-full bg-blue-50 py-2 mb-2">CONTACT INFORMATION</h3>

        <div class="border rounded-md mb-4">
          <div class="flex content-between items-center p-4" v-for="(group, key) in formValues.contactgroup" :key="key">
            <div class="grid grid-cols-3 gap-x-10 grow">
              <div class="mb-5">
                <label for="`contact-level-${group.label}`" class="mb-1 block font-bold">Contact Level</label>
                <Dropdown
                  :id="`contact-level-${key}`"
                  class="w-full p-inputtext-sm"
                  v-model="formValues.contactgroup[key].contact_level"
                  :options="Object.entries(contactlvl).map(([value, label]) => ({value, label}))"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Select Contact Level"
                />
              </div>

              <div class="mb-5">
                <label :for="`contact-type-${group.label}`" class="mb-1 block font-bold">Contact Type</label>
                <Dropdown
                  :id="`contact-type-${key}`"
                  class="w-full p-inputtext-sm"
                  v-model="formValues.contactgroup[key].contact_type"
                  :options="Object.entries(contacttype).map(([value, label]) => ({value, label}))"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Select Contact Type"
                />
              </div>

              <div class="mb-5">
                <label :for="`contact-info-${group.label}`" class="mb-1 block font-bold">Contact Info *</label>
                <InputText
                  :id="`contact-info-${key}`"
                  class="w-full p-inputtext-sm"
                  v-model="formValues.contactgroup[key].contact_info"
                  placeholder="Contact Info"
                />
                <span class="text-error" v-if="!formValues.contactgroup[key].contact_info && submitted">Contact Info is required.</span>
              </div>
            </div>
            <Button icon="pi pi-minus-circle" text rounded aria-label="Filter" @click="removeContactInformatinRow(key)" />
          </div>
          <div class="border-t-2 p-4">
            <Button label="Add Row" icon="pi pi-plus" class="text-indigo-600 border border-indigo-600 rounded-md p-1" @click="addNewContactInformatinRow" />
          </div>
        </div>

        <h3 v-if="formValues.customer_type != 'IC'" class="w-full bg-blue-50 py-2 mb-2">ADDITIONAL INFORMATION</h3>

        <div class="grid grid-cols-3 gap-x-10">
          <div class="mb-5">
            <label class="mb-1 block font-bold">Distribution Channel</label>
            <InputText 
              type="number"
              v-model="formValues.broker_id" 
              placeholder="Distribution Channel"
            />
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Language Code</label>
            <InputText 
              v-model="formValues.language_code" 
              placeholder="Language Code"
            />
            <span class="text-error" v-if="formValues.language_code && formValues.language_code.length > 2 && submitted">Language Code can only be 2 digits.</span>
          </div>

          <div class="mb-5">
            <label class="mb-1 block font-bold">Risk Category</label>
            <Dropdown
              class="w-full p-inputtext-sm"
              v-model="formValues.risk_category"
              :options="Object.entries(riskcategory).map(([value, label]) => ({value, label}))"
              optionLabel="label"
              optionValue="value"
              placeholder="Choose an option"
            />
          </div>
        </div>

        <div class="text-right mt-5">
          <router-link to="/customer-management/customer" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
          <button 
            type="button" 
            @click="handleSubmit" 
            class="btn btn-primary w-24"
          >
              <span v-if="id">Update</span>
              <span v-else>Create</span>
          </button>
        </div>
      </div>
      <!-- <FormulateForm
        v-model="formValues"
        :schema="schema"
        @submit="handleSubmit"
        @changeForm="changeForm"
        @updateDistrictOptions="updateDistrictOptions"
        @updateCommuneOptions="updateCommuneOptions"
        @updatePostalCode="updatePostalCode"
        @listenToCountrySelected="listenToCountrySelected">

        <div class="text-right mt-5">
          <router-link to="/customer-management/customer" class="btn btn-outline-secondary w-24 mr-1">Cancel</router-link>
          <button type="submit" class="btn btn-primary w-24">
              <span v-if="id">Update</span>
              <span v-else>Create</span>
          </button>
        </div>
      </FormulateForm> -->
    </div>
  </div>
</template>

<script setup>

import axios from 'axios'
import formField from './FormFieldCustomers'
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute();
const router = useRouter();
const id = route.params.id ?? null;
const formValues = reactive({
  customer_type: "IC",
  cust_classification: "01-001",
  contactgroup: [
    {
      contact_level: "PRIMARY",
      contact_type: "TELEPHONE",
      contact_info: ""
    }
  ]
});

const submitted = ref(false);
const schema = ref(formField.individual);
const occupationData = ref([]);
const communeOptons = ref([]);
const districtOptions = ref([]);
const customerTypes = ref([]);
const customerIndis = ref({});
const genders = ref({ M: 'Male', F: 'Female' });
const identityTypes =  ref({ N: 'National ID Card', P: 'Passport', F: "Family Book", T: "TID Number" });
const provinceOptions = ref([]);
const countryOptions = ref([]);
const contactlvl = ref({});
const contacttype = ref({});
const riskcategory = ref({ L: 'Low', M: 'Medium', H: "High" });
const customerCorp = ref([]);

const refEnum = () => {
  axios.get('/refenumforcustomer').then(response => {
    customerTypes.value = response.data.customertype;
    var customerType = formValues.customer_type;
    // customer type category
    formField.individual[0].children[0].options =
      response.data.customertype;
    // end customer type category

    // customer occcupation
    customerIndis.value = response.data.customerIndi;
    var corporate = response.data.customerCorp;
    countryOptions.value = response.data.countryOptions;
    provinceOptions.value = response.data.provinceOptions;
    communeOptons.value = response.data.communeOptions;
    districtOptions.value = response.data.districtOptions;
    contactlvl.value = response.data.contactlvl;
    contacttype.value = response.data.contacttype;
    customerCorp.value = response.data.customerCorp;

    if (customerType == 'IC') {
        formField.individual[2].children[2].options = customerIndis.value;
        formField.individual[4].children[6].options = provinceOptions.value;
        formField.individual[4].children[11].options = countryOptions.value;
    } else {
        formField.corporate[2].children[4].options = corporate;
        formField.corporate[2].children[6].options = countryOptions.value;
        formField.corporate[2].children[7].options = provinceOptions.value;
    }
    occupationData.value = response.data;
    // end customer occcupation

    // contact information
    formField.contact[0].children[0].children[0].options =
      response.data.contactlvl;
    formField.contact[0].children[0].children[1].options =
      response.data.contacttype;
    // contact information
  }).then(() => handleEdit())
}

const getFixRow = () => {
  // contact individual form
  formField.individual[6] = formField.contact[0]
  // end contact information

  // additional information
  formField.individual[7] = formField.additional[1]
  // end additional information
};

const changeForm = () => {
  var customerType = formValues.customer_type;
  var corporate = occupationData.value.customerCorp;

  if (customerType == 'IC') {
    formValues.cust_classification = '01-001';
    // individual form
    // schema.value = formField.individual
    // getFixRow()
    // end individual form
  } else {
    formValues.cust_classification = '02-001';
    // corporateForm(customerType, corporate)

    // Reassign value to form
    // refEnum()
    // handleEdit()
  }
};

const addNewContactInformatinRow = () => {
  formValues.contactgroup.push(
    {
      contact_level: "PRIMARY",
      contact_type: "TELEPHONE",
      contact_info: ""
    }
  );
}

const removeContactInformatinRow = (index) => {
  formValues.contactgroup.splice(index, 1);
}

const updatePostalCode = () => {
  var commune = formValues.commune;
  if(commune)
      formValues.postal_code = communeOptons.value.find(item => item.commune == commune).postal_code;
  else
      formValues.postal_code = null
};

const validateForm = () => {

  switch(formValues.customer_type) {

    case "IC":

      if(!formValues.name_en || 
        !formValues.date_of_birth || 
        !formValues.identity_no || 
        !formValues.national ||
        !formValues.nationality
      ) return false;

      break;

    case "CL":

      if(!formValues.name_en || 
        !formValues.tin_code
      ) return false;

      break;

    case "CA":

      if(!formValues.name_en || 
        !formValues.tin_code ||
        !formValues.country_code
      ) return false;

      break;

    default:
      break;

  }

  if(formValues.contactgroup.length > 0) {
    if(formValues.contactgroup.filter(contact => contact.contact_info.length < 1).length > 0) {
      return false;
    };
  }


  if(formValues.language_code && formValues.language_code.length > 2) {
    return false
  }

  return true;
}

const handleSubmit = () => {

  submitted.value = true;
  const validation = validateForm();

  if(!validation) return;

  updatePostalCode();

  if (id) {
    axios.put('/customers/' + id, formValues).then(response => {
      if (response) {
        router.push({
          name: 'Customer',
          params: { result: response.data },
        })
      }
    }).catch(err => {
      if (err?.response?.status === 422) {
        notify('Validation failed', 'error');
      } else {
        notify(err?.response?.data?.message, 'error');
      }
    })
  } else
    axios .post('/customers', formValues).then(response => {
      if (response) {
        router.push({
          name: 'Customer',
          params: { result: response.data },
        })
      }
    }).catch(err => {
      if (err?.response?.status === 422) {
        notify('Validation failed', 'error');
      } else {
        notify(err?.response?.data?.message, 'error');
      }
    })
};

const handleEdit = () => {
  if (id) {
    axios.get('/customers/' + id + '/edit').then(response => {
        formField.individual[0].children[0].disabled = true
        if (response) {
          var customer = response.data.customer
          var customerOption = response.data.customerOption
          var contactData = response.data.contactGroup
          var addressData = response.data.addressData

          formValues.customer_type = customer.customer_type

          formValues.name_kh = customer.name_kh
          formValues.name_en = customer.name_en
          formValues.cust_classification = customer.cust_classification
          formValues.address_en = customer.address_en
          formValues.village_en = customer.village_en
          formValues.postal_code = customer.postal_code
          formValues.country_code = customer.country_code
          if(addressData){
            formValues.province = addressData.province
            formValues.district = addressData.district
            formValues.commune = addressData.commune
          }

          if (customer.customer_type == 'IC') {
            formValues.gender = customerOption.gender
            formValues.date_of_birth = customerOption.date_of_birth
            formValues.identity_type = customerOption.identity_type
            formValues.identity_no = customerOption.identity_no
            formValues.national = customerOption.national
            formValues.nationality = customerOption.nationality
            formValues.identity_iss_date = customerOption.identity_iss_date
            formValues.identity_exp_date = customerOption.identity_exp_date
          } else {
            formValues.tin_code = customerOption.tin_code ? customerOption.tin_code : customerOption.foreign_tin_no
            formValues.incorporate_date = customerOption.incorporate_date
            formValues.business_registration_no = customerOption.business_registration_no
          }
          formValues.contactgroup = contactData
          formValues.language_code = customer.language_code
          formValues.risk_category = customer.risk_category
          formValues.broker_id = customer.broker_id
        }
      }).catch(err => {
      if (err?.response?.status === 422) {
        notify('Validation failed', 'error');
      } else {
        notify(err?.response?.data?.message, 'error');
      }
    })
  } else {
    formField.individual[0].children[0].disabled = false
  }
};

onMounted(() => {
  refEnum();
  getFixRow();
})

// export default {
//   data() {
//     return {
//       id: this.$route.params.id ?? null,
//       formValues: {
//         customer_type: "IC",
//         cust_classification: "01-001",
//         contact_level: "PRIMARY",
//         contact_type: "TELEPHONE"
//       },

//       submitted: false,
//       schema: formField.individual,
//       occupationData: [],
//       communeOptons: [],
//       districtOptions: [],
//       customerTypes: [],
//       customerIndis: {},
//       genders: { M: 'Male', F: 'Female' },
//       identityTypes:  { N: 'National ID Card', P: 'Passport', F: "Family Book", T: "TID Number" },
//       provinceOptions: [],
//       countryOptions: [],
//       contactlvl: {},
//       contacttype: {},
//       riskcategory: { L: 'Low', M: 'Medium', H: "High" }
//     }
//   },
//   mounted() {
//     this.refEnum()
//     this.getFixRow()
//   },
//   // watch: {
//   //   formValues: {
//   //     deep: true,
//   //     handler (newValue, oldValue) {
//   //       // console.log(this.districtOptions[100]);
//   //       console.log(this.districtOptions.filter(obj => "Siem Reap" === obj.province));
//   //     }
//   //   }
//   // },
//   methods: {
//     refEnum() {
//       axios.get('/refenumforcustomer').then(response => {
//         this.customerTypes = response.data.customertype
//         var customerType = this.formValues.customer_type
//         // customer type category
//         formField.individual[0].children[0].options =
//           response.data.customertype
//         // end customer type category

//         // customer occcupation
//         this.customerIndis = response.data.customerIndi
//         var corporate = response.data.customerCorp
//         this.countryOptions = response.data.countryOptions
//         this.provinceOptions = response.data.provinceOptions
//         this.communeOptons = response.data.communeOptions
//         this.districtOptions = response.data.districtOptions
//         this.contactlvl = response.data.contactlvl
//         this.contacttype = response.data.contacttype
//         this.risk

//         if (customerType == 'IC') {
//             formField.individual[2].children[2].options = this.customerIndis
//             formField.individual[4].children[6].options = this.provinceOptions
//             formField.individual[4].children[11].options = this.countryOptions
//         } else {
//             formField.corporate[2].children[4].options = corporate
//             formField.corporate[2].children[6].options = this.countryOptions
//             formField.corporate[2].children[7].options = this.provinceOptions
//         }
//         this.occupationData = response.data
//         // end customer occcupation

//         // contact information
//         formField.contact[0].children[0].children[0].options =
//           response.data.contactlvl
//         formField.contact[0].children[0].children[1].options =
//           response.data.contacttype
//         // contact information
//       }).then(() => this.handleEdit())
//     },
//     getFixRow() {
//       // contact individual form
//       formField.individual[6] = formField.contact[0]
//       // end contact information

//       // additional information
//       formField.individual[7] = formField.additional[1]
//       // end additional information
//     },
//     changeForm() {
//       var customerType = this.formValues.customer_type
//       var corporate = this.occupationData.customerCorp
//       if (customerType == 'IC') {
//         // individual form
//         this.schema = formField.individual
//         this.getFixRow()
//         // end individual form
//       } else {
//         this.corporateForm(customerType, corporate)

//         // Reassign value to form
//         this.refEnum()
//         this.handleEdit()
//       }
//     },
//     updateDistrictOptions(){
//         var province = this.formValues.province
//         var customerType = this.formValues.customer_type
//         var districtOptions = [];
//         this.formValues.district = null
//         this.formValues.commune = null
//         for(let index = 0 ; index < this.districtOptions.length; index++){
//             if(this.districtOptions[index].province == province){
//                 let district = {
//                     value: this.districtOptions[index].district,
//                     label: this.districtOptions[index].district
//                 }
//                 districtOptions.push(district)
//             }
//         }

//         if(customerType == 'IC')
//             formField.individual[4].children[7].options = districtOptions
//         else
//             formField.corporate[2].children[8].options = districtOptions
//         this.updatePostalCode()
//     },
//     updateCommuneOptions(){
//         var district = this.formValues.district;
//         var customerType = this.formValues.customer_type
//         var communeOptions = [];
//         this.formValues.commune = null
//         for(let index = 0 ; index < this.communeOptons.length; index++){
//             if(this.communeOptons[index].district == district){
//                 let commune = {
//                     value: this.communeOptons[index].commune,
//                     label: this.communeOptons[index].commune
//                 }
//                 communeOptions.push(commune)
//             }
//         }
//         if(customerType == 'IC')
//             formField.individual[4].children[8].options = communeOptions
//         else
//             formField.corporate[2].children[9].options = communeOptions
//         this.updatePostalCode()
//     },
//     updatePostalCode(){
//         var commune = this.formValues.commune;
//         if(commune)
//             this.formValues.postal_code = this.communeOptons.find(item => item.commune == commune).postal_code;
//         else
//             this.formValues.postal_code = null
//     },
//     listenToCountrySelected(){
//         var customerType = this.formValues.customer_type
//         if (customerType == 'IC') {
//             formField.individual[4].children[6].label = 'City/Province'
//             formField.individual[4].children[6].validation = ''

//             formField.individual[4].children[7].label = 'District'
//             formField.individual[4].children[7].validation = ''

//             formField.individual[4].children[8].label = 'Commune'
//             formField.individual[4].children[8].validation = ''
//         }else{
//             formField.corporate[2].children[7].label = 'City/Province'
//             formField.corporate[2].children[7].validation = ''

//             formField.corporate[2].children[8].label = 'District'
//             formField.corporate[2].children[8].validation = ''

//             formField.corporate[2].children[9].label = 'Commune'
//             formField.corporate[2].children[9].validation = ''
//         }
//     },
//     corporateForm(customerType, occupationObj) {
//         this.schema = formField.corporate
//         // corporate form
//         formField.corporate[0] = formField.individual[0]

//         // get business/occupation option
//         formField.corporate[2].children[4].options = occupationObj

//         // contact information
//         formField.corporate[4] = formField.contact[0]

//         // contact additional information
//         formField.corporate[6] = formField.additional[0]
//         formField.corporate[7] = formField.additional[1]

//         // change field for corporate form

//         formField.corporate[2].children[7].label = 'City/Province'
//         formField.corporate[2].children[7].validation = ''

//         formField.corporate[2].children[8].label = 'District'
//         formField.corporate[2].children[8].validation = ''

//         formField.corporate[2].children[9].label = 'Commune'
//         formField.corporate[2].children[9].validation = ''
//         if (customerType == 'CA') {
//             formField.corporate[2].children[0].validation = ''
//             formField.corporate[2].children[0].label = 'Company Name Khmer'

//             formField.corporate[2].children[2].label = 'Foreign TIN Number *'
//             formField.corporate[2].children[2].placeholder = 'Foreign TIN Number'
//             formField.corporate[2].children[6].label = 'Country *'
//             formField.corporate[2].children[6].validation = 'required'
//         } else {
//             formField.corporate[2].children[2].label = 'TIN Code *'
//             formField.corporate[2].children[2].placeholder = 'TIN Code'

//             formField.corporate[2].children[6].label = 'Country'
//             formField.corporate[2].children[6].validation = ''
//         }
//     },
//     handleSubmit() {
//       // this.submitted = true;
//       return;
//       if (this.id) {
//         axios.put('/customers/' + this.id, this.formValues).then(response => {
//           if (response) {
//             this.$router.push({
//               name: 'Customer',
//               params: { result: response.data },
//             })
//           }
//         }).catch(err => {
//           if (err?.response?.status === 422) {
//             notify('Validation failed', 'error');
//           } else {
//             notify(err?.response?.data?.message, 'error');
//           }
//         })
//       } else
//         axios .post('/customers', this.formValues).then(response => {
//           if (response) {
//             this.$router.push({
//               name: 'Customer',
//               params: { result: response.data },
//             })
//           }
//         }).catch(err => {
//           if (err?.response?.status === 422) {
//             notify('Validation failed', 'error');
//           } else {
//             notify(err?.response?.data?.message, 'error');
//           }
//         })
//     },
//     handleEdit() {
//       // this.submitted = true;
//       if (this.id) {
//         axios.get('/customers/' + this.id + '/edit').then(response => {
//             formField.individual[0].children[0].disabled = true
//             if (response) {
//               var customer = response.data.customer
//               var customerOption = response.data.customerOption
//               var contactData = response.data.contactGroup
//               var addressData = response.data.addressData

//               this.formValues.customer_type = customer.customer_type

//               this.formValues.name_kh = customer.name_kh
//               this.formValues.name_en = customer.name_en
//               this.formValues.cust_classification = customer.cust_classification
//               this.formValues.address_en = customer.address_en
//               this.formValues.village_en = customer.village_en
//               this.formValues.postal_code = customer.postal_code
//               this.formValues.country_code = customer.country_code
//               if(addressData){
//                 this.formValues.province = addressData.province
//                 this.formValues.district = addressData.district
//                 this.formValues.commune = addressData.commune
//               }

//               if (customer.customer_type == 'IC') {
//                 this.formValues.gender = customerOption.gender
//                 this.formValues.date_of_birth = customerOption.date_of_birth
//                 this.formValues.identity_type = customerOption.identity_type
//                 this.formValues.identity_no = customerOption.identity_no
//                 this.formValues.national = customerOption.national
//                 this.formValues.nationality = customerOption.nationality
//                 this.formValues.identity_iss_date = customerOption.identity_iss_date
//                 this.formValues.identity_exp_date = customerOption.identity_exp_date
//               } else {
//                 this.formValues.tin_code = customerOption.tin_code ? customerOption.tin_code : customerOption.foreign_tin_no
//                 this.formValues.incorporate_date = customerOption.incorporate_date
//                 this.formValues.business_registration_no = customerOption.business_registration_no
//               }
//               this.formValues.contactgroup = contactData
//               this.formValues.language_code = customer.language_code
//               this.formValues.risk_category = customer.risk_category
//               this.formValues.broker_id = customer.broker_id
//             }
//           }).catch(err => {
//           if (err?.response?.status === 422) {
//             notify('Validation failed', 'error');
//           } else {
//             notify(err?.response?.data?.message, 'error');
//           }
//         })
//       } else {
//         formField.individual[0].children[0].disabled = false
//       }
//     },
//   },
// }

</script>
<style scoped>
  h3 {
    font-size: 1.17em !important;
    font-weight: bold !important;
  }
</style>
