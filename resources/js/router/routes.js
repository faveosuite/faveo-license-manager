import store from '../store/index'

import LicenseLayout from '../Layouts/LicenseManagerLayout.vue';

import Dashboard from '../Pages/Dashboard.vue';

import NotFound from '../Pages/NotFound.vue';

import Login from '../Pages/Auth/Login.vue';

import ForgotPassword from '../Pages/Auth/ForgotPassword.vue';

import ResetPassword from '../Pages/Auth/ResetPassword.vue'

//===========================PRODUCTS MENU=========================

import ProductCreateEdit from '../Pages/Product/ProductCreateEdit.vue';

import ProductsIndex from '../Pages/Product/ProductsIndex.vue';

let productsMenu = {

    path: '/products',

    component: LicenseLayout,

    name: 'Products',

    redirect: '/products/list',

    beforeEnter: requireAuth,

    children: [

        {

            path: 'list',

            name: 'Products Index',

            component: ProductsIndex,

            meta: { title : 'products', crumb : { link: { name : 'dashboard', to : '/' }, active : 'products' } }
        },

        {

            path: 'create',

            name: 'Product Create',

            component: ProductCreateEdit,

            meta: { title : 'products', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'products', to : '/products' }, active : 'create' } }
        },

        {

            path: ':id/edit',

            name: 'Product Edit',

            component: ProductCreateEdit,

            meta: { title : 'products', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'products', to : '/products' }, active : 'edit' } }
        },
    ]
}

//=================================================================

//===========================CLIENTS MENU==========================

import ClientCreateEdit from '../Pages/Client/ClientCreateEdit.vue';

import ClientsIndex from '../Pages/Client/ClientsIndex.vue';

let clientsMenu = {

	path: '/clients',

	component: LicenseLayout,

	name: 'Clients',

	redirect: '/clients/list',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'list',

			name: 'Clients Index',

			component: ClientsIndex,

			meta: { title : 'Contacts', crumb : { link: { name : 'dashboard', to : '/' }, active : 'Contacts' } }
		},

		{

			path: 'create',

			name: 'Client Create',

			component: ClientCreateEdit,

			meta: { title : 'Contacts', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'Contacts', to : '/clients' }, active : 'create' } }
		},

		{

			path: ':id/edit',

			name: 'Client Edit',

			component: ClientCreateEdit,

			meta: { title : 'Contacts', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'Contacts', to : '/clients' }, active : 'edit' } }
		},
	]
}

//=================================================================

//===========================LICENSES MENU=========================

import LicenseCreateEdit from '../Pages/License/LicenseCreateEdit.vue';

import LicensesIndex from '../Pages/License/LicensesIndex.vue';

let licensesMenu = {

	path: '/licenses',

	component: LicenseLayout,

	name: 'Licenses',

	redirect: '/licenses/list',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'list',

			name: 'Licenses Index',

			component: LicensesIndex,

			meta: { title : 'licenses', crumb : { link: { name : 'dashboard', to : '/' }, active : 'licenses' } }
		},

		{

			path: 'create',

			name: 'License Create',

			component: LicenseCreateEdit,

			meta: { title : 'licenses', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'licenses', to : '/licenses' }, active : 'create' } }
		},

		{

			path: ':id/edit',

			name: 'License Edit',

			component: LicenseCreateEdit,

			meta: { title : 'licenses', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'licenses', to : '/licenses' }, active : 'edit' } }
		},
	]
}

//=================================================================

//===========================INSTALLATIONS MENU==========================

import InstallationsIndex from '../Pages/Installations/InstallationsIndex.vue';

import InstallationCreateEdit from '../Pages/Installations/InstallationCreateEdit.vue';

let installationsMenu = {

	path: '/installations',

	component: LicenseLayout,

	name: 'Installations',

	redirect: '/installations/list',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'list',

			name: 'Installations Index',

			component: InstallationsIndex,

			meta: { title : 'installations', crumb : { link: { name : 'dashboard', to : '/' }, active : 'installations' } }
		},

		{

			path: ':id/edit',

			name: 'Installation Edit',

			component: InstallationCreateEdit,

			meta: { title : 'installations', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'installations', to : '/installations' }, active : 'edit' } }
		},
	]
}

//=================================================================

//===========================CALLBACKS MENU==========================

import CallbacksIndex from '../Pages/Callbacks/CallbacksIndex.vue';

