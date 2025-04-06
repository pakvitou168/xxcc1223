import PAQuotationIndex from '@/views/Quotation/PA/Index.vue'
import PAQuotationForm from '@/views/Quotation/PA/Form.vue'
import PAQuotationDetail from '@/views/Quotation/PA/Detail.vue'

import PAPolicyIndex from "@/views/Policy/PA/Index.vue"
import PAPolicyForm from "@/views/Policy/PA/Form.vue"
import PAPolicyDetail from "@/views/Policy/PA/Detail.vue"

import ExtensionIndex from '@/views/ProductConfiguration/PaExtension/Index.vue';
import ExtensionForm from '@/views/ProductConfiguration/PaExtension/Form.vue';
import ExtensionDetail from '@/views/ProductConfiguration/PaExtension/Detail.vue';

import PAEndorsementIndex from "@/views/Endorsement/PA/Index.vue"
import PAEndorsementForm from "@/views/Endorsement/PA/Form.vue"
import PAEndorsementDetail from "@/views/Endorsement/PA/Detail.vue"

export default [
    //quotation
    {
        name: 'PAQuotationIndex',
        path: '/quotation/pa',
        component: PAQuotationIndex,
        meta: { code: 'PA_QUOTATION', permission: 'VIEW' }
    },
    {
        name: 'PAQuotationCreate',
        path: '/quotation/pa/create',
        component: PAQuotationForm,
        meta: { code: 'PA_QUOTATION', permission: 'NEW' }
    },
    {
        name: 'PAQuotationEdit',
        path: '/quotation/pa/:id/edit',
        component: PAQuotationForm,
        meta: { code: 'PA_QUOTATION', permission: 'UPDATE' }
    },
    {
        name: 'PAQuotationDetail',
        path: '/quotation/pa/:id',
        component: PAQuotationDetail,
        meta: { code: 'PA_QUOTATION', permission: 'VIEW' }
    },
    //policy
    {
        name: 'PAPolicyIndex',
        path: '/policies/pa',
        component: PAPolicyIndex
    },
    {
        name: 'PAPolicyCreate',
        path: '/policies/pa/create',
        component: PAPolicyForm
    },
    {
        name: 'PAPolicyEdit',
        path: '/policies/pa/:id/edit',
        component: PAPolicyForm
    },
    {
        name: 'PAPolicyDetail',
        path: '/policies/pa/:id',
        component: PAPolicyDetail
    },
    //PA extensions
    {
        path: '/product-configuration/pa-extensions',
        name: 'ProductConditionIndex',
        component: ExtensionIndex,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'VIEW' }
    },
    {
        path: '/product-configuration/pa-extensions/new',
        name: 'ProductConditionCreate',
        component: ExtensionForm,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'NEW' }
    },
    {
        path: '/product-configuration/pa-extensions/:id/edit',
        name: 'ProductConditionUpdate',
        component: ExtensionForm,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'UPDATE' }
    },
    {
        path: '/product-configuration/pa-extensions/:id',
        name: 'ProductConditionDetail',
        component: ExtensionDetail,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'VIEW' }
    },
    //endorsement
    {
        name: 'PAEndorsementIndex',
        path: '/endorsements/pa',
        component: PAEndorsementIndex,
        meta: { code: 'PA_ENDORSEMENT', permission: 'VIEW' }
    },
    {
        name: 'PAEndorsementEdit',
        path: '/endorsements/pa/:id/edit',
        component: PAEndorsementForm,
        meta: { code: 'PA_ENDORSEMENT', permission: 'UPDATE' }
    },
    {
        name: 'PAEndorsementDetail',
        path: '/endorsements/pa/:id',
        component: PAEndorsementDetail,
        meta: { code: 'PA_ENDORSEMENT', permission: 'VIEW' }
    },
]
