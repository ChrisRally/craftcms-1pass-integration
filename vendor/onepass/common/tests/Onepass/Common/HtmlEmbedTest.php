<?php

namespace Onepass\Common;

use PHPUnit_Framework_TestCase;

class HtmlEmbedTest extends PHPUnit_Framework_TestCase {

	public function testGetButtonTag() {
		$embed = $this->getEmbedObject();
		$idealOutput = file_get_contents( dirname( __FILE__ ) . '/TestExamples/GoodButtonTag.html' );

		$article = new Article( [
			'url'       	=> 'http://example.com',
			'unique_id' 	=> '123:abc',
			'title' 		=> 'Example Article',
			'author'		=> 'Joe Bloggs',
			'published'		=> '1989-01-01T00:00:00Z',
			'last_modified' => '1996-01-01T00:00:00Z',
			'description'	=> 'A short description of the article'
		] );
		$actualOutput = $embed->getButtonTag($article, 12345);

		$this->assertEquals( $idealOutput, $actualOutput );
	}

	public function testGetJavaScriptTag() {
		$embed = $this->getEmbedObject();
		$idealOutput = file_get_contents( dirname( __FILE__ ) . '/TestExamples/GoodJavaScriptTag.html' );

		$actualOutput = $embed->getJavaScriptTag();

		$this->assertEquals( $idealOutput, $actualOutput );
	}

	private function getEmbedObject() {
		$publisher_account 	= new PublisherAccount( [ "publishable_key"=>"abcde", "secret_key"=>"xyz" ], 'demo' );
		return new HtmlEmbed( $publisher_account );
	}
}