let callbacksMenu = {

	path: '/callbacks',

	component: LicenseLayout,

	name: 'Callbacks',

	redirect: '/callbacks/list',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'list',

			name: 'Callbacks Index',

			component: CallbacksIndex,

			meta: { title : 'callbacks', crumb : { link: { name : 'dashboard', to : '/' }, active : 'callbacks' } }
		},
        //
        // {
        //
        //     path: ':id/edit',
        //
        //     name: 'Callback Edit',
        //
        //     component: CallbackCreateEdit,
        //
        //     meta: { title : 'callbacks', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'callbacks', to : '/callbacks' }, active : 'edit' } }
        // },
	]
}

//=================================================================

//===========================REPORTS MENU==========================

import ViewCrackingReports from '../Pages/Report/ViewCrackingReports.vue';

import ViewLicenseReports from '../Pages/Report/ViewLicenseReports.vue';

import ViewSystemReports from '../Pages/Report/ViewSystemReports.vue';

let reportsMenu = {

	path: '/reports',

	component: LicenseLayout,

	name: 'Reports',

	redirect: '/reports',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'crack',

			name: 'View Cracking Report',

			component: ViewCrackingReports,

			meta: { title : 'reports', crumb : { link: { name : 'dashboard', to : '/' }, active : 'view_cracking_reports' } }
		},

        {

            path: 'license',

            name: 'View License Report',

            component: ViewLicenseReports,

            meta: { title : 'reports', crumb : { link: { name : 'dashboard', to : '/' }, active : 'view_license_reports' } }
        },

        {

            path: 'system',

            name: 'View System Report',

            component: ViewSystemReports,

            meta: { title : 'reports', crumb : { link: { name : 'dashboard', to : '/' }, active : 'view_system_reports' } }
        }
	]
}

//=================================================================

//===========================SERVER MENU==========================

import CustomizeNotifications from '../Pages/ServerNotifications/CustomizeNotifications.vue';

import CustomizeEmails from '../Pages/ServerNotifications/CustomizeEmails.vue';

let serverMenu = {

	path: '/server',

	component: LicenseLayout,

	name: 'Server Notifications',

	redirect: '/server/notifications',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'notifications',

			name: 'Customize Notifications',

			component: CustomizeNotifications,

			meta: { title : 'server_notify', crumb : { link: { name : 'dashboard', to : '/' }, active : 'customize_notifications' } }
		},

		{

			path: 'emails',

			name: 'Customize Emails',

			component: CustomizeEmails,

			meta: { title : 'server_notify', crumb : { link: { name : 'dashboard', to : '/' }, active : 'customize_emails' } }
		}
	]
}

//=================================================================

//===========================SETTINGS MENU=========================

import GeneralSettings from '../Pages/Settings/GeneralSettings.vue';

import SecuritySettings from '../Pages/Settings/SecuritySettings.vue';

import EmailSettings from '../Pages/Settings/EmailSettings.vue';

import SystemCleanupSettings from '../Pages/Settings/SystemCleanupSettings.vue';

import DebugSettings from '../Pages/Settings/DebugSettings.vue';

let settingsMenu = {

	path: '/settings',

	component: LicenseLayout,

	name: 'Settings',

	redirect: '/settings/general',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'general',

			name: 'General Settings',

			component: GeneralSettings,

			meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'general_settings' } }
		},

        {

            path: 'security',

            name: 'Security Settings',

            component: SecuritySettings,

            meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'security_settings' } }
        },

        {

            path: 'email',

            name: 'Email Settings',

            component: EmailSettings,

            meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'email_settings' } }
        },

        {

            path: 'cleanup',

            name: 'System Cleanup Settings',

            component: SystemCleanupSettings,

            meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'system_cleanup_settings' } }
        },
        {

            path: 'debug',

            name: 'Debug Settings',

            component: DebugSettings,

            meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'debug_settings' } }
        }

    ]
}


//===========================API KEY MENU==========================

import APIKeyCreateEdit from '../Pages/APIKey/APIKeyCreateEdit.vue';

import APIKeyIndex from '../Pages/APIKey/APIKeyIndex.vue';

let apiMenu = {

	path: '/apikeys',

	component: LicenseLayout,

	name: 'API',

	redirect: '/apikeys/list',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'list',

			name: 'API Key Index',

			component: APIKeyIndex,

			meta: { title : 'api_keys', crumb : { link: { name : 'dashboard', to : '/' }, active : 'api_keys' } }
		},

		{

			path: 'create',

			name: 'API Key Create',

			component: APIKeyCreateEdit,

			meta: { title : 'api_keys', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'api_keys', to : '/apikeys' }, active : 'create' } }
		},

		{

			path: ':id/edit',

			name: 'API Key Edit',

			component: APIKeyCreateEdit,

			meta: { title : 'api_keys', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'api_keys', to : '/apikeys' }, active : 'edit' } }
		},
	]
}

