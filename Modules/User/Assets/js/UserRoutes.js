import AccountProfile from './components/Account/Profile.vue';

import AdminTable from './components/Admin/Table.vue';
import AdminCreate from './components/Admin/Create.vue';
import AdminEdit from './components/Admin/Edit.vue';

import AllUsersTable from './components/All/Table.vue';
import AllUsersForm from './components/All/Form.vue';

import RoleTable from './components/Role/Table.vue';
import RoleForm from './components/Role/Form.vue';

import ExpertTable from './components/Expert/Table.vue';
import ExpertView from './components/Expert/View.vue';
import ExpertEdit from './components/Expert/Edit.vue';

import TraderTable from './components/Trader/Table.vue';
import TraderView from './components/Trader/View.vue';

import InvoiceTable from './components/Inovice/Table.vue';

const locales = window.locales;

export default [
    // All User Routes
    {
        path: '/user/all-users',
        name: 'admin.user.all-users',
        component: AllUsersTable,
    },
    {
        path: '/user/all-users/create',
        name: 'admin.user.all-users.create',
        component: AllUsersForm,
        props: {
            locales,
            pageTitle: 'title.new-user',
        },
    },
    {
        path: '/user/all-users/:id/edit',
        name: 'admin.user.all-users.edit',
        component: AllUsersForm,
        props: {
            locales,
            pageTitle: 'title.edit-user',
        },
    },
    // Admin User Routes
    {
        path: '/user/admin-users',
        name: 'admin.user.admin-users',
        component: AdminTable,
    },
    {
        path: '/user/admin-users/create',
        name: 'admin.user.admin-users.create',
        component: AdminCreate,
        props: {
            locales,
            pageTitle: 'title.new-user',
        },
    },
    {
        path: '/user/admin-users/:id/edit',
        name: 'admin.user.admin-users.edit',
        component: AdminEdit,
        props: {
            locales,
            pageTitle: 'title.edit-user',
        },
    },
    // Expert Routes
    {
        path: '/user/experts',
        name: 'admin.user.experts.index',
        component: ExpertTable,
        props: {
            availableGroups: window.initialState.group.groups || [],
        },
    },
    {
        path: '/user/experts/:id',
        name: 'admin.user.experts.view',
        component: ExpertView,
    },
    {
        path: '/user/experts/:id/edit',
        name: 'admin.user.experts.edit',
        component: ExpertEdit,
    },
    // Trader Routes
    {
        path: '/user/traders',
        name: 'admin.user.traders.index',
        component: TraderTable,
        props: {
            availableGroups: window.initialState.group.groups || [],
        },
    },
    {
        path: '/user/traders/:id',
        name: 'admin.user.traders.view',
        component: TraderView,
    },
    {
        path: '/user/users/create',
        name: 'admin.user.users.create',
        component: AllUsersForm,
        props: {
            locales,
            pageTitle: 'title.new-user',
        },
    },
    {
        path: '/user/users/:userId/edit',
        name: 'admin.user.users.edit',
        component: AllUsersForm,
        props: {
            locales,
            pageTitle: 'title.edit-user',
        },
    },
    // Role Routes
    {
        path: '/user/roles',
        name: 'admin.user.roles.index',
        component: RoleTable,
    },
    {
        path: '/user/roles/create',
        name: 'admin.user.roles.create',
        component: RoleForm,
        props: {
            locales,
            pageTitle: 'new-role',
            isNew: true,
        },
    },
    {
        path: '/user/roles/:id/edit',
        name: 'admin.user.roles.edit',
        component: RoleForm,
        props: {
            locales,
            pageTitle: 'title.edit',
            isNew: false,
        },
    },
    // Account routes
    {
        path: '/account/profile',
        name: 'admin.account.profile.edit',
        component: AccountProfile,
    },
    // Invoice routes
    {
        path: '/invoices',
        name: 'admin.invoice.index',
        component: InvoiceTable,
    },
];
