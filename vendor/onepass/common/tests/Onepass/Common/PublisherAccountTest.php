<?php

namespace Onepass\Common;

use PHPUnit_Framework_TestCase;

class PublisherAccountTest extends PHPUnit_Framework_TestCase {

	private $credentials, $publisherAccount;

	public function setUp() {
		$this->credentials = [ "publishable_key" => "abcde", "secret_key" => "xyz" ];
		$this->publisherAccount = new PublisherAccount($this->credentials);
	}

	public function testHashBuilding() {
		$ts = '1455367796';
		$uniqueIdentifier = 'urn:mysite.com:abc123';

		$hash = $this->publisherAccount->buildHash($uniqueIdentifier, $ts);

		# The expected hash value is calculated using another implementation of the algorithm
		$this->assertEquals("14e8e490015a46b9a561f1197a632f841fbcb8b3", $hash);
	}

	function testOnepassAuthenticateValid() {
		$url = "https://example.com/feed/atom/1pass/";

		$timestamp = time();
		$hash = $this->publisherAccount->buildHash( $url, $timestamp );

		$request_params = [
			'HTTP_X_1PASS_SIGNATURE' => $hash,
			'HTTP_X_1PASS_TIMESTAMP' => $timestamp,
			'SERVER_PORT' => 443,
			'HTTP_HOST' => 'example.com',
			'REQUEST_URI' => '/feed/atom/1pass/',
		];

		$this->assertTrue( $this->publisherAccount->isAuthenticRequestFrom1Pass( $request_params ) );
	}

	function testOnepassAuthenticateInvalid() {
		$request_params = [
			'HTTP_X_1PASS_SIGNATURE' => 'invalid',
			'HTTP_X_1PASS_TIMESTAMP' => 1234,
			'SERVER_PORT' => 443,
			'HTTP_HOST' => 'example.com',
			'REQUEST_URI' => '/feed/atom/1pass'
		];
		$this->assertFalse( $this->publisherAccount->isAuthenticRequestFrom1Pass( $request_params ) );
	}

	function testOnepassHostResolution() {
		$this->assertEquals('https://1pass.me', (new PublisherAccount($this->credentials))->get1PassDomain());
		$this->assertEquals('https://1pass.me', (new PublisherAccount($this->credentials, 'live'))->get1PassDomain());
		$this->assertEquals('https://demo.1pass.me', (new PublisherAccount($this->credentials, 'demo'))->get1PassDomain());
	}
}
