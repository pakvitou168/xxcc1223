<template>
  <div>
    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            <span>{{ product_insurance_name }}</span>
          </div>
          <div class="mt-2 text-xl text-center">POLICY SCHEDULE</div>
        </div>
        <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
          <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
              Policy No.: <span>{{ documentNo }}</span>
            </div>
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
              Business Code: {{ dataDetail.business_code }}
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">THE INSURED NAME:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-bold mb-3">
              <span>{{ dataDetail.insured_name }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">CORRESPONDENCE ADDRESS:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              <span>{{ dataDetail.correspondence_address }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">BUSINESS / OCCUPATION:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ dataDetail.business_occupation }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">PERIOD OF INSURANCE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ dataDetail.period_of_insurance }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">COVERAGE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="dataDetail.coverage"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">POLICY WORDING:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm">
              Subject to {{ product_insurance_name }} Policy Wording ({{
                dataDetail.policy_wording_version
              }})
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">GEOGRAPHICAL LIMIT:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ dataDetail.geographical_limit?.clause }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">INSURED PERSONS:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="dataDetail.insured_person_note"></div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16">
        <div class="flex" v-if="loadedNameList && dataDetail.insured_persons.length > 1">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">NAME LIST:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm mb-3 underline cursor-pointer btn-attach" @click="exportInsuredPersons">{{
                dataDetail.insured_persons.length + ' persons as per list attached'
              }}
            </div>
          </div>
        </div>
        <div class="table-responsive" v-if="loadedNameList && dataDetail.insured_persons.length == 1">
          <table class="w-full border">
            <thead>
            <tr class="bg-gray-300">
              <th class="border p-2">Insured Person</th>
              <th class="border p-2">Occupation</th>
              <th class="border p-2">Sex</th>
              <th class="border p-2">Date of Birth</th>
              <th class="border p-2">Inception Date</th>
              <th class="border p-2">Expiry Date</th>
              <th class="border p-2">Endorsement Effective Date</th>
              <th class="border p-2">IPD Plan</th>
              <th class="border p-2">OPD Plan</th>
              <th class="border p-2">IPD Premium(USD)</th>
              <th class="border p-2">OPD Premium(USD)</th>
              <th class="border p-2">Total Premium(USD)</th>
              <th class="border p-2">Transaction Type (Policy/Addition/Deletion/Others)</th>
              <th class="border p-2">Policy/Endorsement No.</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="insured_person in dataDetail.insured_persons" :key="insured_person.id">
              <td class="border p-2">{{ insured_person.insured_person }}</td>
              <td class="border p-2">{{ insured_person.occupation }}</td>
              <td class="border p-2">{{ insured_person.gender }}</td>
              <td class="border p-2">{{ insured_person.date_of_birth }}</td>
              <td class="border p-2">{{ insured_person.inception_date }}</td>
              <td class="border p-2">{{ insured_person.expiry_date }}</td>
              <td class="border p-2">{{ insured_person.endorsement_effective_date }}</td>
              <td class="border p-2 whitespace-nowrap">{{ insured_person.standard_plan }}</td>
              <td class="border p-2 whitespace-nowrap">{{ insured_person.optional_plan }}</td>
              <td class="border p-2 text-right">{{ insured_person.standard_premium }}</td>
              <td class="border p-2 text-right">{{ insured_person.optional_premium }}</td>
              <td class="border p-2 text-right">{{ insured_person.total_premium }}</td>
              <td class="border p-2 text-right">{{ insured_person.transaction_type }}</td>
              <td class="border p-2 text-right">{{ insured_person.document_no }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="text-md font-bold mb-3">SCHEDULE OF BENEFITS</div>
        </div>
        <table class="w-full border">
          <thead>
          <tr class="bg-gray-300">
            <th class="border p-2">Item</th>
            <th class="border p-2">Benefits</th>
            <th class="border p-2">Number of Days</th>
            <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_1 > 0">Plan I (USD)</th>
            <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_2 > 0">Plan II (USD)</th>
            <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_3 > 0">Plan III (USD)</th>
            <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_4 > 0">Plan IV (USD)</th>
            <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_5 > 0">Plan V (USD)</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, index) in dataDetail.standard_benefits" :key="'sd_bnf' + index">
            <td class="border p-2 text-center">{{ index + 1 }}</td>
            <td class="border p-2">{{ item?.name }}</td>
            <td class="border p-2">{{ item?.amount ? `${item?.amount} days` : "" }}</td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">{{
                formatCurrency(item?.plan_1)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">{{
                formatCurrency(item?.plan_2)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">{{
                formatCurrency(item?.plan_3)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">{{
                formatCurrency(item?.plan_4)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">{{
                formatCurrency(item?.plan_5)
              }}
            </td>
          </tr>
          <tr v-if="dataDetail.standard_base_plan_amount">
            <td colspan="3" class="border p-2 text-right">
              {{ dataDetail.standard_base_plan_amount?.name }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
              {{ formatCurrency(dataDetail.standard_base_plan_amount?.plan_1) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
              {{ formatCurrency(dataDetail.standard_base_plan_amount?.plan_2) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
              {{ formatCurrency(dataDetail.standard_base_plan_amount?.plan_3) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
              {{ formatCurrency(dataDetail.standard_base_plan_amount?.plan_4) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
              {{ formatCurrency(dataDetail.standard_base_plan_amount?.plan_5) }}
            </td>
          </tr>
          <tr>
            <td colspan="3" class="border p-2 text-right">
              {{ dataDetail.standard_premium_per_person?.name }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
              {{ formatCurrency(dataDetail.standard_premium_per_person?.plan_1) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
              {{ formatCurrency(dataDetail.standard_premium_per_person?.plan_2) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
              {{ formatCurrency(dataDetail.standard_premium_per_person?.plan_3) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
              {{ formatCurrency(dataDetail.standard_premium_per_person?.plan_4) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
              {{ formatCurrency(dataDetail.standard_premium_per_person?.plan_5) }}
            </td>
          </tr>

          <tr>
            <td colspan="3" class="border p-2 text-right">
              {{ dataDetail.standard_total_premium?.name }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
              {{ formatCurrency(dataDetail.standard_total_premium?.plan_1) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
              {{ formatCurrency(dataDetail.standard_total_premium?.plan_2) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
              {{ formatCurrency(dataDetail.standard_total_premium?.plan_3) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
              {{ formatCurrency(dataDetail.standard_total_premium?.plan_4) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
              {{ formatCurrency(dataDetail.standard_total_premium?.plan_5) }}
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="px-5 sm:px-16 py-5" v-if="totalOptBenefit > 0">
        <div class="flex">
          <div class="text-md font-bold mb-3">OPTIONAL EXTENSIONS</div>
        </div>
        <table class="w-full border">
          <thead>
          <tr class="bg-gray-300">
            <th class="border p-2">Item</th>
            <th class="border p-2">Benefits</th>
            <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_1 > 0">Limit Plan I</th>
            <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_2 > 0">Limit Plan II</th>
            <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_3 > 0">Limit Plan III</th>
            <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_4 > 0">Limit Plan IV</th>
            <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_5 > 0">Limit Plan V</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, index) in dataDetail.optional_benefits" :key="'opt_bnf' + index">
            <td class="border p-2 text-center">{{ index + 1 }}</td>
            <td class="border p-2">{{ item?.name }}</td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_1 > 0">{{
                formatCurrency(item?.plan_1)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_2 > 0">{{
                formatCurrency(item?.plan_2)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_3 > 0">{{
                formatCurrency(item?.plan_3)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_4 > 0">{{
                formatCurrency(item?.plan_4)
              }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_5 > 0">{{
                formatCurrency(item?.plan_5)
              }}
            </td>
          </tr>
          <tr>
            <td colspan="2" class="border p-2 text-right">
              {{ dataDetail.optional_premium_per_person?.name }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_1 > 0">
              {{ formatCurrency(dataDetail.optional_premium_per_person?.plan_1) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_2 > 0">
              {{ formatCurrency(dataDetail.optional_premium_per_person?.plan_2) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_3 > 0">
              {{ formatCurrency(dataDetail.optional_premium_per_person?.plan_3) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_4 > 0">
              {{ formatCurrency(dataDetail.optional_premium_per_person?.plan_4) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_5 > 0">
              {{ formatCurrency(dataDetail.optional_premium_per_person?.plan_5) }}
            </td>
          </tr>
          <tr>
            <td colspan="2" class="border p-2 text-right">
              {{ dataDetail.optional_total_premium?.name }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_1 > 0">
              {{ formatCurrency(dataDetail.optional_total_premium?.plan_1) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_2 > 0">
              {{ formatCurrency(dataDetail.optional_total_premium?.plan_2) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_3 > 0">
              {{ formatCurrency(dataDetail.optional_total_premium?.plan_3) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_4 > 0">
              {{ formatCurrency(dataDetail.optional_total_premium?.plan_4) }}
            </td>
            <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_5 > 0">
              {{ formatCurrency(dataDetail.optional_total_premium?.plan_5) }}
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="px-5 sm:px-16 py-5" v-if="totalAddiBenefit > 0">
        <div class="flex">
          <div class="text-md font-bold mb-3">ADDITIONAL EXTENSIONS</div>
        </div>
        <table class="w-full border">
          <thead>
          <tr class="bg-gray-300">
            <th class="border p-2">Item</th>
            <th class="border p-2">Benefits</th>
            <th class="border p-2"
                v-if="dataDetail.additional_benefits && someAdditionalBenefitsBiggerThanZero('plan_1')">Limit Plan I
            </th>
            <th class="border p-2"
                v-if="dataDetail.additional_benefits && someAdditionalBenefitsBiggerThanZero('plan_2')">Limit Plan II
            </th>
            <th class="border p-2"
                v-if="dataDetail.additional_benefits && someAdditionalBenefitsBiggerThanZero('plan_3')">Limit Plan III
            </th>
            <th class="border p-2"
                v-if="dataDetail.additional_benefits && someAdditionalBenefitsBiggerThanZero('plan_4')">Limit Plan IV
            </th>
            <th class="border p-2"
                v-if="dataDetail.additional_benefits && someAdditionalBenefitsBiggerThanZero('plan_5')">Limit Plan V
            </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, index) in dataDetail.additional_benefits" :key="'add_bnf' + index">
            <td class="border p-2 text-center">{{ index + 1 }}</td>
            <td class="border p-2">{{ item?.name }}</td>
            <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_1')">{{
                formatCurrency(item?.plan_1)
              }}
            </td>
            <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_2')">{{
                formatCurrency(item?.plan_2)
              }}
            </td>
            <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_3')">{{
                formatCurrency(item?.plan_3)
              }}
            </td>
            <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_4')">{{
                formatCurrency(item?.plan_4)
              }}
            </td>
            <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_5')">{{
                formatCurrency(item?.plan_5)
              }}
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="px-5 sm:px-16 py-5" v-if="totalPremium">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">TOTAL PREMIUM (USD):</div>
          </div>
          <div class="w-2/3">
            <table class="w-full border">
              <thead>
              <tr class="bg-gray-300">
                <th class="border p-2">Premium (USD)</th>
                <th class="border p-2"
                    v-if="(dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium?.plan_1 > 0)">
                  Plan I
                </th>
                <th class="border p-2"
                    v-if="(dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium?.plan_2 > 0)">
                  Plan II
                </th>
                <th class="border p-2"
                    v-if="(dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium?.plan_3 > 0)">
                  Plan III
                </th>
                <th class="border p-2"
                    v-if="(dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium?.plan_4 > 0)">
                  Plan IV
                </th>
                <th class="border p-2"
                    v-if="(dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium?.plan_5 > 0)">
                  Plan V
                </th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td class="border p-2 text-right">IPD Premium Per Person:</td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium?.plan_1 > 0)">
                  {{
                    dataDetail.standard_total_premium?.plan_1 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_1) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium?.plan_2 > 0)">
                  {{
                    dataDetail.standard_total_premium?.plan_2 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_2) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium?.plan_3 > 0)">
                  {{
                    dataDetail.standard_total_premium?.plan_3 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_3) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium?.plan_4 > 0)">
                  {{
                    dataDetail.standard_total_premium?.plan_4 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_4) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium?.plan_5 > 0)">
                  {{
                    dataDetail.standard_total_premium?.plan_5 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_5) : ''
                  }}
                </td>
              </tr>
              <tr v-if="totalOptBenefit > 0">
                <td class="border p-2 text-right">Optional Extensions Premium Per Person:</td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium?.plan_1 > 0)">
                  {{
                    dataDetail.optional_total_premium?.plan_1 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_1) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium?.plan_2 > 0)">
                  {{
                    dataDetail.optional_total_premium?.plan_2 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_2) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium?.plan_3 > 0)">
                  {{
                    dataDetail.optional_total_premium?.plan_3 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_3) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium?.plan_4 > 0)">
                  {{
                    dataDetail.optional_total_premium?.plan_4 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_4) : ''
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium?.plan_5 > 0)">
                  {{
                    dataDetail.optional_total_premium?.plan_5 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_5) : ''
                  }}
                </td>
              </tr>
              <tr>
                <td class="border p-2 text-right">{{ dataDetail.premium?.name }}</td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium?.plan_1 > 0)">
                  {{
                    formatCurrency(dataDetail.premium?.plan_1)
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium?.plan_2 > 0)">
                  {{
                    formatCurrency(dataDetail.premium?.plan_2)
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium?.plan_3 > 0)">
                  {{
                    formatCurrency(dataDetail.premium?.plan_3)
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium?.plan_4 > 0)">
                  {{
                    formatCurrency(dataDetail.premium?.plan_4)
                  }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium?.plan_5 > 0)">
                  {{
                    formatCurrency(dataDetail.premium?.plan_5)
                  }}
                </td>
              </tr>
              <tr>
                <td class="border p-2 text-right">
                  {{ dataDetail.total_premium?.name }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium?.plan_1 > 0)">
                  {{ formatCurrency(dataDetail.total_premium?.plan_1) }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium?.plan_2 > 0)">
                  {{ formatCurrency(dataDetail.total_premium?.plan_2) }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium?.plan_3 > 0)">
                  {{ formatCurrency(dataDetail.total_premium?.plan_3) }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium?.plan_4 > 0)">
                  {{ formatCurrency(dataDetail.total_premium?.plan_4) }}
                </td>
                <td class="border p-2 text-right"
                    v-if="(dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium?.plan_5 > 0)">
                  {{ formatCurrency(dataDetail.total_premium?.plan_5) }}
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">GRAND TOTAL PREMIUM (USD):</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ formatCurrency(dataDetail.grand_total_premium) }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ENDORSEMENT/CLAUSES:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-for="(endorsement, index) in dataDetail.endorsement_clauses"
                 :key="`EN_${index}`">
              {{ endorsement.clause }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">GENERAL EXCLUSIONS:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-for="(exclusion, index) in dataDetail.general_exclusions"
                 :key="`EX_${index}`">
              {{ exclusion.clause }}
            </div>
          </div>
        </div>
        <div v-if="dataDetail.warranty" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">WARRANTY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="dataDetail.warranty"></div>
          </div>
        </div>
        <div v-if="dataDetail.memorandum" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">MEMORANDUM:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="dataDetail.memorandum"></div>
          </div>
        </div>
        <div v-if="dataDetail.subjectivity" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">SUBJECTIVITY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="dataDetail.subjectivity"></div>
          </div>
        </div>
        <div v-if="dataDetail.remark" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">REMARK:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3" v-html="dataDetail.remark"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">JURISDICTION:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md mb-3">{{ dataDetail?.jurisdiction }}</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED ON:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ dataDetail.issued_on }}</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED By:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ dataDetail.issued_by }}</div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-auto">
            <div class="text-md font-bold mb-3 uppercase">
              Phillip General Insurance (Cambodia) Plc.
            </div>
            <div class="my-2" v-bind:class="{ relative: signature }" style="min-height: 150px">
              <img v-if="signature && canShowSignature" class="img-over" :src="'/' + signature.file_url"
                   style="max-height: 150px" alt=""/>
              <img v-if="signature && canShowSignature" class="img-under" src="/images/stamp/phillip_insurance.png"
                   style="max-height: 150px" alt=""/>
            </div>

            <hr class="my-3"/>

            <div class="text-md mb-3 font-medium">Authorised Signature</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import policyServiceService from "@/services/hs/policy_service.service";

export default {
  props: {
    id: Number,
    documentNo: String,
    policyId: [Number, String],
    policyStatus: String,
  },

  data() {
    return {
      functionCode: "HS",
      businessName: "",
      dataDetail: {},
      coverage: [],
      deductibles: [],
      loadedNameList: false,
      signature: null,
      approveDialog: false,
      acceptDialog: false,
      submitted: false,
    };
  },

  computed: {
    totalOptBenefit() {
      let optPremium = this.dataDetail?.optional_total_premium;
      return !optPremium ? 0 : parseFloat(optPremium?.plan_1) + parseFloat(optPremium?.plan_2) + parseFloat(optPremium?.plan_3) + parseFloat(optPremium?.plan_4) + parseFloat(optPremium?.plan_5);
    },
    totalAddiBenefit() {
      let addiBenefits = this.dataDetail?.additional_benefits;
      return addiBenefits ? addiBenefits.reduce((acc, o) => acc + (parseFloat(o.plan_1 ?? 0) + parseFloat(o.plan_2 ?? 0) + parseFloat(o.plan_3 ?? 0) + parseFloat(o.plan_4 ?? 0) + parseFloat(o.plan_5 ?? 0)), 0) : 0
    },
    customer_insured() {
      return this.dataDetail.customer ? this.dataDetail.customer.name_en : null;
    },
    product_insurance_name() {
      return this.dataDetail.product ? this.dataDetail.product.name : null;
    },

    ncd() {
      return `${this.dataDetail.vehicles[0].ncd ?? 0} %`;
    },
    discount() {
      return `${this.dataDetail.vehicles[0].discount} %`;
    },
    isCommercialVehicle() {
      return this.hasPassengerOrTonnage;
    },
    hasPassengerOrTonnage() {
      return this.dataDetail.has_passenger_tonnage;
    },
    canShowSignature() {
      // If policy is approved
      return this.policyStatus === "APV";
    },
    limitToUse() {
      return this.dataDetail.product?.limitation_to_use_en;
    },
    totalPremium() {
      return this.dataDetail.total_premium?.plan_1 + this.dataDetail.total_premium?.plan_2 + this.dataDetail.total_premium?.plan_3 + this.dataDetail.total_premium?.plan_4 + this.dataDetail.total_premium?.plan_5
    }
  },

  methods: {
    someAdditionalBenefitsBiggerThanZero(key) {
      const benefits = this.dataDetail.additional_benefits?.some((benefit) => parseFloat(benefit[key]) > 0)
      return benefits;
    },
    resolveHs() {
      if (this.id) {
        axios.get("/hs/policies/show-detail/" + this.id).then((response) => {
          if (response) {
            this.dataDetail = response.data.hs;
            this.coverage = response.data.coverage;
            this.deductibles = response.data.deductibles;
            this.loadedNameList = true;
            if (this.dataDetail.quotation) this.signature = response.data.signature;
            else {
              policyServiceService.getSignature(this.policyId)
                .then((response) => {
                  if (response) this.signature = response.data.signature;
                });
            }
          }
        });
      }
    },
    exportInsuredPersons() {
      location.href = '/hs/policy-services/' + this.dataDetail.data_id + '/export-insured-persons/' + this.dataDetail.policy_no
    },
    formatCurrency(number) {
      if (!number) return "";
      if (typeof number === "string" && !number.includes(",")) {
        number = parseFloat(number);
      }
      return number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
  mounted() {
    this.resolveHs();
  },
};
</script>

<style scoped>
.table th {
  white-space: normal;
}

.btn-attach:hover {
  color: rgb(28, 63, 170);
}

.table td,
.table th {
  padding: 0.5rem 0.75rem;
}

.img-under {
  position: absolute;
  left: 0px;
  top: 0px;
  z-index: -1;
}

.img-over {
  position: absolute;
  left: 80px;
  top: 10px;
  z-index: -1;
}
</style>
