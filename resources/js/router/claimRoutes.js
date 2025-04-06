import CauseOfLossIndex from '@/views/ClaimConfiguration/CauseOfLoss/Index.vue'
import CauseOfLossForm from '@/views/ClaimConfiguration/CauseOfLoss/Form.vue'
import CauseOfLossDetail from '@/views/ClaimConfiguration/CauseOfLoss/Detail.vue'

import DriverLicenseIndex from '@/views/ClaimConfiguration/DriverLicense/Index.vue'
import DriverLicenseForm from '@/views/ClaimConfiguration/DriverLicense/Form.vue'
import DriverLicenseDetail from '@/views/ClaimConfiguration/DriverLicense/Detail.vue'

import AdjusterCompanyIndex from '@/views/ClaimConfiguration/AdjusterCompany/Index.vue'
import AdjusterCompanyForm from '@/views/ClaimConfiguration/AdjusterCompany/Form.vue'
import AdjusterCompanyDetail from '@/views/ClaimConfiguration/AdjusterCompany/Detail.vue'

import PayeeIndex from '@/views/ClaimConfiguration/Payee/Index.vue'
import PayeeForm from '@/views/ClaimConfiguration/Payee/Form.vue'
import PayeeDetail from '@/views/ClaimConfiguration/Payee/Detail.vue'

import ThirdPartyIndex from '@/views/ClaimConfiguration/ThirdParty/Index.vue'
import ThirdPartyForm from '@/views/ClaimConfiguration/ThirdParty/Form.vue'
import ThirdPartyDetail from '@/views/ClaimConfiguration/ThirdParty/Detail.vue'

import ServiceProviderIndex from '@/views/ClaimConfiguration/ServiceProvider/Index.vue'
import ServiceProviderForm from '@/views/ClaimConfiguration/ServiceProvider/Form.vue'
import ServiceProviderDetail from '@/views/ClaimConfiguration/ServiceProvider/Detail.vue'

import ClaimRegisterIndex from '@/views/Claim/Register/Index.vue'
import ClaimRegisterForm from '@/views/Claim/Register/Form.vue'
import ClaimRegisterDetail from '@/views/Claim/Register/Detail.vue'

import PartialPaymentIndex from '@/views/Claim/PartialPayment/Index.vue'
import PartialPaymentForm from '@/views/Claim/PartialPayment/Form.vue'
import PartialPaymentDetail from '@/views/Claim/PartialPayment/Detail.vue'

import ClaimProcessIndex from '@/views/Claim/Process/Index.vue'
import ClaimProcessForm from '@/views/Claim/Process/Form.vue'
import ClaimProcessDetail from '@/views/Claim/Process/Detail.vue'

import ClaimRecoveryIndex from '@/views/Claim/Recovery/Index.vue'
import ClaimRecoveryForm from '@/views/Claim/Recovery/Form.vue'
import ClaimRecoveryDetail from '@/views/Claim/Recovery/Detail.vue'

