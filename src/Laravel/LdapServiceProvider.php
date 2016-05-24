<?php namespace Roshangautam\Sentinel\Addons\Ldap\Laravel;

use Roshangautam\Sentinel\Addons\Ldap\Manager;

class LdapServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	protected $defer = true;

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../config/config.php' => config_path('roshangautam/sentinel-ldap.php'),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../config/config.php', 'roshangautam.sentinel-ldap'
		);
		
		$this->registerSentinelLdap();
	}


	/**
	 * Registers Sentinel LDAP.
	 *
	 * @return void
	 */
	protected function registerSentinelLdap()
	{
		$this->app['sentinel.addons.ldap'] = $this->app->share(function($app)
		{
			$manager = new Manager(
				$app['sentinel'],
				$app['events']
			);

			return $manager;
		});

		$this->app->alias('sentinel.addons.ldap', 'Roshangautam\Sentinel\Addons\Ldap\Manager');
	}

	/**
	 * {@inheritDoc}
	 */
	public function provides()
	{
		return [
			'sentinel.addons.ldap',
		];
	}

}
