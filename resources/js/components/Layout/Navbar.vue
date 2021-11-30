<template>

	<nav class="main-header navbar navbar-expand navbar-white navbar-light">
		
		<ul class="navbar-nav">
	  		
	  		<li class="nav-item">
				
				<a class="nav-link" data-widget="pushmenu" href="javascript:;" role="button"><i class="fas fa-bars"></i></a>
	  		</li>
	  		
	  		<li class="nav-item d-none d-sm-inline-block">
				
				<a href="index3.html" class="nav-link">Home</a>
	  		</li>
	  		
		</ul>

		<ul class="navbar-nav ml-auto">
	 
			<li class="nav-item dropdown user-menu">
        		
        		<a href="javascript:;" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
          			
          			<img :src="basePath()+'/themes/default/img/user2-160x160.jpg'" class="user-image img-circle elevation-2" alt="User Image">
          			
          			<span class="d-none d-md-inline">Alexander Pierce</span>
        		</a>
        		
        		<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          			
          			<li class="user-header bg-white">
            		
            			<img :src="basePath()+'/themes/default/img/user2-160x160.jpg'" class="img-circle elevation-2" alt="User Image">

            			<p>Alexander Pierce<small>Admin</small></p>
          			</li>
          			
          			<li class="user-footer">
            			
            			<a href="javascript:;" class="btn btn-primary btn-flat">Profile</a>
            			
            			<a href="javascript:;" class="btn btn-danger btn-flat float-right">Sign out</a>
          			</li>
        		</ul>
      		</li>
		</ul>
  	</nav>
</template>

<script>
	
	export default {

		name : 'nav-bar'
	}
</script>

