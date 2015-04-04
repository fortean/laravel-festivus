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
		// Publish a demo service description
		$this->publishes([
			__DIR__.'/../../config/httpbin.php' => config_path('festivus/httpbin.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Bind Festivus as a singleton
		$this->app->singleton('Festivus', function($app)
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
		return ['Festivus'];
	}

}
