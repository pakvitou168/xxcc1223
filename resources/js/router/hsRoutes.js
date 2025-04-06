
import HSQuotationIndex from '@/views/Quotation/HS/Index.vue'
import HSQuotationDetail from '@/views/Quotation/HS/Detail.vue'
// Policy
import HSPolicyIndex from '../views/Policy/HS/Index.vue';
import HSPolicyDetail from '../views/Policy/HS/Detail.vue';
import HSPolicyForm from '../views/Policy/HS/Form.vue';

// Endorsement
import HSEndorsementIndex from '../views/Endorsement/HS/Index.vue';
import HSEndorsementDetail from '../views/Endorsement/HS/Detail.vue';
import HSEndorsementForm from '../views/Endorsement/HS/Form.vue';
// Claim
import ClaimHSRegisterIndex from '@/views/Claim/HS/Register/Index.vue'
import ClaimHSRegisterForm from '@/views/Claim/HS/Register/Form.vue'
import ClaimHSRegisterDetail from '@/views/Claim/HS/Register/Detail.vue'
import ClaimHSRegisterSchema from '@/views/Claim/HS/Register/Schema.vue'
//Claim Payment
import ClaimHSPayment from '@/views/Claim/HS/Payment/Index.vue';
import ClaimHSPaymentForm from '@/views/Claim/HS/Payment/Form.vue'
import ClaimHSPaymentDetail from '@/views/Claim/HS/Payment/Detail.vue'

export default [
  {
    path: '/quotations/hs',
    name: 'HSQuotationIndex',
    component: HSQuotationIndex,
    meta: { code: 'HS_QUOTATION', permission: 'VIEW' },
  },
  {
    path: '/quotations/hs/:id',
    name: 'HSQuotationDetail',
    component: HSQuotationDetail,
    meta: { code: 'HS_QUOTATION', permission: 'VIEW' },
  },
  //Policy 
  {
    path: '/policies/hs',
    name: 'HSPolicyIndex',
    component: HSPolicyIndex,
    meta: { code: 'HS_POLICY', permission: 'VIEW' },
  },
  {
    path: '/policies/hs/:id/edit',
    name: 'HSPolicyEdit',
    component: HSPolicyForm,
    meta: { code: 'HS_POLICY', permission: 'UPDATE' },
  },
  {
    path: '/policies/hs/:id',
    name: 'HSPolicyDetail',
    component: HSPolicyDetail,
    meta: { code: 'HS_POLICY', permission: 'VIEW' },
  },

  // Endorsements
  {
    path: '/endorsements/hs',
    name: 'HSEndorsementIndex',
    component: HSEndorsementIndex,
    meta: { code: 'HS_ENDORSEMENT', permission: 'VIEW' },
  },
  {
    path: '/endorsements/hs/:id',
    name: 'HSEndorsementDetail',
    component: HSEndorsementDetail,
    meta: { code: 'HS_ENDORSEMENT', permission: 'VIEW' },
  },
  {
    path: '/endorsements/hs/:id/edit',
    name: 'HSEndorsementEdit',
    component: HSEndorsementForm,
    meta: { code: 'HS_ENDORSEMENT', permission: 'VIEW' },
  },
  {
    path: '/claim/hs/registers',
    name: 'ClaimHSRegisterIndex',
    component: ClaimHSRegisterIndex,
    meta: { code: 'HS_CLAIM_REGISTER', permission: 'VIEW' },
  },
  {
    path: '/claim/hs/registers/new',
    name: 'ClaimHSRegisterCreate',
    component: ClaimHSRegisterForm,
    meta: { code: 'HS_CLAIM_REGISTER', permission: 'NEW' },
  },
  {
    path: '/claim/hs/registers/:id',
    name: 'ClaimHSRegisterDetail',
    component: ClaimHSRegisterDetail,
    meta: { code: 'HS_CLAIM_REGISTER', permission: 'VIEW' },
  },
  {
    path: '/claim/hs/registers/:id/edit',
    name: 'ClaimHSRegisterEdit',
    component: ClaimHSRegisterForm,
    meta: { code: 'HS_CLAIM_REGISTER', permission: 'UPDATE' },
  },
  {
    path:'/claim/hs/registers/:id/schema',
    name:'ClaimHSRegisterSchema',
    component:ClaimHSRegisterSchema,
    meta:{code:'HS_CLAIM_REGISTER',permission:'VIEW'}
  },
  //Claim Payments 
  {
    path: '/claim/hs/payments',
    name: 'ClaimHSPaymentIndex',
    component: ClaimHSPayment,
    meta: { code: 'HS_CLAIM_PAYMENT', permission: 'VIEW' }
  },
  {
    path: '/claim/hs/payments/new',
    name: 'ClaimHSPaymentCreate',
    component: ClaimHSPaymentForm,
    meta: { code: 'HS_CLAIM_PAYMENT', permission: 'NEW' },
  },
  {
    path: '/claim/hs/payments/:id',
    name: 'ClaimHSPaymentDetail',
    component: ClaimHSPaymentDetail,
    meta: { code: 'HS_CLAIM_PAYMENT', permission: 'VIEW' },
  },
  {
    path: '/claim/hs/payments/:id/edit',
    name: 'ClaimHSPaymentEdit',
    component: ClaimHSPaymentForm,
    meta: { code: 'HS_CLAIM_PAYMENT', permission: 'UPDATE' },
  },
]