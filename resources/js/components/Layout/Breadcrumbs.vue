<template>
	
	<div class="content-header">
				
		<div class="container-fluid">
			
			<div class="row mb-2">
				
				<div class="col-sm-6">
					
					<h1 class="m-0">{{trans(getTitle())}}</h1>
				</div>
				
				<div v-if="$route.meta.crumb" class="col-sm-6">
					
					<ol class="breadcrumb float-sm-right">
						
						<li><i class="fas fa-home text-gray"></i>&nbsp;&nbsp;</li>

						<li v-if="$route.meta.crumb.link" class="breadcrumb-item">

							<router-link :to="$route.meta.crumb.link.to">{{trans($route.meta.crumb.link.name)}}</router-link>
						</li>
						
						<li v-if="$route.meta.crumb.root_link" class="breadcrumb-item">

							<router-link :to="$route.meta.crumb.root_link.to">{{trans($route.meta.crumb.root_link.name)}}</router-link>
						</li>

						<li v-if="$route.meta.crumb.root" class="breadcrumb-item active">{{trans($route.meta.crumb.root)}}</li>

						<li v-if="$route.meta.crumb.active" class="breadcrumb-item active">{{trans(getActive($route.meta.crumb.active))}}</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	
	export default {

		name : 'bread-crumbs',

		methods : {

			getTitle() {

				return this.$route.meta.title;
			},

			getActive(value) {

				return value
		
			},
		}
	};
</script>