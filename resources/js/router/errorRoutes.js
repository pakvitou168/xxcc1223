import Error404 from '../components/ErrorViews/404.vue'
import Error403 from '../components/ErrorViews/403.vue'

export default [
    {
        path: '/403',
        name: 'Error403',
        component: Error403,
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'Error404',
        component: Error404,
    },
]