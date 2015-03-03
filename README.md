# Sentinel LDAP Addon

[![Build Status](https://travis-ci.org/roshangautam/sentinel-ldap.svg?branch=master)](https://travis-ci.org/roshangautam/sentinel-ldap)

Sentinel LDAP is a Cartalyst `Sentinel` addon and lets you authenticate your users using LDAP (Active Directory).

The package requires PHP 5.4+ and comes bundled with a Laravel 4 Facade and a Service Provider to simplify the optional framework integration and follows the FIG standard PSR-4 to ensure a high level of interoperability between shared PHP code. The package also requires that you have [php5-ldap](http://php.net/manual/en/book.ldap.php) extension installed in your server.

## Installation

Sentinel LDAP is installable with Composer. Follow the instructions below

1. Copy the following line in your composer.json "require" section.

"roshangautam/sentinel-ldap": "dev-master",

2. Run composer update
3. Run php artisan config:publish roshangautam/sentinel-ldap
4. Open app/config/packages/roshangautam/sentinel-ldap/config.php and populate the parameters
5. Open app/config/app.php and include 
	'Roshangautam\Sentinel\Addons\Ldap\Laravel\LdapServiceProvider'
   in providers array and 
	'LDAP'          	=> 'Roshangautam\Sentinel\Addons\Ldap\Laravel\Facades\Ldap' 
   in aliases array
6. To enable LDAP login use LDAP::authenticate instead of using Sentinel::authenticate. 

Thats it. Have fun.

## Config Parameters

	'ldap' => [

		'host' => 'ldaps://yourcompany.com:3269', // This is the ldap host  preferable a Global Catalog if you have multiple ldap servers. Also prepend :port if you are using a url instead of hostname (php5-ldap requirement as it will ignore the port parameter while using URL)

		'port' => 389, // port number while using hostname instead of url

		'search_user_dn' => 'CN=Some User,OU=SomeGroup,OU=SomeGroup,DC=yourcompany,DC=com', // search user - ask your ldap adminstrator for this 

		'search_base' => 'DC=yourcompany,DC=com', // usually root of your domain

		'login_attribute' => 'sAMAccountName',  // currently unused

		'search_password' => 'yourpassword', // password for the search user - ask your ldap administrator

	],

