<?php

namespace Dorcas\ModulesAccessRequests;
use Illuminate\Support\ServiceProvider;

class ModulesAccessRequestsServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-access-requests');
		$this->publishes([
			__DIR__.'/config/modules-access-requests.php' => config_path('modules-access-requests.php'),
		], 'config');
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-access-requests')
		], 'public');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-access-requests.sub-menu'
	     );
	}

}


?>