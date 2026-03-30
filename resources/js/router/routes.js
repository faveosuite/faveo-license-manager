import store from '../store/index'

import LicenseLayout from '../Layouts/LicenseManagerLayout.vue';

import Dashboard from '../Pages/Dashboard.vue';

import NotFound from '../Pages/NotFound.vue';

import Login from '../Pages/Auth/Login.vue';

import Verify2FA from "../Pages/Auth/Verify2FA.vue";

import ForgotPassword from '../Pages/Auth/ForgotPassword.vue';

import ResetPassword from '../Pages/Auth/ResetPassword.vue'

//===========================PRODUCTS MENU=========================

import ProductCreateEdit from '../Pages/Product/ProductCreateEdit.vue';

import ProductsIndex from '../Pages/Product/ProductsIndex.vue';

import ProductsSuspended from "../Pages/Product/ProductsSuspended.vue";

import ProductsView from "../Pages/Product/ProductsView.vue";

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

            path: 'suspended',

            name: 'Product Suspended',

            component: ProductsSuspended,

            meta: { title : 'products', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'products', to : '/products' }, active : 'create' } }
        },

        {

            path: ':id/edit',

            name: 'Product Edit',

            component: ProductCreateEdit,

            meta: { title : 'products', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'products', to : '/products' }, active : 'edit' } }
        },

        {

            path: ':id/view',

            name: 'Product View',

            component: ProductsView,

            meta: { title : 'products', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'products', to : '/products' }, active : 'view' } }
        },
    ]
}

//=================================================================

//===========================VERSIONS MENU=========================

import VersionCreateEdit from "../Pages/Version/VersionCreateEdit.vue";

import VersionsIndex from "../Pages/Version/VersionsIndex.vue";

import VersionsView from "../Pages/Version/VersionsView.vue";

let versionsMenu = {

    path: '/versions',

    component: LicenseLayout,

    name: 'Versions',

    redirect: '/versions/list',

    beforeEnter: requireAuth,

    children: [

        {

            path: 'list',

            name: 'Versions Index',

            component: VersionsIndex,

            meta: { title : 'versions', crumb : { link: { name : 'dashboard', to : '/' }, active : 'versions' } }
        },

        {

            path: 'create',

            name: 'Version Create',

            component: VersionCreateEdit,

            meta: { title : 'versions', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'versions', to : '/versions' }, active : 'create' } }
        },

        {

            path: ':id/edit',

            name: 'Version Edit',

            component: VersionCreateEdit,

            meta: { title : 'version', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'version', to : '/versions' }, active : 'edit' } }
        },

        {

            path: ':id/view',

            name: 'Versions View',

            component: VersionsView,

            meta: { title : 'versions', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'versions', to : '/versions' }, active : 'view' } }
        },

    ]
}

//=================================================================

//===========================PROFILE MENU=========================

import Profile from "../Pages/UserProfile/Profile.vue";

let profileMenu = {

    path: '/profile',

    component: LicenseLayout,

    name: 'Profile',

    redirect: '/profile/edit',

    beforeEnter: requireAuth,

    children: [

        {

            path: 'edit',

            name: 'Profile Edit',

            component: Profile,

            meta: { title : 'View Profile', crumb : { link: { name : 'dashboard', to : '/' }, active : 'my_profile' } }
        },

    ]
}

//===========================CLIENTS MENU==========================

import ClientCreateEdit from '../Pages/Client/ClientCreateEdit.vue';

import ClientsIndex from '../Pages/Client/ClientsIndex.vue';

import ClientsView from "../Pages/Client/ClientsView.vue";

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

        {

            path: ':id/view',

            name: 'Client View',

            component: ClientsView,

            meta: { title : 'Contacts', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'Contacts', to : '/clients' }, active : 'view' } }
        },
	]
}

//=================================================================

//===========================LICENSES MENU=========================

import LicenseCreateEdit from '../Pages/License/LicenseCreateEdit.vue';

import LicensesIndex from '../Pages/License/LicensesIndex.vue';

import LicensesView from "../Pages/License/LicensesView.vue";

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

        {

            path: ':id/view',

            name: 'License View',

            component: LicensesView,

            meta: { title : 'licenses', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'licenses', to : '/licenses' }, active : 'view' } }
        },
	]
}

//=================================================================

//===========================INSTALLATIONS MENU==========================

import InstallationsIndex from '../Pages/Installations/InstallationsIndex.vue';

import InstallationCreateEdit from '../Pages/Installations/InstallationCreateEdit.vue';

import InstallationsView from "../Pages/Installations/InstallationsView.vue";

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

        {

            path: ':id/view',

            name: 'Installation View',

            component: InstallationsView,

            meta: { title : 'installations', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'installations', to : '/installations' }, active : 'view' } }
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

import ViewUpdateReports from "../Pages/Report/ViewUpdateReports.vue";

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

            path: 'update',

            name: 'View Update Report',

            component: ViewUpdateReports,

            meta: { title : 'reports', crumb : { link: { name : 'dashboard', to : '/' }, active : 'view_update_reports' } }
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

import RedisStreamSettings from '../Pages/Settings/RedisStreamSettings.vue';

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
        },
        {

            path: 'redis-stream',

            name: 'Redis Stream Settings',

            component: RedisStreamSettings,

            meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'redis_stream_settings' } }
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
            name: 'Whitelist Index',
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
            name: 'Whitelist Edit',
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

            meta: { title : 'exception_logs', crumb : { link: { name : 'dashboard', to : '/' }, active : 'exception' } }
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

    versionsMenu,

    clientsMenu,

    licensesMenu,

    installationsMenu,

    callbacksMenu,

    reportsMenu,

    serverMenu,

    profileMenu,

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
        path: '/verify-2fa/:pp',
        name: 'Verify2FA',
        props: true,
        component: Verify2FA
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
