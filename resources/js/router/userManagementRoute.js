// Block User Router
import UserIndex from '@/views/UsersManagement/Users/Index.vue'
import UserForm from '@/views/UsersManagement/Users/Form.vue'
import UserDetail from '@/views/UsersManagement/Users/Detail.vue'

// Block Group Router
import GroupIndex from '@/views/UsersManagement/Groups/Index.vue'
import GroupForm from '@/views/UsersManagement/Groups/Form.vue'
import GroupDetail from '@/views/UsersManagement/Groups/Detail.vue'

// Block Applications Router
import ApplicationIndex from '@/views/UsersManagement/Applications/Index.vue'
import ApplicationForm from '@/views/UsersManagement/Applications/Form.vue'
import ApplicationDetail from '@/views/UsersManagement/Applications/Detail.vue'

// Block Function Router
import FunctionIndex from '@/views/UsersManagement/Functions/Index.vue'
import FunctionForm from '@/views/UsersManagement/Functions/Form.vue'
import FunctionDetail from '@/views/UsersManagement/Functions/Detail.vue'

//Role Management
import RoleIndex from '@/views/UsersManagement/Role/Index.vue'
import RoleForm from '@/views/UsersManagement/Role/Form.vue'
import RoleDetail from '@/views/UsersManagement/Role/Detail.vue'

export default [
    // Block User
    {
        path: '/user-management/users',
        name: 'users',
        component: UserIndex,
        meta: { code: 'USER', permission: 'VIEW' },
    },
    {
        path: '/user-management/users/detail/:id',
        name: 'user-detail',
        component: UserDetail,
        meta: { code: 'USER', permission: 'VIEW' },
    },
    {

        path: '/user-management/users/:id/edit',
        name: 'user-edit',
        component: UserForm,
        meta: { code: 'USER', permission: 'UPD' },
    },
    {
        path: '/user-management/users/new',
        name: 'user-create',
        component: UserForm,
        meta: { code: 'USER', permission: 'NEW' },
    },

    // Block Applictions 
    {
        path: '/user-management/applications',
        name: 'ApplicationIndex',
        component: ApplicationIndex,
        meta: { code: 'APPLICATION', permission: 'VIEW' },
    },
    {
        path: '/user-management/applications/new',
        name: 'ApplicationCreate',
        component: ApplicationForm,
        meta: { code: 'APPLICATION', permission: 'NEW' },
    },
    {
        path: '/user-management/applications/:id/edit',
        name: 'ApplicationUpdate',
        component: ApplicationForm,
        meta: { code: 'APPLICATION', permission: 'UPD' },
    },
    {
        path: '/user-management/applications/:id',
        name: 'ApplicationDetail',
        component: ApplicationDetail,
        meta: { code: 'APPLICATION', permission: 'VIEW' },
    },

    // Block Function 
    {
        path: '/user-management/functions',
        name: 'FunctionIndex',
        component: FunctionIndex,
        meta: { code: 'FUNCTION', permission: 'VIEW' },
    },
    {
        path: '/user-management/functions/new',
        name: 'FunctionCreate',
        component: FunctionForm,
        meta: { code: 'FUNCTION', permission: 'NEW' },
    },
    {
        path: '/user-management/functions/:id/edit',
        name: 'FunctionUpdate',
        component: FunctionForm,
        meta: { code: 'FUNCTION', permission: 'UPD' },
    },
    {
        path: '/user-management/functions/:id',
        name: 'FunctionDetail',
        component: FunctionDetail,
        meta: { code: 'FUNCTION', permission: 'VIEW' },
    },

    // Block Group
    {
        path: '/user-management/groups',
        name: 'GroupIndex',
        component: GroupIndex,
        meta: { code: 'GROUP', permission: 'VIEW' },
    },
    {
        path: '/user-management/groups/new',
        name: 'GroupCreate',
        component: GroupForm,
        meta: { code: 'GROUP', permission: 'NEW' },
    },
    {
        path: '/user-management/groups/:id/edit',
        name: 'GroupUpdate',
        component: GroupForm,
        meta: { code: 'GROUP', permission: 'UPD' },
    },
    {
        path: '/user-management/groups/:id',
        name: 'GroupDetail',
        component: GroupDetail,
        meta: { code: 'GROUP', permission: 'VIEW' },
    },
    // Role
    {
        path: '/user-management/roles',
        name: 'RoleIndex',
        component: RoleIndex,
        meta: { code: 'ROLE', permission: 'VIEW' },
    },
    {
        path: '/user-management/roles/new',
        name: 'RoleCreate',
        component: RoleForm,
        meta: { code: 'ROLE', permission: 'NEW' },
    },
    {
        path: '/user-management/roles/:id/edit',
        name: 'RoleUpdate',
        component: RoleForm,
        meta: { code: 'ROLE', permission: 'UPD' },
    },
    {
        path: '/user-management/roles/:id',
        name: 'RoleDetail',
        component: RoleDetail,
        meta: { code: 'ROLE', permission: 'VIEW' },
    },
];