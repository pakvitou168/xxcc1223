<template>
  <div class="w-full box px-2 py-2">
    <div class="w-full py-2 bg-blue-50 text-base font-bold my-2 px-2 flex mb-3">
      <div class="w-full intro-y pt-3">
        <h1>Customer Detail: {{ id }}</h1>
      </div>
      <div class="float-right">
        <router-link v-if="canUpdate"
          :to="{
            path: '/customer-management/customer/' + this.id + '/edit',
            params: { hisRoute: 'detail' },
          }"
        >
          <button class="btn btn-primary mx-1 intro-x">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              ></path>
            </svg>
          </button>
        </router-link>
      </div>
    </div>
    <!-- <div class="w-full py-2 bg-blue-50 text-base font-bold my-2 px-2"></div> -->
    <table class="w-full">
      <tbody>
        <tr
        v-for="(cust, index) in customerData"
        :key="index"
        class="my-2 border-b-2"
      >
        <td class="px-2 py-2 text-md font-bold intro-y w-1/3">
          {{ cust.head.toUpperCase() }}
        </td>
        <td class="px-4 py-2 text-base intro-y w-2/3">
          {{ cust.body }}
        </td>
      </tr>
      </tbody>
    </table>
    <!-- block customer type -->
    <div class="w-full py-2 bg-blue-50 text-base font-bold my-2 px-2">
      <h1>{{ customerOptional }}</h1>
    </div>
    <table class="w-full">
      <tbody>
        <tr
        v-for="(cust, index) in customerTypeData"
        :key="index"
        class="my-2 border-b-2"
      >
        <td class="px-2 py-2 text-md font-bold intro-y w-1/3">
          {{ cust.head.toUpperCase() }}
        </td>
        <td class="px-4 py-2 text-base intro-y w-2/3">
          {{ cust.body }}
        </td>
      </tr>
      </tbody>
    </table>

    <!-- block customer conatact info -->
    <div class="w-full py-2 bg-blue-50 text-base font-bold my-2 px-2">
      <h1>Contact Info</h1>
    </div>
    <table class="w-full">
      <tbody>
        <tr>
          <th
            v-for="(head, index) in contactInfoTitle"
            :key="index"
            class="border-2 py-2"
          >
            {{ head.toUpperCase() }}
          </th>
        </tr>
        <tr
          v-for="(info, index) in contactInfoData"
          :key="index"
          class="text-center"
        >
          <td class="border-2 py-2">{{ info.level }}</td>
          <td class="border-2 py-2">{{ info.type }}</td>
          <td class="border-2 py-2">{{ info.info }}</td>
        </tr>
      </tbody>
    </table>
    <!-- end block customer conatact info -->
  </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment'
