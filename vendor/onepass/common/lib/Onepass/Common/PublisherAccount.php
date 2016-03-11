<?php

namespace Onepass\Common;

/**
 * Stores credentials for the 1Pass service and handles authentication.
 */
class PublisherAccount {

	use HTTPHelpers;

	/**
	 * @var $credentials array An associative array containing publishable_key and secret_key fields
	 */
	private $credentials = [];

	/**
	 * @var $credentials string The 1Pass environment of the credentials (either 'live' or 'demo')
	 */
	private $environment;

	public function __construct($credentials, $environment = 'live') {
		$this->credentials = $credentials;
		$this->environment = $environment;
	}

	public function getPublishableKey() {
		return $this->credentials['publishable_key'];
	}

	/**
	 * Build a hash we can use to authenticate with 1Pass.
	 *
	 * @return string
	 */
	public function buildHash( $unique_identifier, $ts ) {
		$publishable_key = $this->credentials['publishable_key'];
		$to_hash = compact( 'unique_identifier', 'ts', 'publishable_key' );
		ksort( $to_hash );
		$query_string = http_build_query( $to_hash );
		$hash = hash_hmac( 'sha1', $query_string, $this->credentials['secret_key'] );
		return $hash;
	}

	/**
	 * Authenticate a request from the 1Pass servers by checking the HTTP headers
	 * Accepts the $_SERVER superglobal
	 * @return bool
	 */
	public function isAuthenticRequestFrom1Pass( $server ) {
		$hash = $this->getHeaderValue('X-1PASS-SIGNATURE', $server);
		$ts = $this->getHeaderValue('X-1PASS-TIMESTAMP', $server);

		$url = $this->currentURL($server);

		return ( $hash === $this->buildHash( $url, $ts ) );
	}

	/**
	 * Returns the appropriate 1Pass domain, depending on the environment (demo or live)
	 * @return string
	 */
	public function get1PassDomain() {
		if ( $this->environment === 'live' ) {
			return 'https://1pass.me';
		} elseif ( $this->environment === 'demo' ) {
			return 'https://demo.1pass.me';
		} else {
			throw new Exception("Unknown environment: " . $this->environment);
		}
	}
}
