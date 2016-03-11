<?php

namespace Onepass\Common;

trait HTTPHelpers {

	/**
	 * Get the fully-qualified URI of the current page
	 * @return string
	 */
	function currentURL( $server ) {
		$https = ( isset( $server['SERVER_PORT'] ) && $server['SERVER_PORT'] == 443 );
		$host = isset( $server['HTTP_HOST'] ) ? $server['HTTP_HOST'] : null;
		$path = isset( $server['REQUEST_URI'] ) ? $server['REQUEST_URI'] : null;
		return "http" . ( $https ? "s://" : "://") . $host .  parse_url( $path, PHP_URL_PATH );
	}

	/**
	 * Get a value from headers
	 * @return string 
	 */
	function getHeaderValue( $header_name, $server) {
		$header_key = "HTTP_" . strtoupper(str_replace("-", "_", $header_name));
		return isset( $server[$header_key] ) ? $server[$header_key] : null;
	}
}