import UserPermissions from '../../../mixins/UserPermissions'
export default {
  mixins: [UserPermissions],
  data() {
    return {
      id: this.$route.params.id,
      functionCode: 'CUSTOMER',
      customerData: [],
      customerOptional: '',
      customerTypeData: [],
      contactInfoData: [],
      contactInfoTitle: ['contact level', 'contact type', 'contact info'],
    }
  },
  mounted() {
    this.show()
  },
  methods: {
    show() {
      axios.get('/customers/' + this.id).then(response => {
        var data = response.data
        // customer
        this.customer(data.customer, data.address, data.country)
        // end customer

        // customer type
        this.customerType(data.customerType, data.customer.customer_type)
        // end customer type

        // customer contact info
        this.contactInfo(data.customerContact)
        // end customer contact info
      })
    },
    customer(data, address, country) {
      var customerOptional = ''
      var nameKhOptional = ''
      var nameEnOptional = ''
      var customerType = data.customer_type
      if (customerType == 'IC') {
        customerOptional = 'Individual Customer'
        nameKhOptional = 'Individual Name in Khmer'
        nameEnOptional = 'Individual Name in LATIN'
      } else if (customerType == 'CL') {
        customerOptional = 'Corporate Customer-Local'
        nameKhOptional = 'Company Name Khmer'
        nameEnOptional = 'Company Name Latin'
      } else {
        customerOptional = 'Corporate Customer-Abroad'
        nameKhOptional = 'Company Name Khmer'
        nameEnOptional = 'Company Name Latin'
      }
      this.customerOptional = customerOptional
      var status = data.status
      var statusOptional = ''
      if (status == 'ACT') {
        statusOptional = 'ACTIVE'
      } else {
        statusOptional = 'DELECT'
      }
      var nameKh = data.name_kh
      var nameEn = data.name_en
      var custClassification = data.occupation_descritpion
      var brokerId = data.broker_id
      var languageCode = data.language_code
      var addressEn = this.getAddressStr(data.address_en, data.village_en, address, country)

      var riskCategory = data.risk_category
      var riskOptional = ''
      if (riskCategory == 'L') {
        riskOptional = 'Low'
      } else if (riskOptional == 'M') {
        riskOptional = 'Medium'
      } else {
        riskOptional = 'High'
      }

      var createdAt = data.created_at
      var createOptional = ''
      if (createdAt) {
        createOptional = moment(createdAt).format('MMMM Do YYYY')
      } else {
        createOptional = 'N/A'
      }

      var updatedAt = data.updated_at
      var updateOptional = ''
      if (updatedAt) {
        updateOptional = moment(updatedAt).format('MMMM Do YYYY')
      } else {
        updateOptional = 'N/A'
      }

      this.customerData = [
        {
          head: 'customer type',
          body: customerOptional,
        },
        {
          head: nameKhOptional,
          body: nameKh,
        },
        {
          head: nameEnOptional,
          body: nameEn,
        },
        {
          head: 'Business/Occupation',
          body: custClassification,
        },
        {
          head: 'Distribution Channel',
          body: brokerId,
        },
        {
          head: 'Language Code',
          body: languageCode,
        },
        {
          head: 'Risk Category',
          body: riskOptional,
        },
        {
          head: 'Address',
          body: addressEn,
        },
        {
          head: 'status',
          body: statusOptional,
        },
        {
          head: 'created at',
          body: createOptional,
        },
        {
          head: 'updated at',
          body: updateOptional,
        },
      ]
    },

    getAddressStr(custom_address, village, address, country) {
        if(address)
            if(country)
                return `${custom_address ? custom_address + ', ' : ''}
                        ${village ? village + ', ' : ''}
                        ${address.commune ? address.commune + ', ' : ''}
                        ${address.district ? address.district + ', ' : ''}
                        ${address.province ? address.province : ''}
                        ${address.province == 'Phnom Penh' ? ' Capital, ' : ' Province, '}
                        ${country}`
            else
                return `${custom_address ? custom_address + ', ' : ''}
                        ${village ? village + ', ' : ''}
                        ${address.commune ? address.commune + ', ' : ''}
                        ${address.district ? address.district + ', ' : ''}
                        ${address.province ? address.province : ''}
                        ${address.province == 'Phnom Penh' ? ' Capital' : ' Province'}`
        else if(country)
            return `${custom_address ? custom_address + ', ' : ''}
                    ${village ? village + ', ' : ''}
                    ${country}`
        else if(village)
            return `${custom_address ? custom_address + ', ' : ''}
                    ${village}`
        else
            return `${custom_address}`
    },

    customerType(data, type) {
      if (type == 'IC') {
        var identity = ''
        if (data[0].identity_type == 'N') {
          identity = 'National ID Card'
        } else if (data[0].identity_type == 'P') {
          identity = 'Passport'
        } else if (data[0].identity_type == 'F') {
          identity = 'Family Book'
        } else {
          identity = 'TID Number'
        }
        this.customerTypeData = [
          {
            head: 'gender',
            body: data[0].gender == 'M' ? 'Male' : 'Female',
          },
          {
            head: 'date of birth',
            body: data[0].date_of_birth,
          },
          {
            head: 'identity type',
            body: identity,
          },
          {
            head: 'identity no',
            body: data[0].identity_no,
          },
          {
            head: 'national',
            body: data[0].national,
          },
          {
            head: 'nationality',
            body: data[0].nationality,
          },
          {
            head: 'identity issue date',
            body: data[0].identity_iss_date,
          },
          {
            head: 'identity expire date',
            body: data[0].identity_exp_date,
          },
        ]
      } else {
        this.customerTypeData = [
          {
            head: type == 'CL' ? 'TIN Code' : 'Foreign TIN Number',
            body: type == 'CL' ? data[0].tin_code : data[0].foreign_tin_no,
          },
          {
            head: 'incorporate date',
            body: data[0].incorporate_date,
          },
          {
            head: 'Business Registration No.',
            body: data[0].business_registration_no,
          },
        ]
      }
    },
    contactInfo(data) {
      let value = []
      for (var i = 0; i < data.length; i++) {
        value.push({
          level: data[i].contact_level,
          type: data[i].contact_type,
          info: data[i].contact_info,
        })
      }
      this.contactInfoData = value
    },
  },
}
</script>

<style>
</style>
