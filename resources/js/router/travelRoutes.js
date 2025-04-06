import TravelQuotationIndex from '@/views/Quotation/Travel/Index.vue'
import TravelQuotationForm from '@/views/Quotation/Travel/Form.vue'
import TravelQuotationDetail from '@/views/Quotation/Travel/Detail.vue'
//Policy
import TravelPolicyIndex from '../views/Policy/Travel/Index.vue';
import TravelPolicyDetail from "@/views/Policy/Travel/Detail.vue"
import TravelPolicyEdit from "@/views/Policy/Travel/Form.vue"

//Claim
import ClaimRegisterIndex from '@/views/Claim/Travel/Register/Index.vue'
import ClaimRegisterForm from '@/views/Claim/Travel/Register/Form.vue'
import ClaimRegisterDetail from '@/views/Claim/Travel/Register/Detail.vue'

import TravelEndorsementIndex from '../views/Endorsement/Travel/Index.vue';
import TravelEndorsementDetail from '../views/Endorsement/Travel/Detail.vue';
import TravelEndorsementForm from '../views/Endorsement/Travel/Form.vue';
export default [
    //quotation
    {
        name: 'TravelQuotationIndex',
        path: '/quotation/travel',
        component: TravelQuotationIndex
    },
    {
        name: 'TravelQuotationCreate',
        path: '/quotation/travel/create',
        component: TravelQuotationForm
    },
    {
        name: 'TravelQuotationEdit',
        path: '/quotation/travel/:id/edit',
        component: TravelQuotationForm
    },
    {
        name: 'TravelQuotationDetail',
        path: '/quotation/travel/:id',
        component: TravelQuotationDetail
    },
    //policy
    {
        name: 'TravelPolicyIndex',
        path: '/policies/travel',
        component: TravelPolicyIndex,
        meta: { code: 'TV_POLICY', permission: 'VIEW' }
    },
    {
        name: 'TravelPolicyDetail',
        path: '/policies/travel/:id',
        component: TravelPolicyDetail,
        meta: { code: 'TV_POLICY', permission: 'VIEW' }
    },
    {
        name: 'TravelPolicyEdit',
        path: '/policies/travel/:id/edit',
        component: TravelPolicyEdit,
        meta: { code: 'TV_POLICY', permission: 'UPDATE' }
    },
    //claim
    {
        path: '/claim/travel/registers',
        name: 'TravelClaimRegisterIndex',
        component: ClaimRegisterIndex,
        meta: { code: 'CLAIM_REGISTER', permission: 'VIEW' },
      },
      {
        path: '/claim/travel/registers/new',
        name: 'TravelClaimRegisterCreate',
        component: ClaimRegisterForm,
        meta: { code: 'CLAIM_REGISTER', permission: 'NEW' },
      },
      {
        path: '/claim/travel/registers/:id/edit',
        name: 'TravelClaimRegisterEdit',
        component: ClaimRegisterForm,
        meta: { code: 'CLAIM_REGISTER', permission: 'UPD' },
      },
      {
        path: '/claim/travel/registers/:id',
        name: 'TravelClaimRegisterDetail',
        component: ClaimRegisterDetail,
        meta: { code: 'CLAIM_REGISTER', permission: 'VIEW' },
      },

    // Endorsements
    {
        path: '/endorsements/travel',
        name: 'TravelEndorsementIndex',
        component: TravelEndorsementIndex,
        meta: { code: 'TV_ENDORSEMENT', permission: 'VIEW' },
    },
    {
        path: '/endorsements/travel/:id',
        name: 'TravelEndorsementDetail',
        component: TravelEndorsementDetail,
        meta: { code: 'TV_ENDORSEMENT', permission: 'VIEW' },
    },
    {
        path: '/endorsements/travel/:id/edit',
        name: 'TravelEndorsementEdit',
        component: TravelEndorsementForm,
        meta: { code: 'TV_ENDORSEMENT', permission: 'UPDATE' },
    },

]
