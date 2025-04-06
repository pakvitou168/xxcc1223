import SMUserIndex from '@/views/SecurityManagement/User/Index.vue';
import SMRoleIndex from '@/views/SecurityManagement/Role/Index.vue';
import SMAppIndex from '@/views/SecurityManagement/Application/Index.vue';
import SMFncIndex from '@/views/SecurityManagement/Function/Index.vue';
import SMGroupIndex from '@/views/SecurityManagement/Group/Index.vue';

export default [
  // Users routes
  {
    path: '/security-management/users',
    name: 'SMUserIndex',
    component: SMUserIndex,
    // meta: { code: 'USER', permission: 'VIEW' },
  },
  {
    path: '/security-management/users/:id',
    name: 'SMUserDetail',
    component: SMUserIndex,
    // meta: { code: 'USER', permission: 'VIEW' },
  },

  // Roles routes
  {
    path: '/security-management/roles',
    name: 'SMRoleIndex',
    component: SMRoleIndex,
    // meta: { code: 'ROLE', permission: 'VIEW' },
  },
  {
    path: '/security-management/roles/:id',
    name: 'SMRoleDetail',
    component: SMRoleIndex,
    // meta: { code: 'ROLE', permission: 'VIEW' },
  },
  // Application routes
  {
    path: '/security-management/applications',
    name: 'SMAppIndex',
    component: SMAppIndex,
    // meta: { code: 'ROLE', permission: 'VIEW' },
  },
  {
    path: '/security-management/applications/:id',
    name: 'SMAppDetail',
    component: SMAppIndex,
    // meta: { code: 'ROLE', permission: 'UPDATE' },
  },
  // Function routes
  {
    path: '/security-management/functions',
    name: 'SMFncIndex',
    component: SMFncIndex,
    // meta: { code: 'FUNCTION', permission: 'VIEW' },
  },
  {
    path: '/security-management/functions/:id',
    name: 'SMFncDetail',
    component: SMFncIndex,
    // meta: { code: 'FUNCTION', permission: 'VIEW' },
  },
  //Group 
  {
    path: '/security-management/groups',
    name: 'SMGroupIndex',
    component: SMGroupIndex,
    // meta: { code: 'ROLE', permission: 'VIEW' },
  },
  {
    path: '/security-management/groups/:id',
    name: 'SMGroupDetail',
    component: SMGroupIndex,
    // meta: { code: 'ROLE', permission: 'UPDATE' },
  }
]