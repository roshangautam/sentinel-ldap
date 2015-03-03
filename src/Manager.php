<?php namespace Roshangautam\Sentinel\Addons\Ldap;


use Closure;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Events\Dispatcher;
use Cartalyst\Support\Traits\EventTrait;
use Cartalyst\Sentinel\Users\UserInterface;
use Config;
use Illuminate\Exception;

class Manager {

	use EventTrait;

	/**
	 * The shared Sentinel instance.
	 *
	 * @var \Cartalyst\Sentinel\Sentinel
	 */
	protected $sentinel;


	/**
	 * Create a new Sentinel Ldap Manager instance.
	 *
	 * @param  \Cartalyst\Sentinel\Sentinel  $sentinel
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct(Sentinel $sentinel, 
		Dispatcher $dispatcher = null)
	{
		if ( ! function_exists('ldap_connect'))
		{
			throw new \Exception('LDAPauth requires the php-ldap extension to be installed.');
		}		
		$this->sentinel = $sentinel;

		if (isset($dispatcher))
		{
			$this->dispatcher = $dispatcher;
		}

	}


	/**
	 * Authenticate against the ldap. A closure may be provided
	 * for a callback upon authentication as a shortcut for subscribing
	 * to an event.
	 *
	 * @param  \Cartalyst\Sentinel\Users\UserInterface|array  $credentials
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentinel\Users\UserInterface
	 */
	public function authenticate($credentials, $remember = false)
	{

		$config = Config::get('roshangautam/sentinel-ldap::ldap');

		if($conn = $this->connect($config['host'], $config['port'])) 
		{

			$user = $this->sentinel->findByCredentials($credentials);
			
			ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

			$valid = $user !== null ? ldap_bind($conn, $user->email, $credentials['password']) : false;

			if ($valid) {
		        $this->login($user, $remember);
		        $this->disconnect($conn);
				return $user;				
			}	
			
		}
		$this->disconnect($conn);
		return false;
	}

	protected function connect($ldap_host, $ldap_port) 
	{
		if ($ldap_host && $ldap_port) return @ldap_connect($ldap_host, $ldap_port);
		return false;
	}

	protected function disconnect($conn) 
	{
		@ldap_unbind($conn);
		@ldap_close($conn);	
	}	

	public function search($query, $attr = "sn" , $email = true) 
	{
		$config = Config::get('roshangautam/sentinel-ldap::ldap');
		if($email) $query = substr($query, 0, strrpos($query, '@'));

		if($conn = $this->connect($config['host'], $config['port'])) 
		{		

			$attributes = [
				"ou", 
				"sn", 
				"cn", 
				"givenname", 
				"displayname", 
				"department", 
				"mail", 
				"userPrincipalName",
				"telephonenumber", 
				"sAMAccountName", 
				"employeeid"
			];		

			$filter = "(" . $attr . "=*" . $query . "*)";

			ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($conn, LDAP_OPT_REFERRALS, 1);

			@ldap_bind($conn, $config['search_user_dn'], $config['search_password']);

			$read = @ldap_search($conn, $config['search_base'],$filter, $attributes);
			
			$raw = ldap_get_entries($conn, $read);

			$results = $this->prepareData($raw);

			$this->disconnect($conn);

			return $results;			
		}
		return false;
	}

	/**
	 * Logs the given user into Sentinel.
	 *
	 * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 */
	protected function login(UserInterface $user, $remember = false)
	{
		return $this->sentinel->authenticate($user, $remember);
	}


	/**
	 * Register an event with Sentinel Social.
	 *
	 * @param  string   $name
	 * @param  \Closure  $callback
	 * @return void
	 * @throws \RuntimeException
	 */
	protected function registerEvent($name, Closure $callback)
	{
		$this->dispatcher->listen("sentinel.ldap.{$name}", $callback);
	}

	/**
	 * Prepares the given data in more readable format.
	 *
	 * @param  array  $raw
	 * @return mixed
	 */
	protected function prepareData($raw)
	{
		$processed = array();
		if($raw['count'] > 0) {
			for ( $i = 0 ; $i < $raw['count']; $i++) {
				for ( $j = 0 ; $j < $raw[$i]['count']; $j++) {
					$key = $raw[$i][$j];
					$value = $raw[$i][$key][0];
					$processed[$i][$key] = $value;
				}
			}
			
		}
		return $processed;		
	}

}
