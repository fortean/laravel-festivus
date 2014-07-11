<?php namespace Fortean\Festivus;

use Illuminate\Support\ServiceProvider;

class FestivusServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Setup the standard package configuration
		$this->package('fortean/laravel-festivus');

		// Add a specific config namespace pointing at the override config directory
		$this->app['config']->addNamespace('laravel-festivus-cascade', app_path().'/config/packages/fortean/laravel-festivus');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('festivus', function($app)
		{
			return new Festivus();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('festivus');
	}

}
