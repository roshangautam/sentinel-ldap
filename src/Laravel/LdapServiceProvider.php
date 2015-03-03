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
		$this->package('roshanguatam/sentinel-ldap', 'roshangautam/sentinel-ldap', __DIR__.'/..');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{

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
