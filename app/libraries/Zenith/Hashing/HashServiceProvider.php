<?php namespace Zenith\Hashing;

use Config;
use Illuminate\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->bindShared('hash', function() {
			switch (Config::get('zenith.encryption_type')) {
				case 'md5': return new MD5Hasher;
				case 'sha1': return new SHA1Hasher;
			}
			return new PlainHasher;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('hash');
	}
}