//===========================BANNED HOSTS MENU==========================

import BannedHostCreateEdit from '../Pages/BannedHost/BannedHostCreateEdit.vue';

import BannedHostsIndex from '../Pages/BannedHost/BannedHostsIndex.vue';

let bannedMenu = {

	path: '/banned-hosts',

	component: LicenseLayout,

	name: 'Banned Hosts',

	redirect: '/banned-hosts/list',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'list',

			name: 'Banned Hosts Index',

			component: BannedHostsIndex,

			meta: { title : 'banned-hosts', crumb : { link: { name : 'dashboard', to : '/' }, active : 'banned-hosts' } }
		},

		{

			path: 'create',

			name: 'Banned Host Create',

			component: BannedHostCreateEdit,

			meta: { title : 'banned-hosts', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'banned-hosts', to : '/banned-hosts' }, active : 'create' } }
		},

		{

			path: ':id/edit',

			name: 'Banned Host Edit',

			component: BannedHostCreateEdit,

			meta: { title : 'banned-hosts', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'banned-hosts', to : '/banned-hosts' }, active : 'edit' } }
		},
	]
}

//===========================WHITELIST IP ======================
import WhiteList from "../Pages/WhiteList/WhiteList.vue";
import WhiteListCreate from "../Pages/WhiteList/WhiteListCreate.vue";

let whitelistMenu = {
    path: '/Whitelist',
    component: LicenseLayout,
    name: 'Whitelist',
    redirect: '/Whitelist/list',
    beforeEnter: requireAuth,
    children: [
        {
            path: 'list',
            name: 'Whitelist',
            component: WhiteList,
            meta: { title: 'Whitelist', crumb: { link: { name: 'dashboard', to: '/' }, active: 'Whitelist' } }
        },
        {
            path: 'create',
            name: 'Whitelist Create',
            component: WhiteListCreate,
            meta: { title: 'Whitelist', crumb: { link: { name: 'dashboard', to: '/' }, root_link: { name: 'Whitelist', to: '/whitelist' }, active: 'create' } }
        },
        {
            path: ':id/edit',
            name: 'Whitelist create',
            component: WhiteListCreate,
            meta: { title: 'Whitelist', crumb: { link: { name: 'dashboard', to: '/' }, root_link: { name: 'Whitelist', to: '/whitelist' }, active: 'edit' } }
        }
    ]

}
//===========================EXTRA MENU=========================

import ConfigurationSettings from '../Pages/Extra/ConfigurationSettings.vue';

import ExceptionLogs from "../Pages/Extra/ExceptionLogs.vue";

let extraMenu = {

	path: '/tools',

	component: LicenseLayout,

	name: 'Configuration',

	redirect: '/tools/config',

	beforeEnter: requireAuth,

	children: [

		{

			path: 'config',

			name: 'Configuration Settings',

			component: ConfigurationSettings,

			meta: { title : 'configuration', crumb : { link: { name : 'dashboard', to : '/' }, active : 'configuration' } }
		},
        {

            path: 'exception',

            name: 'Exception Logs',

            component: ExceptionLogs,

            meta: { title : 'error-logs', crumb : { link: { name : 'dashboard', to : '/' }, active : 'exception' } }
        },
	]
}

//=================================================================

const routes = [

    {

		path: '/',

		component: LicenseLayout,

		redirect: '/dashboard',

		name: 'Dashboard Layout',

		beforeEnter: requireAuth,

		children: [

			{

				path: 'dashboard',

				name: 'Dashboard',

				component: Dashboard,

				meta: { title : 'dashboard', crumb : { active : 'dashboard' } }
			}
		]
	},

    productsMenu,

    clientsMenu,

    licensesMenu,

    installationsMenu,

    callbacksMenu,

    reportsMenu,

    serverMenu,

    settingsMenu,

    apiMenu,

    bannedMenu,

    extraMenu,

    whitelistMenu,

    {
        path: '/login',
        name: 'login',
        component: Login
    },

    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword
    },

    {
        path: '/reset/:id',
        name: 'reset-password',
        component: ResetPassword
    },

	{
        path: '/:pathMatch(.*)*',
		name:"404",
		component: NotFound
	}
];

function requireAuth (to, from, next) {

    if (store.getters.getUserToken) {

        next();

    } else {

        next('/login');
    }
}

export default routes;
