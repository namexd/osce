<?php namespace Modules\Osce\Providers;

use Illuminate\Support\ServiceProvider;

class OsceServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot the application events.
	 * 
	 * @return void
	 */
	public function boot()
	{
		$this->registerConfig();
		$this->registerTranslations();
		$this->registerViews();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{		
		//
	}

	/**
	 * Register config.
	 * 
	 * @return void
	 */
	protected function registerConfig()
	{
		$this->publishes([
		    __DIR__.'/../Config/config.php' => config_path('osce.php'),
		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'osce'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('resources/views/modules/osce');

		$sourcePath = __DIR__.'/../Resources/views';

		//todo:
		// ! do not copy view to /Resources/views
		// limingyao 2015-11-21
		//


/*		$this->publishes([
			$sourcePath => $viewPath
		]);*/

		$this->loadViewsFrom([$viewPath, $sourcePath], 'osce');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/osce');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'osce');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'osce');
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
