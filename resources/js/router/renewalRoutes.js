import RenewalIndex from '@/views/Renewal/Index.vue'
import RenewalDetail from '@/views/Renewal/Detail.vue'
import RenewalForm from '@/views/Renewal/Form.vue'

export default [
  {
    path: '/renewals',
    name: 'RenewalIndex',
    component: RenewalIndex,
    meta: { code: 'RENEWAL', permission: 'VIEW' },
  },
  {
    path: '/renewals/:id',
    name: 'RenewalDetail',
    component: RenewalDetail,
    meta: { code: 'RENEWAL', permission: 'VIEW' },
  },
  {
    path: '/renewals/:id/edit',
    name: 'RenewalEdit',
    component: RenewalForm,
    meta: { code: 'RENEWAL', permission: 'UPDATE' },
  },
]