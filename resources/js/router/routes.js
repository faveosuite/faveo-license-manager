import LicenseLayout from 'components/LicenseManagerLayout.vue';

import Dashboard from 'components/Views/Dashboard/Dashboard.vue';

import NotFound from 'components/Views/NotFound/NotFound.vue';

let routes = [
	
	{
		
		path: '/',
		
		component: LicenseLayout,
		
		redirect: '/dashboard',
		
		name: 'Dashboard Layout',
		
		children: [
			
			{
				
				path: 'dashboard',
				
				name: 'Dashboard',
				
				component: Dashboard,

				meta: { title : 'dashboard', crumb : { active : 'dashboard' } }
			}
		]
	},

	{
		path: '*',
		name:"404",
		component: NotFound
	}
];

export default routes;
