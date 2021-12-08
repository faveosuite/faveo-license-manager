import {store} from 'store'

import LicenseLayout from 'components/LicenseManagerLayout.vue';

import Dashboard from 'components/Views/Dashboard/Dashboard.vue';

import NotFound from 'components/Views/NotFound/NotFound.vue';

import Login from 'components/Auth/Login.vue';

import Register from 'components/Auth/Register.vue';

import ForgotPassword from 'components/Auth/ForgotPassword.vue';

//===========================PRODUCTS MENU=========================

import ProductCreateEdit from 'components/Views/Product/ProductCreateEdit.vue';

import ProductsIndex from 'components/Views/Product/ProductsIndex.vue';

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

import ClientCreateEdit from 'components/Views/Client/ClientCreateEdit.vue';

import ClientsIndex from 'components/Views/Client/ClientsIndex.vue';

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
			
			meta: { title : 'clients', crumb : { link: { name : 'dashboard', to : '/' }, active : 'clients' } }
		},

		{

			path: 'create',
			
			name: 'Client Create',
			
			component: ClientCreateEdit,
			
			meta: { title : 'clients', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'clients', to : '/clients' }, active : 'create' } }
		},

		{

			path: ':id/edit',
			
			name: 'Client Edit',
			
			component: ClientCreateEdit,
			
			meta: { title : 'clients', crumb : { link: { name : 'dashboard', to : '/' }, root_link: { name : 'clients', to : '/clients' }, active : 'edit' } }
		},
	]
}

//=================================================================

//===========================LICENSES MENU=========================

import LicenseCreateEdit from 'components/Views/License/LicenseCreateEdit.vue';

import LicensesIndex from 'components/Views/License/LicensesIndex.vue';

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

import InstallationsIndex from 'components/Views/Installations/InstallationsIndex.vue';

import InstallationCreateEdit from 'components/Views/Installations/InstallationCreateEdit.vue';

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

import CallbacksIndex from 'components/Views/Callbacks/CallbacksIndex.vue';

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
		}
	]
}

//=================================================================

//===========================REPORTS MENU==========================

import ReportsIndex from 'components/Views/Report/ReportsIndex.vue';

let reportsMenu = {

	path: '/reports',
	
	component: LicenseLayout,
	
	name: 'Reports',
	
	redirect: '/reports/system',

	beforeEnter: requireAuth,
	
	children: [
		
		{

			path: ':category',
			
			name: 'Reports Index',
			
			component: ReportsIndex,
			
			meta: { title : 'reports', crumb : { link: { name : 'dashboard', to : '/' }, active : 'reports' } }
		}
	]
}

//=================================================================

//===========================SERVER MENU==========================

import CustomizeNotifications from 'components/Views/ServerNotifications/CustomizeNotifications.vue';

import CustomizeEmails from 'components/Views/ServerNotifications/CustomizeEmails.vue';

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

import SettingsIndex from 'components/Views/Settings/SettingsIndex.vue';

let settingsMenu = {

	path: '/settings',
	
	component: LicenseLayout,
	
	name: 'Settings',
	
	redirect: '/settings/general',

	beforeEnter: requireAuth,
	
	children: [
		
		{

			path: ':category',
			
			name: 'Settings Index',
			
			component: SettingsIndex,
			
			meta: { title : 'settings', crumb : { link: { name : 'dashboard', to : '/' }, active : 'settings' } }
		}
	]
}

//=================================================================

//===========================API KEY MENU==========================

import APIKeyCreateEdit from 'components/Views/APIKey/APIKeyCreateEdit.vue';

import APIKeyIndex from 'components/Views/APIKey/APIKeyIndex.vue';

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

//=================================================================

//===========================BANNED HOSTS MENU==========================

import BannedHostCreateEdit from 'components/Views/BannedHost/BannedHostCreateEdit.vue';

import BannedHostsIndex from 'components/Views/BannedHost/BannedHostsIndex.vue';

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

//=================================================================

//===========================EXTRA MENU=========================

import ConfigurationSettings from 'components/Views/Extra/ConfigurationSettings.vue';

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
		}
	]
}

//=================================================================

let routes = [
	
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

	{
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/register',
        name: 'register',
        component: Register
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword
    },

	{
		path: '*',
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