export default [
  {
    path: '/claim-configuration/cause-of-losses',
    name: 'CauseOfLossIndex',
    component: CauseOfLossIndex,
    meta: { code: 'CAUSE_OF_LOSE', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/cause-of-losses/new',
    name: 'CauseOfLossCreate',
    component: CauseOfLossForm,
    meta: { code: 'CAUSE_OF_LOSE', permission: 'NEW' },
  },
  {
    path: '/claim-configuration/cause-of-losses/:id/edit',
    name: 'CauseOfLossEdit',
    component: CauseOfLossForm,
    meta: { code: 'CAUSE_OF_LOSE', permission: 'UPDATE' },
  },
  {
    path: '/claim-configuration/cause-of-losses/:id',
    name: 'CauseOfLossDetail',
    component: CauseOfLossDetail,
    meta: { code: 'CAUSE_OF_LOSE', permission: 'VIEW' },
  },

  {
    path: '/claim-configuration/driver-licenses',
    name: 'DriverLicenseIndex',
    component: DriverLicenseIndex,
    meta: { code: 'DRIVER_LICENSE', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/driver-licenses/new',
    name: 'DriverLicenseCreate',
    component: DriverLicenseForm,
    meta: { code: 'DRIVER_LICENSE', permission: 'NEW' },
  },
  {
    path: '/claim-configuration/driver-licenses/:id/edit',
    name: 'DriverLicenseEdit',
    component: DriverLicenseForm,
    meta: { code: 'DRIVER_LICENSE', permission: 'UPDATE' },
  },
  {
    path: '/claim-configuration/driver-licenses/:id',
    name: 'DriverLicenseDetail',
    component: DriverLicenseDetail,
    meta: { code: 'DRIVER_LICENSE', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/adjuster-companies',
    name: 'AdjusterCompanyIndex',
    component: AdjusterCompanyIndex,
    meta: { code: 'ADJUSTER_COMPANY', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/adjuster-companies/new',
    name: 'AdjusterCompanyCreate',
    component: AdjusterCompanyForm,
    meta: { code: 'ADJUSTER_COMPANY', permission: 'NEW' },
  },
  {
    path: '/claim-configuration/adjuster-companies/:id/edit',
    name: 'AdjusterCompanyEdit',
    component: AdjusterCompanyForm,
    meta: { code: 'ADJUSTER_COMPANY', permission: 'UPDATE' },
  },
  {
    path: '/claim-configuration/adjuster-companies/:id',
    name: 'AdjusterCompanyDetail',
    component: AdjusterCompanyDetail,
    meta: { code: 'ADJUSTER_COMPANY', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/payees',
    name: 'PayeeIndex',
    component: PayeeIndex,
    meta: { code: 'PAYEE', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/payees/new',
    name: 'PayeeCreate',
    component: PayeeForm,
    meta: { code: 'PAYEE', permission: 'NEW' },
  },
  {
    path: '/claim-configuration/payees/:id/edit',
    name: 'PayeeEdit',
    component: PayeeForm,
    meta: { code: 'PAYEE', permission: 'UPDATE' },
  },
  {
    path: '/claim-configuration/payees/:id',
    name: 'PayeeDetail',
    component: PayeeDetail,
    meta: { code: 'PAYEE', permission: 'VIEW' },
  },

  {
    path: '/claim-configuration/third-parties',
    name: 'ThirdPartyIndex',
    component: ThirdPartyIndex,
    meta: { code: 'THIRD_PARTY', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/third-parties/new',
    name: 'ThirdPartyCreate',
    component: ThirdPartyForm,
    meta: { code: 'THIRD_PARTY', permission: 'NEW' },
  },
  {
    path: '/claim-configuration/third-parties/:id/edit',
    name: 'ThirdPartyEdit',
    component: ThirdPartyForm,
    meta: { code: 'THIRD_PARTY', permission: 'UPDATE' },
  },
  {
    path: '/claim-configuration/third-parties/:id',
    name: 'ThirdPartyDetail',
    component: ThirdPartyDetail,
    meta: { code: 'THIRD_PARTY', permission: 'VIEW' },
  },

  {
    path: '/claim-configuration/service-providers',
    name: 'ServiceProviderIndex',
    component: ServiceProviderIndex,
    meta: { code: 'SERVICE_PROVIDER', permission: 'VIEW' },
  },
  {
    path: '/claim-configuration/service-providers/new',
    name: 'ServiceProviderCreate',
    component: ServiceProviderForm,
    meta: { code: 'SERVICE_PROVIDER', permission: 'NEW' },
  },
  {
    path: '/claim-configuration/service-providers/:id/edit',
    name: 'ServiceProviderEdit',
    component: ServiceProviderForm,
    meta: { code: 'SERVICE_PROVIDER', permission: 'UPDATE' },
  },
  {
    path: '/claim-configuration/service-providers/:id',
    name: 'ServiceProviderDetail',
    component: ServiceProviderDetail,
    meta: { code: 'SERVICE_PROVIDER', permission: 'VIEW' },
  },

  {
    path: '/claim/auto/registers',
    name: 'ClaimRegisterIndex',
    component: ClaimRegisterIndex,
    meta: { code: 'CLAIM_REGISTER', permission: 'VIEW' },
  },
  {
    path: '/claim/auto/registers/new',
    name: 'ClaimRegisterCreate',
    component: ClaimRegisterForm,
    meta: { code: 'CLAIM_REGISTER', permission: 'NEW' },
  },
  {
    path: '/claim/auto/registers/:id/edit',
    name: 'ClaimRegisterEdit',
    component: ClaimRegisterForm,
    meta: { code: 'CLAIM_REGISTER', permission: 'UPDATE' },
  },
  {
    path: '/claim/auto/registers/:id',
    name: 'ClaimRegisterDetail',
    component: ClaimRegisterDetail,
    meta: { code: 'CLAIM_REGISTER', permission: 'VIEW' },
  },

  {
    path: '/claim/auto/partial-payments',
    name: 'PartialPaymentIndex',
    component: PartialPaymentIndex,
    meta: { code: 'CLAIM_PARTIAL_PAYMENT', permission: 'VIEW' },
  },
  {
    path: '/claim/auto/partial-payments/new',
    name: 'PartialPaymentCreate',
    component: PartialPaymentForm,
    meta: { code: 'CLAIM_PARTIAL_PAYMENT', permission: 'NEW' },
  },
  {
    path: '/claim/auto/partial-payments/:id/edit',
    name: 'PartialPaymentEdit',
    component: PartialPaymentForm,
    meta: { code: 'CLAIM_PARTIAL_PAYMENT', permission: 'UPDATE' },
  },
  {
    path: '/claim/auto/partial-payments/:id',
    name: 'PartialPaymentDetail',
    component: PartialPaymentDetail,
    meta: { code: 'CLAIM_PARTIAL_PAYMENT', permission: 'VIEW' },
  },

  {
    path: '/claim/auto/processes',
    name: 'ClaimProcessIndex',
    component: ClaimProcessIndex,
    meta: { code: 'CLAIM_PROCESS', permission: 'VIEW' },
  },
  {
    path: '/claim/auto/processes/new',
    name: 'ClaimProcessCreate',
    component: ClaimProcessForm,
    meta: { code: 'CLAIM_PROCESS', permission: 'NEW' },
  },
  {
    path: '/claim/auto/processes/:id/edit',
    name: 'ClaimProcessEdit',
    component: ClaimProcessForm,
    meta: { code: 'CLAIM_PROCESS', permission: 'UPDATE' },
  },
  {
    path: '/claim/auto/processes/:id',
    name: 'ClaimProcessDetail',
    component: ClaimProcessDetail,
    meta: { code: 'CLAIM_PROCESS', permission: 'VIEW' },
  },

  {
    path: '/claim/auto/recoveries',
    name: 'ClaimRecoveryIndex',
    component: ClaimRecoveryIndex,
    meta: { code: 'CLAIM_RECOVERY', permission: 'VIEW' },
  },
  {
    path: '/claim/auto/recoveries/:id/edit',
    name: 'ClaimRecoveryEdit',
    component: ClaimRecoveryForm,
    meta: { code: 'CLAIM_RECOVERY', permission: 'UPDATE' },
  },
  {
    path: '/claim/auto/recoveries/:id',
    name: 'ClaimRecoveryDetail',
    component: ClaimRecoveryDetail,
    meta: { code: 'CLAIM_RECOVERY', permission: 'VIEW' },
  },
]