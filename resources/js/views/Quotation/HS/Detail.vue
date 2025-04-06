<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">H & S Quotation Detail</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canProceedToPolicy" class="btn btn-primary shadow-md mr-2" title="Proceed to Policy"
          @click="proceedToPolicy">
          <span class="h-6 leading-6">Proceed to Policy</span>
        </button>
        <button v-if="canApprove" class="btn btn-primary shadow-md mr-2" title="Approve" @click="openDialog('APPROVE')">
          <span class="h-6 leading-6">Approve</span>
        </button>
        <button v-if="canAccept" class="btn btn-primary shadow-md mr-2" title="Accept" @click="openDialog('ACCEPT')">
          <span class="h-6 leading-6">Accept</span>
        </button>
        <div class="dropdown">
          <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print Quote" id="print-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
              </path>
            </svg>
          </button>
          <div class="dropdown-menu w-60">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadLink" target="_blank">Letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadLink" target="_blank">No Letterhead (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadAndStampLink" target="_blank">No letterhead & Signature (EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printStampWithoutLetterHeadLink" target="_blank">Signature with no letterhead(EN)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadLinkKh" target="_blank">Letterhead (KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadLinkKh" target="_blank">No Letterhead (KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadAndStampLinkKh" target="_blank">No letterhead & Signature (KH)</a>
              <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printStampWithoutLetterHeadLinkKh" target="_blank">Signature with no letterhead(KH)</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            <span>{{ product_insurance_name }}</span>
          </div>
          <div class="mt-2 text-xl text-center">INSURANCE QUOTATION</div>
        </div>
        <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
          <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
              Quotation No: <span>{{ dataDetail.quotation_no }}</span>
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
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">NAME LIST:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm mb-3 underline cursor-pointer btn-attach" @click="exportInsuredPersons">{{
              dataDetail.quotation?.insured_persons_count + ' persons as per list attached' }}</div>
          </div>
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
              <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
                Plan I (USD)
              </th>
              <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
                Plan II (USD)
              </th>
              <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
                Plan III (USD)
              </th>
              <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
                Plan IV (USD)
              </th>
              <th class="border p-2" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
                Plan V (USD)
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataDetail.standard_benefits" :key="'sd_bnf' + index">
              <td class="border p-2 text-center">{{ index + 1 }}</td>
              <td class="border p-2">{{ item?.name }}</td>
              <td class="border p-2">
                {{ item?.amount ? `${item?.amount} days` : "" }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
                {{ formatCurrency(item?.plan_1) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
                {{ formatCurrency(item?.plan_2) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
                {{ formatCurrency(item?.plan_3) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
                {{ formatCurrency(item?.plan_4) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
                {{ formatCurrency(item?.plan_5) }}
              </td>
            </tr>
            <tr v-if="dataDetail.standard_base_plan_amount">
              <td colspan="3" class="border p-2 text-right">
                {{ dataDetail.standard_base_plan_amount?.name }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
                {{
                  formatCurrency(dataDetail.standard_base_plan_amount?.plan_1)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
                {{
                  formatCurrency(dataDetail.standard_base_plan_amount?.plan_2)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
                {{
                  formatCurrency(dataDetail.standard_base_plan_amount?.plan_3)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
                {{
                  formatCurrency(dataDetail.standard_base_plan_amount?.plan_4)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
                {{
                  formatCurrency(dataDetail.standard_base_plan_amount?.plan_5)
                }}
              </td>
            </tr>
            <tr>
              <td colspan="3" class="border p-2 text-right">
                {{ dataDetail.standard_premium_per_person?.name }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_1 > 0">
                {{
                  formatCurrency(dataDetail.standard_premium_per_person?.plan_1)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_2 > 0">
                {{
                  formatCurrency(dataDetail.standard_premium_per_person?.plan_2)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_3 > 0">
                {{
                  formatCurrency(dataDetail.standard_premium_per_person?.plan_3)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_4 > 0">
                {{
                  formatCurrency(dataDetail.standard_premium_per_person?.plan_4)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.standard_total_premium?.plan_5 > 0">
                {{
                  formatCurrency(dataDetail.standard_premium_per_person?.plan_5)
                }}
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
              <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_1 > 0">
                Limit Plan I
              </th>
              <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_2 > 0">
                Limit Plan II
              </th>
              <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_3 > 0">
                Limit Plan III
              </th>
              <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_4 > 0">
                Limit Plan IV
              </th>
              <th class="border p-2" v-if="dataDetail.optional_total_premium?.plan_5 > 0">
                Limit Plan V
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataDetail.optional_benefits" :key="'opt_bnf' + index">
              <td class="border p-2 text-center">{{ index + 1 }}</td>
              <td class="border p-2">{{ item?.name }}</td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_1 > 0">
                {{ formatCurrency(item?.plan_1) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_2 > 0">
                {{ formatCurrency(item?.plan_2) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_3 > 0">
                {{ formatCurrency(item?.plan_3) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_4 > 0">
                {{ formatCurrency(item?.plan_4) }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_5 > 0">
                {{ formatCurrency(item?.plan_5) }}
              </td>
            </tr>
            <tr>
              <td colspan="2" class="border p-2 text-right">
                {{ dataDetail.optional_premium_per_person?.name }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_1 > 0">
                {{
                  formatCurrency(dataDetail.optional_premium_per_person?.plan_1)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_2 > 0">
                {{
                  formatCurrency(dataDetail.optional_premium_per_person?.plan_2)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_3 > 0">
                {{
                  formatCurrency(dataDetail.optional_premium_per_person?.plan_3)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_4 > 0">
                {{
                  formatCurrency(dataDetail.optional_premium_per_person?.plan_4)
                }}
              </td>
              <td class="border p-2 text-right" v-if="dataDetail.optional_total_premium?.plan_5 > 0">
                {{
                  formatCurrency(dataDetail.optional_premium_per_person?.plan_5)
                }}
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
              <th class="border p-2" v-if="
                dataDetail.additional_benefits &&
                someAdditionalBenefitsBiggerThanZero('plan_1')
              ">
                Limit Plan I
              </th>
              <th class="border p-2" v-if="
                dataDetail.additional_benefits &&
                someAdditionalBenefitsBiggerThanZero('plan_2')
              ">
                Limit Plan II
              </th>
              <th class="border p-2" v-if="
                dataDetail.additional_benefits &&
                someAdditionalBenefitsBiggerThanZero('plan_3')
              ">
                Limit Plan III
              </th>
              <th class="border p-2" v-if="
                dataDetail.additional_benefits &&
                someAdditionalBenefitsBiggerThanZero('plan_4')
              ">
                Limit Plan IV
              </th>
              <th class="border p-2" v-if="
                dataDetail.additional_benefits &&
                someAdditionalBenefitsBiggerThanZero('plan_5')
              ">
                Limit Plan V
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in dataDetail.additional_benefits" :key="'add_bnf' + index">
              <td class="border p-2 text-center">{{ index + 1 }}</td>
              <td class="border p-2">{{ item?.name }}</td>
              <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_1')">
                {{ formatCurrency(item?.plan_1) }}
              </td>
              <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_2')">
                {{ formatCurrency(item?.plan_2) }}
              </td>
              <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_3')">
                {{ formatCurrency(item?.plan_3) }}
              </td>
              <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_4')">
                {{ formatCurrency(item?.plan_4) }}
              </td>
              <td class="border p-2 text-right" v-if="someAdditionalBenefitsBiggerThanZero('plan_5')">
                {{ formatCurrency(item?.plan_5) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-5 sm:px-16 py-5" v-if="
        dataDetail.total_premium?.plan_1 > 0 ||
        dataDetail.total_premium?.plan_2 > 0 ||
        dataDetail.total_premium?.plan_3 > 0 ||
        dataDetail.total_premium?.plan_4 > 0 ||
        dataDetail.total_premium?.plan_5 > 0
      ">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">TOTAL PREMIUM (USD):</div>
          </div>
          <div class="w-2/3">
            <table class="w-full border">
              <thead>
                <tr class="bg-gray-300">
                  <th class="border p-2">Premium (USD)</th>
                  <th class="border p-2" v-if=" (dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium.plan_1 > 0) ">Plan I</th>
                  <th class="border p-2" v-if=" (dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium.plan_2 > 0)">Plan II</th>
                  <th class="border p-2" v-if=" (dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium.plan_3 > 0) ">Plan III</th>
                  <th class="border p-2" v-if=" (dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium.plan_4 > 0) ">Plan IV</th>
                  <th class="border p-2" v-if=" (dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium.plan_5 > 0) ">Plan V</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="border p-2 text-right">IPD Premium Per Person:</td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium.plan_1 > 0) ">
                    {{ dataDetail.standard_total_premium?.plan_1 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_1) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium.plan_2 > 0) ">
                    {{ dataDetail.standard_total_premium?.plan_2 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_2) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if="( dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium.plan_3 > 0) ">
                    {{ dataDetail.standard_total_premium?.plan_3 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_3) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if="( dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium.plan_4 > 0) ">
                    {{ dataDetail.standard_total_premium?.plan_4 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_4) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium.plan_5 > 0)">
                    {{ dataDetail.standard_total_premium?.plan_5 > 0 ?
                      formatCurrency(dataDetail.standard_premium_per_person?.plan_5) : '' }}
                  </td>
                </tr>
                <tr v-if="totalOptBenefit > 0">
                  <td class="border p-2 text-right">Optional Extensions Premium Per Person:</td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium.plan_1 > 0) ">
                    {{ dataDetail.optional_total_premium?.plan_1 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_1) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium.plan_2 > 0) ">
                    {{ dataDetail.optional_total_premium?.plan_2 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_2) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium.plan_3 > 0) ">
                    {{ dataDetail.optional_total_premium?.plan_3 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_3) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium.plan_4 > 0) ">
                    {{ dataDetail.optional_total_premium?.plan_4 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_4) : '' }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium.plan_5 > 0) ">
                    {{ dataDetail.optional_total_premium?.plan_5 > 0 ?
                      formatCurrency(dataDetail.optional_premium_per_person?.plan_5) : '' }}
                  </td>
                </tr>

                <tr>
                  <td class="border p-2 text-right">
                    {{ dataDetail.premium?.name }}
                  </td>
                  <!-- total standard -->
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium.plan_1 > 0) ">
                    {{ formatCurrency(dataDetail.premium?.plan_1) }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium.plan_2 > 0) ">
                    {{ formatCurrency(dataDetail.premium?.plan_2) }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium.plan_3 > 0) ">
                    {{ formatCurrency(dataDetail.premium?.plan_3) }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium.plan_4 > 0) ">
                    {{ formatCurrency(dataDetail.premium?.plan_4) }}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium.plan_5 > 0) ">
                    {{ formatCurrency(dataDetail.premium?.plan_5) }}
                  </td>
                </tr>
                <tr>
                  <td class="border p-2 text-right">
                    {{ dataDetail.total_premium?.name }}
                  </td>
                  <!-- total standard -->
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_1 > 0 || dataDetail.optional_total_premium.plan_1 > 0) ">
                    {{formatCurrency(dataDetail.total_premium?.plan_1)}}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_2 > 0 || dataDetail.optional_total_premium.plan_2 > 0) ">
                    {{formatCurrency(dataDetail.total_premium?.plan_2 )}}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_3 > 0 || dataDetail.optional_total_premium.plan_3 > 0) ">
                    {{formatCurrency(dataDetail.total_premium?.plan_3)}}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_4 > 0 || dataDetail.optional_total_premium.plan_4 > 0) ">
                    {{formatCurrency(dataDetail.total_premium?.plan_4)}}
                  </td>
                  <td class="border p-2 text-right" v-if=" (dataDetail.standard_total_premium.plan_5 > 0 || dataDetail.optional_total_premium.plan_5 > 0) ">
                    {{formatCurrency(dataDetail.total_premium?.plan_5)}}
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
              {{ formatCurrency(grandTotalPremium) }}
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
            <div class="text-md font-bold mb-3">QUOTATION VALIDITY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md mb-3">{{ dataDetail?.quotation_validity }}</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED ON:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ dataDetail.issued_on }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED By:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ dataDetail.issued_by }}
            </div>
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
              <img v-if="signature" class="img-over" :src="'/' + signature.file_url" alt="" style="max-height: 150px" />
              <img v-if="signature" class="img-under" src="/images/stamp/phillip_insurance.png" alt=""
                style="max-height: 150px" />
            </div>

            <hr class="my-3" />

            <div class="text-md mb-3 font-medium">Authorised Signature</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3"></div>
          <div class="w-2/3">
            <div class="text-md mb-3 font-bold pt-1" style="
                text-decoration-line: underline;
                text-decoration-style: double;
              ">
              ACCEPTANCE BY CLIENT:
            </div>
            <div class="text-md font-medium mb-3">
              We examine and understand the above terms and premium payment. We
              hereby accept and agree to the terms to issue the Policy with an
              effective on ...................................................
            </div>
          </div>
        </div>
        <br />
        <div class="flex mt-12">
          <div class="w-1/3"></div>
          <div class="w-2/3">
            <div class="text-md font-medium pt-1 border-t border-gray-200">
              Authorised Signature (Company Stamp if Applicable)
            </div>
          </div>
        </div>
      </div>
    </div>
    <ApproveDialog :isVisible="showDialog" :header="`Quote ${approvalType == 'APPROVE' ? 'Approval' : 'Acceptance'}`"
      :submitted="submitted" :options="getStatusOptions" :loading="submittingApproval" :value="approvalType == 'APPROVE' ? 'APV' : 'ACP'" @hideDialog="hideDialog"
      @confirm="handleApproval" />
  </div>
</template>

<script>
import ApproveDialog from "./ApproveDialog.vue";
import quotationService from "@/services/hs/quotation.service";
import { hasPermission } from "@/services/auth.service";
import errorRoutes from "@/router/errorRoutes.js";

export default {
  components: {
    ApproveDialog,
  },

  data() {
    return {
      id: this.$route.params.id ?? null,
      dataDetail: {},
      approvalType: "",
      submittingApproval: false,
      showDialog: false,
      submitted: false,
      signature: null,
    };
  },
  computed: {
    getStatusOptions() {
      if (this.approvalType === 'APPROVE') {
        return [
          { value: 'APV', label: 'Approve' },
          { value: 'REJ', label: 'Reject' }
        ]
      } else {
        return [
          { value: 'ACP', label: 'Accept' },
          { value: 'REJ', label: 'Reject' }
        ]
      }
    },
    grandTotalPremium(){
      let totalPremium = this.dataDetail?.total_premium;
      return totalPremium ? (parseFloat(totalPremium.plan_1) + parseFloat(totalPremium.plan_2) + parseFloat(totalPremium.plan_3) + parseFloat(totalPremium.plan_4) + parseFloat(totalPremium.plan_5)) : 0;
    },
    totalOptBenefit() {
      let optPremium = this.dataDetail?.optional_total_premium;
      return !optPremium
        ? 0
        : parseFloat(optPremium?.plan_1) +
        parseFloat(optPremium?.plan_2) +
        parseFloat(optPremium?.plan_3) +
        parseFloat(optPremium?.plan_4) +
        parseFloat(optPremium?.plan_5);
    },
    totalAddiBenefit() {
      let addiBenefits = this.dataDetail?.additional_benefits;
      return addiBenefits
        ? addiBenefits.reduce(
          (acc, o) =>
            acc +
            (parseFloat(o.plan_1 ?? 0) +
              parseFloat(o.plan_2 ?? 0) +
              parseFloat(o.plan_3 ?? 0) +
              parseFloat(o.plan_4 ?? 0) +
              parseFloat(o.plan_5 ?? 0)),
          0
        )
        : 0;
    },
    canApprove() {
      let canApprovePermission = hasPermission("HS_QUOTATION", "APPROVE");
      if (!canApprovePermission) return false;
      return this.dataDetail.quotation?.approved_status === "PND";
    },
    canAccept() {
      let canAcceptPermission = hasPermission("HS_QUOTATION", "ACCEPT");
      if (!canAcceptPermission) return false;

      return this.dataDetail.quotation?.accepted_status === "PND";
    },
    printWithLetterHeadLink() {
      return `/hs/${this.id}/download-quotation/en?letterhead=1`;
    },
    printWithoutLetterHeadLink() {
      return `/hs/${this.id}/download-quotation/en?letterhead=0`;
    },
    printWithoutLetterHeadAndStampLink() {
      return `/hs/${this.id}/download-quotation/en?letterhead=0&noStamp=1`;
    },
    printStampWithoutLetterHeadLink() {
      return `/hs/${this.id}/download-quotation/en?letterhead=0&noStamp=0`;
    },
    printWithLetterHeadLinkKh() {
      return `/hs/${this.id}/download-quotation/km?letterhead=1`;
    },
    printWithoutLetterHeadLinkKh() {
      return `/hs/${this.id}/download-quotation/km?letterhead=0`;
    },
    printWithoutLetterHeadAndStampLinkKh() {
      return `/hs/${this.id}/download-quotation/km?letterhead=0&noStamp=1`;
    },
    printStampWithoutLetterHeadLinkKh() {
      return `/hs/${this.id}/download-quotation/km?letterhead=0&noStamp=0`;
    },
    canProceedToPolicy() {
      if (!this.dataDetail.quotation) return false;
      // If already proceed to policy
      if (this.dataDetail.quotation.policy) return false;
      // If accepted
      return this.dataDetail.quotation.accepted_status === "ACP";
    },
    product_insurance_name() {
      return this.dataDetail.product ? this.dataDetail.product.name : null;
    },
  },
  methods: {
    exportInsuredPersons() {
      location.href = '/hs/quotations/export-insured-persons/' + this.dataDetail.quotation?.data_id + '/' + this.dataDetail.quotation_no
    },
    getDetail() {
      quotationService
        .detail(this.id)
        .then((res) => {
          this.dataDetail = res.data;
          this.signature = res.data.signature;
        })
        .catch((err) => {
          notify('Error', 'error','bottom-right');
        });
    },
    openDialog(type) {
      this.showDialog = true;
      this.submitted = false;

      this.approvalType = type;
    },
    hideDialog() {
      this.showDialog = false;
      this.submitted = false;
      this.approvalType = "";
    },
    async handleApproval(formData) {
      this.submitted = true;

      if (!formData.status || !formData.reason) return;

      this.submittingApproval = true;

      try {
        let res = null;

        if (this.approvalType === "APPROVE") {
          res = await quotationService.approve(formData, this.id);
        }
        if (this.approvalType === "ACCEPT") {
          res = await quotationService.accept(formData, this.id);
        }

        if (!res) return;

        if (res.data?.success) {
          notify('Success', 'success','bottom-right');

          this.$router.push({ name: "HSQuotationIndex" });
        }
      } catch (err) {
        if(err?.response?.status === 403) {
          notify('Maker can not approve their own records.','error');
        }else{
          notify('Error', 'error','bottom-right');
        }
      }
      this.submittingApproval = false;
    },
    proceedToPolicy() {
      this.$confirm.require({
        message: "Do you want to proceed to policy?",
        header: "Proceed to Policy",
        icon: "pi pi-info-circle",
        acceptLabel: "Proceed",
        rejectLabel: "Cancel",
        acceptClass: "btn btn-primary w-24",
        rejectClass: "btn btn-outline-danger w-24 mr-1",
        blockScroll: false,
        accept: () => {
          axios
            .post(`/hs/proceed-to-policy/${this.dataDetail.quotation.id}`)
            .then((response) => {
              if (response.data.success) {
                notify('Success', 'success','bottom-right');
                this.$router.push({ name: "HSPolicyIndex" });
              } else if (!response.data.success) {
                notify('Error', 'error','bottom-right');
              }
            })
            .catch((err) => {
              notify('Error', 'error','bottom-right');
            });
        },
      });
    },
    someAdditionalBenefitsBiggerThanZero(key) {
      const benefits = this.dataDetail.additional_benefits.some(
        (benefit) => benefit[key] > 0
      );
      return benefits;
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
    this.getDetail();
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